<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains the definition for the grading table which subclassses easy_table
 *
 * @package   mod_edusign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->dirroot . '/mod/edusign/locallib.php');

/**
 * Extends table_sql to provide a table of edusignment submissions
 *
 * @package   mod_edusign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edusign_grading_table extends table_sql implements renderable {
    /** @var edusign $edusignment */
    private $edusignment = null;
    /** @var int $perpage */
    private $perpage = 10;
    /** @var int $rownum (global index of current row in table) */
    private $rownum = -1;
    /** @var renderer_base for getting output */
    private $output = null;
    /** @var stdClass gradinginfo */
    private $gradinginfo = null;
    /** @var int $tablemaxrows */
    private $tablemaxrows = 10000;
    /** @var boolean $quickgrading */
    private $quickgrading = false;
    /** @var boolean $hasgrantextension - Only do the capability check once for the entire table */
    private $hasgrantextension = false;
    /** @var boolean $hasgrade - Only do the capability check once for the entire table */
    private $hasgrade = false;
    /** @var array $groupsubmissions - A static cache of group submissions */
    private $groupsubmissions = array();
    /** @var array $submissiongroups - A static cache of submission groups */
    private $submissiongroups = array();
    /** @var string $plugingradingbatchoperations - List of plugin supported batch operations */
    public $plugingradingbatchoperations = array();
    /** @var array $plugincache - A cache of plugin lookups to match a column name to a plugin efficiently */
    private $plugincache = array();
    /** @var array $scale - A list of the keys and descriptions for the custom scale */
    private $scale = null;

    /**
     * overridden constructor keeps a reference to the edusignment class that is displaying this table
     *
     * @param edusign $edusignment The edusignment class
     * @param int $perpage how many per page
     * @param string $filter The current filter
     * @param int $rowoffset For showing a subsequent page of results
     * @param bool $quickgrading Is this table wrapped in a quickgrading form?
     * @param string $downloadfilename
     */
    public function __construct(
        edusign $edusignment,
        $perpage,
        $filter,
        $rowoffset,
        $quickgrading,
        $downloadfilename = null
    ) {
        global $CFG, $PAGE, $DB, $USER;
        parent::__construct('mod_edusign_grading');
        $this->is_persistent(true);
        $this->edusignment = $edusignment;

        // Check permissions up front.
        $this->hasgrantextension = has_capability(
            'mod/assign:grantextension',
            $this->edusignment->get_context()
        );
        $this->hasgrade = $this->edusignment->can_grade();

        // Check if we have the elevated view capablities to see the blind details.
        $this->hasviewblind = has_capability(
            'mod/assign:viewblinddetails',
            $this->edusignment->get_context()
        );

        foreach ($edusignment->get_feedback_plugins() as $plugin) {
            if ($plugin->is_visible() && $plugin->is_enabled()) {
                foreach ($plugin->get_grading_batch_operations() as $action => $description) {
                    if (empty($this->plugingradingbatchoperations)) {
                        $this->plugingradingbatchoperations[$plugin->get_type()] = array();
                    }
                    $this->plugingradingbatchoperations[$plugin->get_type()][$action] = $description;
                }
            }
        }
        $this->perpage = $perpage;
        $this->quickgrading = $quickgrading && $this->hasgrade;
        $this->output = $PAGE->get_renderer('mod_edusign');

        $urlparams = array('action' => 'grading', 'id' => $edusignment->get_course_module()->id);
        $url = new moodle_url($CFG->wwwroot . '/mod/edusign/view.php', $urlparams);
        $this->define_baseurl($url);

        // Do some business - then set the sql.
        $currentgroup = groups_get_activity_group($edusignment->get_course_module(), true);

        if ($rowoffset) {
            $this->rownum = $rowoffset - 1;
        }

        $users = array_keys($edusignment->list_participants($currentgroup, true));
        if (count($users) == 0) {
            // Insert a record that will never match to the sql is still valid.
            $users[] = -1;
        }

        $params = array();
        $params['edusignmentid1'] = (int) $this->edusignment->get_instance()->id;
        $params['edusignmentid2'] = (int) $this->edusignment->get_instance()->id;
        $params['edusignmentid3'] = (int) $this->edusignment->get_instance()->id;
        $params['newstatus'] = EDUSIGN_SUBMISSION_STATUS_NEW;

        $extrauserfields = \core_user\fields::for_userpic()->get_sql('u', true);
        $extrauserfield_columns = ['firstname', 'lastname'];

        $fields = 'u.id';
        $fields .= $extrauserfields->selects.', ';
        $fields .= 'u.id as userid, ';
        $fields .= 's.status as status, ';
        $fields .= 's.id as submissionid, ';
        $fields .= 's.timecreated as firstsubmission, ';
        $fields .= "CASE WHEN status <> :newstatus THEN s.timemodified ELSE NULL END as timesubmitted, ";
        $fields .= 's.attemptnumber as attemptnumber, ';
        $fields .= 'g.id as gradeid, ';
        $fields .= 'g.grade as grade, ';
        $fields .= 'g.timemodified as timemarked, ';
        $fields .= 'g.timecreated as firstmarked, ';
        $fields .= 'uf.mailed as mailed, ';
        $fields .= 'uf.locked as locked, ';
        $fields .= 'uf.extensionduedate as extensionduedate, ';
        $fields .= 'uf.workflowstate as workflowstate, ';
        $fields .= 'uf.allocatedmarker as allocatedmarker';

        $from = '{user} u
                         LEFT JOIN {edusign_submission} s
                                ON u.id = s.userid
                               AND s.edusignment = :edusignmentid1
                               AND s.latest = 1 ';

        // For group edusignments, there can be a grade with no submission.
        $from .= ' LEFT JOIN {edusign_grades} g
                            ON g.edusignment = :edusignmentid2
                           AND u.id = g.userid
                           AND (g.attemptnumber = s.attemptnumber OR s.attemptnumber IS NULL) ';

        $from .= 'LEFT JOIN {edusign_user_flags} uf
                         ON u.id = uf.userid
                        AND uf.edusignment = :edusignmentid3 ';

        $from .= $extrauserfields->joins;
        $params += $extrauserfields->params;

        $hasoverrides = $this->edusignment->has_overrides();

        if ($hasoverrides) {
            $params['edusignmentid5'] = (int) $this->edusignment->get_instance()->id;
            $params['edusignmentid6'] = (int) $this->edusignment->get_instance()->id;
            $params['edusignmentid7'] = (int) $this->edusignment->get_instance()->id;
            $params['edusignmentid8'] = (int) $this->edusignment->get_instance()->id;
            $params['edusignmentid9'] = (int) $this->edusignment->get_instance()->id;

            $fields .= ', priority.priority, ';
            $fields .= 'effective.allowsubmissionsfromdate, ';
            $fields .= 'effective.duedate, ';
            $fields .= 'effective.cutoffdate ';

            $from .= ' LEFT JOIN (
               SELECT merged.userid, min(merged.priority) priority FROM (
                  ( SELECT u.id as userid, 9999999 AS priority
                      FROM {user} u
                  )
                  UNION
                  ( SELECT uo.userid, 0 AS priority
                      FROM {edusign_overrides} uo
                     WHERE uo.edusignid = :edusignmentid5
                  )
                  UNION
                  ( SELECT gm.userid, go.sortorder AS priority
                      FROM {edusign_overrides} go
                      JOIN {groups} g ON g.id = go.groupid
                      JOIN {groups_members} gm ON gm.groupid = g.id
                     WHERE go.edusignid = :edusignmentid6
                  )
                ) merged
                GROUP BY merged.userid
              ) priority ON priority.userid = u.id

            JOIN (
              (SELECT 9999999 AS priority,
                      u.id AS userid,
                      a.allowsubmissionsfromdate,
                      a.duedate,
                      a.cutoffdate
                 FROM {user} u
                 JOIN {edusign} a ON a.id = :edusignmentid7
              )
              UNION
              (SELECT 0 AS priority,
                      uo.userid,
                      uo.allowsubmissionsfromdate,
                      uo.duedate,
                      uo.cutoffdate
                 FROM {edusign_overrides} uo
                WHERE uo.edusignid = :edusignmentid8
              )
              UNION
              (SELECT go.sortorder AS priority,
                      gm.userid,
                      go.allowsubmissionsfromdate,
                      go.duedate,
                      go.cutoffdate
                 FROM {edusign_overrides} go
                 JOIN {groups} g ON g.id = go.groupid
                 JOIN {groups_members} gm ON gm.groupid = g.id
                WHERE go.edusignid = :edusignmentid9
              )

            ) effective ON effective.priority = priority.priority AND effective.userid = priority.userid ';
        }

        if (!empty($this->edusignment->get_instance()->blindmarking)) {
            $from .= 'LEFT JOIN {edusign_user_mapping} um
                             ON u.id = um.userid
                            AND um.edusignment = :edusignmentidblind ';
            $params['edusignmentidblind'] = (int) $this->edusignment->get_instance()->id;
            $fields .= ', um.id as recordid ';
        }

        $userparams = array();
        $userindex = 0;

        list($userwhere, $userparams) = $DB->get_in_or_equal($users, SQL_PARAMS_NAMED, 'user');
        $where = 'u.id ' . $userwhere;
        $params = array_merge($params, $userparams);

        // The filters do not make sense when there are no submissions, so do not apply them.
        if ($this->edusignment->is_any_submission_plugin_enabled()) {
            if ($filter == EDUSIGN_FILTER_SUBMITTED) {
                $where .= ' AND (s.timemodified IS NOT NULL AND
                                 s.status = :submitted) ';
                $params['submitted'] = EDUSIGN_SUBMISSION_STATUS_SUBMITTED;
            } else if ($filter == EDUSIGN_FILTER_NOT_SUBMITTED) {
                $where .= ' AND (s.timemodified IS NULL OR s.status <> :submitted) ';
                $params['submitted'] = EDUSIGN_SUBMISSION_STATUS_SUBMITTED;
            } else if ($filter == EDUSIGN_FILTER_REQUIRE_GRADING) {
                $where .= ' AND (s.timemodified IS NOT NULL AND
                                 s.status = :submitted AND
                                 (s.timemodified >= g.timemodified OR g.timemodified IS NULL OR g.grade IS NULL';

                // edusignment grade is set to the negative grade scale id when scales are used.
                if ($this->edusignment->get_instance()->grade < 0) {
                    // Scale grades are set to -1 when not graded.
                    $where .= ' OR g.grade = -1';
                }

                $where .= '))';
                $params['submitted'] = EDUSIGN_SUBMISSION_STATUS_SUBMITTED;
            } else if ($filter == EDUSIGN_FILTER_GRANTED_EXTENSION) {
                $where .= ' AND uf.extensionduedate > 0 ';
            } else if (strpos($filter, EDUSIGN_FILTER_SINGLE_USER) === 0) {
                $userfilter = (int) array_pop(explode('=', $filter));
                $where .= ' AND (u.id = :userid)';
                $params['userid'] = $userfilter;
            }
        }

        if ($this->edusignment->get_instance()->markingworkflow &&
            $this->edusignment->get_instance()->markingallocation) {
            if (has_capability('mod/assign:manageallocations', $this->edusignment->get_context())) {
                // Check to see if marker filter is set.
                $markerfilter = (int) get_user_preferences('edusign_markerfilter', '');
                if (!empty($markerfilter)) {
                    if ($markerfilter == EDUSIGN_MARKER_FILTER_NO_MARKER) {
                        $where .= ' AND (uf.allocatedmarker IS NULL OR uf.allocatedmarker = 0)';
                    } else {
                        $where .= ' AND uf.allocatedmarker = :markerid';
                        $params['markerid'] = $markerfilter;
                    }
                }
            }
        }

        if ($this->edusignment->get_instance()->markingworkflow) {
            $workflowstates = $this->edusignment->get_marking_workflow_states_for_current_user();
            if (!empty($workflowstates)) {
                $workflowfilter = get_user_preferences('edusign_workflowfilter', '');
                if ($workflowfilter == EDUSIGN_MARKING_WORKFLOW_STATE_NOTMARKED) {
                    $where .= ' AND (uf.workflowstate = :workflowstate OR uf.workflowstate IS NULL OR ' .
                        $DB->sql_isempty('edusign_user_flags', 'workflowstate', true, true) . ')';
                    $params['workflowstate'] = $workflowfilter;
                } else if (array_key_exists($workflowfilter, $workflowstates)) {
                    $where .= ' AND uf.workflowstate = :workflowstate';
                    $params['workflowstate'] = $workflowfilter;
                }
            }
        }

        $this->set_sql($fields, $from, $where, $params);

        if ($downloadfilename) {
            $this->is_downloading('csv', $downloadfilename);
        }

        $columns = array();
        $headers = array();

        // Select.
        /* if (!$this->is_downloading() && $this->hasgrade) {
            $columns[] = 'select';
            $headers[] = get_string('select') .
                '<div class="selectall"><label class="accesshide" for="selectall">' . get_string('selectall') . '</label>
                    <input type="checkbox" id="selectall" name="selectall" title="' . get_string('selectall') . '"/></div>';
        }*/

        // User picture.
        if ($this->hasviewblind || !$this->edusignment->is_blind_marking()) {
            if (!$this->is_downloading()) {
                $columns[] = 'picture';
                $headers[] = get_string('pictureofuser');
            } else {
                $columns[] = 'recordid';
                $headers[] = get_string('recordid', 'edusign');
            }

            // Fullname.
            $columns[] = 'fullname';
            $headers[] = get_string('fullname');

            // Participant # details if can view real identities.
            if ($this->edusignment->is_blind_marking()) {
                if (!$this->is_downloading()) {
                    $columns[] = 'recordid';
                    $headers[] = get_string('recordid', 'edusign');
                }
            }

            foreach ($extrauserfield_columns as $extrafield) {
                $columns[] = $extrafield;
                $headers[] = \core_user\fields::get_display_name($extrafield);
            }
        } else {
            // Record ID.
            $columns[] = 'recordid';
            $headers[] = get_string('recordid', 'edusign');
        }

        // Submission status.
        $columns[] = 'status';
        $headers[] = get_string('status', 'edusign');

        if ($hasoverrides) {
            // Allowsubmissionsfromdate.
            $columns[] = 'allowsubmissionsfromdate';
            $headers[] = get_string('allowsubmissionsfromdate', 'edusign');

            // Duedate.
            $columns[] = 'duedate';
            $headers[] = get_string('duedate', 'edusign');

            // Cutoffdate.
            $columns[] = 'cutoffdate';
            $headers[] = get_string('cutoffdate', 'edusign');
        }

        // Team submission columns.
        if ($edusignment->get_instance()->teamsubmission) {
            $columns[] = 'team';
            $headers[] = get_string('submissionteam', 'edusign');
        }
        // Allocated marker.
        if ($this->edusignment->get_instance()->markingworkflow &&
            $this->edusignment->get_instance()->markingallocation &&
            has_capability('mod/assign:manageallocations', $this->edusignment->get_context())) {
            // Add a column for the allocated marker.
            $columns[] = 'allocatedmarker';
            $headers[] = get_string('marker', 'edusign');
        }
        // Grade.
        /*$columns[] = 'grade';
        $headers[] = get_string('grade');
        if ($this->is_downloading()) {
            $gradetype = $this->edusignment->get_instance()->grade;
            if ($gradetype > 0) {
                $columns[] = 'grademax';
                $headers[] = get_string('maxgrade', 'edusign');
            } else if ($gradetype < 0) {
                // This is a custom scale.
                $columns[] = 'scale';
                $headers[] = get_string('scale', 'edusign');
            }

            if ($this->edusignment->get_instance()->markingworkflow) {
                // Add a column for the marking workflow state.
                $columns[] = 'workflowstate';
                $headers[] = get_string('markingworkflowstate', 'edusign');
            }
            // Add a column to show if this grade can be changed.
            $columns[] = 'gradecanbechanged';
            $headers[] = get_string('gradecanbechanged', 'edusign');
        }
        if (!$this->is_downloading() && $this->hasgrade) {
            // We have to call this column userid so we can use userid as a default sortable column.
            $columns[] = 'userid';
            $headers[] = get_string('edit');
        }
        */
        // Submission plugins.
        if ($edusignment->is_any_submission_plugin_enabled()) {
            $columns[] = 'timesubmitted';
            $headers[] = get_string('lastmodifiedsubmission', 'edusign');

            foreach ($this->edusignment->get_submission_plugins() as $plugin) {
                if ($this->is_downloading()) {
                    if ($plugin->is_visible() && $plugin->is_enabled()) {
                        foreach ($plugin->get_editor_fields() as $field => $description) {
                            $index = 'plugin' . count($this->plugincache);
                            $this->plugincache[$index] = array($plugin, $field);
                            $columns[] = $index;
                            $headers[] = $plugin->get_name();
                        }
                    }
                } else {
                    if ($plugin->is_visible() && $plugin->is_enabled() && $plugin->has_user_summary()) {
                        $index = 'plugin' . count($this->plugincache);
                        $this->plugincache[$index] = array($plugin);
                        $columns[] = $index;
                        $headers[] = $plugin->get_name();
                    }
                }
            }
        }


        if ($edusignment->is_any_submission_plugin_enabled()) {
            $columns[] = 'delete';
            $headers[] = get_string('delete', 'edusign');
        }




        // Time marked.
        /*
        $columns[] = 'timemarked';
        $headers[] = get_string('lastmodifiedgrade', 'edusign');
        */
        // Feedback plugins.
        /*foreach ($this->edusignment->get_feedback_plugins() as $plugin) {
            if ($this->is_downloading()) {
                if ($plugin->is_visible() && $plugin->is_enabled()) {
                    foreach ($plugin->get_editor_fields() as $field => $description) {
                        $index = 'plugin' . count($this->plugincache);
                        $this->plugincache[$index] = array($plugin, $field);
                        $columns[] = $index;
                        $headers[] = $description;
                    }
                }
            } else if ($plugin->is_visible() && $plugin->is_enabled() && $plugin->has_user_summary()) {
                $index = 'plugin' . count($this->plugincache);
                $this->plugincache[$index] = array($plugin);
                $columns[] = $index;
                $headers[] = $plugin->get_name();
            }*/

        // Exclude 'Final grade' column in downloaded grading worksheets.
        /*if (!$this->is_downloading()) {
            // Final grade.
            $columns[] = 'finalgrade';
            $headers[] = get_string('finalgrade', 'grades');
        }*/

        // Load the grading info for all users.
        $this->gradinginfo = grade_get_grades(
            $this->edusignment->get_course()->id,
            'mod',
            'edusign',
            $this->edusignment->get_instance()->id,
            $users
        );

        if (!empty($CFG->enableoutcomes) && !empty($this->gradinginfo->outcomes)) {
            $columns[] = 'outcomes';
            $headers[] = get_string('outcomes', 'grades');
        }

        // Set the columns.
        $this->define_columns($columns);
        $this->define_headers($headers);
        foreach ($extrauserfield_columns as $extrafield) {
            $this->column_class($extrafield, $extrafield);
        }
        $this->no_sorting('recordid');
        $this->no_sorting('finalgrade');
        $this->no_sorting('userid');
        $this->no_sorting('select');
        $this->no_sorting('outcomes');
        $this->no_sorting('delete');

        if ($edusignment->get_instance()->teamsubmission) {
            $this->no_sorting('team');
        }

        $plugincolumnindex = 0;
        foreach ($this->edusignment->get_submission_plugins() as $plugin) {
            if ($plugin->is_visible() && $plugin->is_enabled() && $plugin->has_user_summary()) {
                $submissionpluginindex = 'plugin' . $plugincolumnindex++;
                $this->no_sorting($submissionpluginindex);
            }
        }
        foreach ($this->edusignment->get_feedback_plugins() as $plugin) {
            if ($plugin->is_visible() && $plugin->is_enabled() && $plugin->has_user_summary()) {
                $feedbackpluginindex = 'plugin' . $plugincolumnindex++;
                $this->no_sorting($feedbackpluginindex);
            }
        }

        // When there is no data we still want the column headers printed in the csv file.
        if ($this->is_downloading()) {
            $this->start_output();
        }
    }

    /**
     * Before adding each row to the table make sure rownum is incremented.
     *
     * @param array $row row of data from db used to make one row of the table.
     * @return array one row for the table
     */
    public function format_row($row) {
        if ($this->rownum < 0) {
            $this->rownum = $this->currpage * $this->pagesize;
        } else {
            $this->rownum += 1;
        }

        return parent::format_row($row);
    }

    /**
     * Add a column with an ID that uniquely identifies this user in this edusignment.
     *
     * @param stdClass $row
     * @return string
     */
    public function col_recordid(stdClass $row) {
        if (empty($row->recordid)) {
            $row->recordid = $this->edusignment->get_uniqueid_for_user($row->userid);
        }
        return get_string('hiddenuser', 'edusign') . $row->recordid;
    }

    /**
     * Add the userid to the row class so it can be updated via ajax.
     *
     * @param stdClass $row The row of data
     * @return string The row class
     */
    public function get_row_class($row) {
        return 'user' . $row->userid;
    }

    /**
     * Return the number of rows to display on a single page.
     *
     * @return int The number of rows per page
     */
    public function get_rows_per_page() {
        return $this->perpage;
    }

    /**
     * list current marking workflow state
     *
     * @param stdClass $row
     * @return string
     */
    public function col_workflowstatus(stdClass $row) {
        $o = '';

        $gradingdisabled = $this->edusignment->grading_disabled($row->id);
        // The function in the edusignment keeps a static cache of this list of states.
        $workflowstates = $this->edusignment->get_marking_workflow_states_for_current_user();
        $workflowstate = $row->workflowstate;
        if (empty($workflowstate)) {
            $workflowstate = EDUSIGN_MARKING_WORKFLOW_STATE_NOTMARKED;
        }
        if ($this->quickgrading && !$gradingdisabled) {
            $notmarked = get_string('markingworkflowstatenotmarked', 'edusign');
            $name = 'quickgrade_' . $row->id . '_workflowstate';
            $o .= html_writer::select($workflowstates, $name, $workflowstate, array('' => $notmarked));
            // Check if this user is a marker that can't manage allocations and doesn't have the marker column added.
            if ($this->edusignment->get_instance()->markingworkflow &&
                $this->edusignment->get_instance()->markingallocation &&
                !has_capability('mod/assign:manageallocations', $this->edusignment->get_context())) {
                $name = 'quickgrade_' . $row->id . '_allocatedmarker';
                $o .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => $name,
                    'value' => $row->allocatedmarker));
            }
        } else {
            $o .= $this->output->container(get_string('markingworkflowstate' . $workflowstate, 'edusign'), $workflowstate);
        }
        return $o;
    }

    /**
     * For download only - list current marking workflow state
     *
     * @param stdClass $row - The row of data
     * @return string The current marking workflow state
     */
    public function col_workflowstate($row) {
        $state = $row->workflowstate;
        if (empty($state)) {
            $state = EDUSIGN_MARKING_WORKFLOW_STATE_NOTMARKED;
        }

        return get_string('markingworkflowstate' . $state, 'edusign');
    }

    /**
     * list current marker
     *
     * @param stdClass $row - The row of data
     * @return id the user->id of the marker.
     */
    public function col_allocatedmarker(stdClass $row) {
        static $markers = null;
        static $markerlist = array();
        if ($markers === null) {
            list($sort, $params) = users_order_by_sql('u');
            // Only enrolled users could be edusigned as potential markers.
            $markers = get_enrolled_users($this->edusignment->get_context(), 'mod/assign:grade', 0, 'u.*', $sort);
            $markerlist[0] = get_string('choosemarker', 'edusign');
            $viewfullnames = has_capability('moodle/site:viewfullnames', $this->edusignment->get_context());
            foreach ($markers as $marker) {
                $markerlist[$marker->id] = fullname($marker, $viewfullnames);
            }
        }
        if (empty($markerlist)) {
            // TODO: add some form of notification here that no markers are available.
            return '';
        }
        if ($this->is_downloading()) {
            if (isset($markers[$row->allocatedmarker])) {
                return fullname(
                    $markers[$row->allocatedmarker],
                    has_capability('moodle/site:viewfullnames', $this->edusignment->get_context())
                );
            } else {
                return '';
            }
        }

        if ($this->quickgrading && has_capability('mod/assign:manageallocations', $this->edusignment->get_context()) &&
            (empty($row->workflowstate) ||
                $row->workflowstate == EDUSIGN_MARKING_WORKFLOW_STATE_INMARKING ||
                $row->workflowstate == EDUSIGN_MARKING_WORKFLOW_STATE_NOTMARKED)) {
            $name = 'quickgrade_' . $row->id . '_allocatedmarker';
            return html_writer::select($markerlist, $name, $row->allocatedmarker, false);
        } else if (!empty($row->allocatedmarker)) {
            $output = '';
            if ($this->quickgrading) { // Add hidden field for quickgrading page.
                $name = 'quickgrade_' . $row->id . '_allocatedmarker';
                $output .= html_writer::empty_tag(
                    'input',
                    array('type' => 'hidden', 'name' => $name, 'value' => $row->allocatedmarker)
                );
            }
            $output .= $markerlist[$row->allocatedmarker];
            return $output;
        }
    }

    /**
     * For download only - list all the valid options for this custom scale.
     *
     * @param stdClass $row - The row of data
     * @return string A list of valid options for the current scale
     */
    public function col_scale($row) {
        global $DB;

        if (empty($this->scale)) {
            $dbparams = array('id' => -($this->edusignment->get_instance()->grade));
            $this->scale = $DB->get_record('scale', $dbparams);
        }

        if (!empty($this->scale->scale)) {
            return implode("\n", explode(',', $this->scale->scale));
        }
        return '';
    }

    /**
     * Display a grade with scales etc.
     *
     * @param string $grade
     * @param boolean $editable
     * @param int $userid The user id of the user this grade belongs to
     * @param int $modified Timestamp showing when the grade was last modified
     * @return string The formatted grade
     */
    public function display_grade($grade, $editable, $userid, $modified) {
        if ($this->is_downloading()) {
            if ($this->edusignment->get_instance()->grade >= 0) {
                if ($grade == -1 || $grade === null) {
                    return '';
                }
                $gradeitem = $this->edusignment->get_grade_item();
                return format_float($grade, $gradeitem->get_decimals());
            } else {
                // This is a custom scale.
                $scale = $this->edusignment->display_grade($grade, false);
                if ($scale == '-') {
                    $scale = '';
                }
                return $scale;
            }
        }
        return $this->edusignment->display_grade($grade, $editable, $userid, $modified);
    }

    /**
     * Get the team info for this user.
     *
     * @param stdClass $row
     * @return string The team name
     */
    public function col_team(stdClass $row) {
        $submission = false;
        $group = false;
        $this->get_group_and_submission($row->id, $group, $submission, -1);
        if ($group) {
            return $group->name;
        } else if ($this->edusignment->get_instance()->preventsubmissionnotingroup) {
            $usergroups = $this->edusignment->get_all_groups($row->id);
            if (count($usergroups) > 1) {
                return get_string('multipleteamsgrader', 'edusign');
            } else {
                return get_string('noteamgrader', 'edusign');
            }
        }
        return get_string('defaultteam', 'edusign');
    }

    /**
     * Use a static cache to try and reduce DB calls.
     *
     * @param int $userid The user id for this submission
     * @param int $group The groupid (returned)
     * @param stdClass|false $submission The stdClass submission or false (returned)
     * @param int $attemptnumber Return a specific attempt number (-1 for latest)
     */
    protected function get_group_and_submission($userid, &$group, &$submission, $attemptnumber) {
        $group = false;
        if (isset($this->submissiongroups[$userid])) {
            $group = $this->submissiongroups[$userid];
        } else {
            $group = $this->edusignment->get_submission_group($userid, false);
            $this->submissiongroups[$userid] = $group;
        }

        $groupid = 0;
        if ($group) {
            $groupid = $group->id;
        }

        // Static cache is keyed by groupid and attemptnumber.
        // We may need both the latest and previous attempt in the same page.
        if (isset($this->groupsubmissions[$groupid . ':' . $attemptnumber])) {
            $submission = $this->groupsubmissions[$groupid . ':' . $attemptnumber];
        } else {
            $submission = $this->edusignment->get_group_submission($userid, $groupid, false, $attemptnumber);
            $this->groupsubmissions[$groupid . ':' . $attemptnumber] = $submission;
        }
    }

    /**
     * Format a list of outcomes.
     *
     * @param stdClass $row
     * @return string
     */
    public function col_outcomes(stdClass $row) {
        $outcomes = '';
        foreach ($this->gradinginfo->outcomes as $index => $outcome) {
            $options = make_grades_menu(-$outcome->scaleid);

            $options[0] = get_string('nooutcome', 'grades');
            if ($this->quickgrading && !($outcome->grades[$row->userid]->locked)) {
                $select = '<select name="outcome_' . $index . '_' . $row->userid . '" class="quickgrade">';
                foreach ($options as $optionindex => $optionvalue) {
                    $selected = '';
                    if ($outcome->grades[$row->userid]->grade == $optionindex) {
                        $selected = 'selected="selected"';
                    }
                    $select .= '<option value="' . $optionindex . '"' . $selected . '>' . $optionvalue . '</option>';
                }
                $select .= '</select>';
                $outcomes .= $this->output->container($outcome->name . ': ' . $select, 'outcome');
            } else {
                $name = $outcome->name . ': ' . $options[$outcome->grades[$row->userid]->grade];
                if ($this->is_downloading()) {
                    $outcomes .= $name;
                } else {
                    $outcomes .= $this->output->container($name, 'outcome');
                }
            }
        }

        return $outcomes;
    }

    public function col_delete(stdClass $row) {
        if ($row->status !== "submitted") {
            return '';
        }
        $urlparams = array('id' => $this->edusignment->get_course_module()->id,
                'userid' => $row->id,
                'action' => 'delete',
                'sesskey' => sesskey(),
                'page' => $this->currpage);
        $url = new moodle_url('/mod/edusign/view.php', $urlparams);
        $modal = '<div class="modal fade" id="Modal' . $row->id . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">' . get_string('delete', 'edusign') . '</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        </button>
                      </div>
                      <div class="modal-body">' . get_string('delete:confirm', 'edusign') . '</div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <a  class="btn btn-danger" href="' . $url . '">' . get_string('delete', 'edusign') . '</a>
                      </div>
                    </div>
                  </div>
                </div>';
        $link = '<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#Modal' . $row->id . '">' .
                get_string('delete', 'edusign') . '
</button>' . $modal;
        return $link;
    }
    /**
     * Format a user picture for display.
     *
     * @param stdClass $row
     * @return string
     */
    public function col_picture(stdClass $row) {
        return $this->output->user_picture($row);
    }

    /**
     * Format a user record for display (link to profile).
     *
     * @param stdClass $row
     * @return string
     */
    public function col_fullname($row) {
        if (!$this->is_downloading()) {
            $courseid = $this->edusignment->get_course()->id;
            $link = new moodle_url('/user/view.php', array('id' => $row->id, 'course' => $courseid));
            $fullname = $this->output->action_link($link, $this->edusignment->fullname($row));
        } else {
            $fullname = $this->edusignment->fullname($row);
        }

        if (!$this->edusignment->is_active_user($row->id)) {
            $suspendedstring = get_string('userenrolmentsuspended', 'grades');
            $fullname .= ' ' . $this->output->pix_icon('i/enrolmentsuspended', $suspendedstring);
            $fullname = html_writer::tag('span', $fullname, array('class' => 'usersuspended'));
        }
        return $fullname;
    }

    /**
     * Insert a checkbox for selecting the current row for batch operations.
     *
     * @param stdClass $row
     * @return string
     */
    public function col_select(stdClass $row) {
        $selectcol = '<label class="accesshide" for="selectuser_' . $row->userid . '">';
        $selectcol .= get_string('selectuser', 'edusign', $this->edusignment->fullname($row));
        $selectcol .= '</label>';
        $selectcol .= '<input type="checkbox"
                              id="selectuser_' . $row->userid . '"
                              name="selectedusers"
                              value="' . $row->userid . '"/>';
        $selectcol .= '<input type="hidden"
                              name="grademodified_' . $row->userid . '"
                              value="' . $row->timemarked . '"/>';
        $selectcol .= '<input type="hidden"
                              name="gradeattempt_' . $row->userid . '"
                              value="' . $row->attemptnumber . '"/>';
        return $selectcol;
    }

    /**
     * Return a users grades from the listing of all grade data for this edusignment.
     *
     * @param int $userid
     * @return mixed stdClass or false
     */
    private function get_gradebook_data_for_user($userid) {
        if (isset($this->gradinginfo->items[0]) && $this->gradinginfo->items[0]->grades[$userid]) {
            return $this->gradinginfo->items[0]->grades[$userid];
        }
        return false;
    }

    /**
     * Format a column of data for display.
     *
     * @param stdClass $row
     * @return string
     */
    public function col_gradecanbechanged(stdClass $row) {
        $gradingdisabled = $this->edusignment->grading_disabled($row->id);
        if ($gradingdisabled) {
            return get_string('no');
        } else {
            return get_string('yes');
        }
    }

    /**
     * Format a column of data for display
     *
     * @param stdClass $row
     * @return string
     */
    public function col_grademax(stdClass $row) {
        if ($this->edusignment->get_instance()->grade > 0) {
            $gradeitem = $this->edusignment->get_grade_item();
            return format_float($this->edusignment->get_instance()->grade, $gradeitem->get_decimals());
        } else {
            return '';
        }
    }

    /**
     * Format a column of data for display.
     *
     * @param stdClass $row
     * @return string
     */
    public function col_grade(stdClass $row) {
        $o = '';

        $link = '';
        $separator = $this->output->spacer(array(), true);
        $grade = '';
        $gradingdisabled = $this->edusignment->grading_disabled($row->id);

        if (!$this->is_downloading() && $this->hasgrade) {
            $urlparams = array('id' => $this->edusignment->get_course_module()->id,
                'rownum' => 0,
                'action' => 'grader');

            if ($this->edusignment->is_blind_marking()) {
                if (empty($row->recordid)) {
                    $row->recordid = $this->edusignment->get_uniqueid_for_user($row->userid);
                }
                $urlparams['blindid'] = $row->recordid;
            } else {
                $urlparams['userid'] = $row->userid;
            }

            $url = new moodle_url('/mod/edusign/view.php', $urlparams);
            $link = '<a href="' . $url . '" class="btn btn-primary">' . get_string('grade') . '</a>';
            $grade .= $link . $separator;
        }

        $grade .= $this->display_grade(
            $row->grade,
            $this->quickgrading && !$gradingdisabled,
            $row->userid,
            $row->timemarked
        );

        return $grade;
    }

    /**
     * Format a column of data for display.
     *
     * @param stdClass $row
     * @return string
     */
    public function col_finalgrade(stdClass $row) {
        $o = '';

        $grade = $this->get_gradebook_data_for_user($row->userid);
        if ($grade) {
            $o = $this->display_grade($grade->grade, false, $row->userid, $row->timemarked);
        }

        return $o;
    }

    /**
     * Format a column of data for display.
     *
     * @param stdClass $row
     * @return string
     */
    public function col_timemarked(stdClass $row) {
        $o = '-';

        if ($row->timemarked && $row->grade !== null && $row->grade >= 0) {
            $o = userdate($row->timemarked);
        }
        if ($row->timemarked && $this->is_downloading()) {
            // Force it for downloads as it affects import.
            $o = userdate($row->timemarked);
        }

        return $o;
    }

    /**
     * Format a column of data for display.
     *
     * @param stdClass $row
     * @return string
     */
    public function col_timesubmitted(stdClass $row) {
        $o = '-';

        $group = false;
        $submission = false;
        $this->get_group_and_submission($row->id, $group, $submission, -1);
        if ($submission && $submission->timemodified && $submission->status != EDUSIGN_SUBMISSION_STATUS_NEW) {
            $o = userdate($submission->timemodified);
        } else if ($row->timesubmitted && $row->status != EDUSIGN_SUBMISSION_STATUS_NEW) {
            $o = userdate($row->timesubmitted);
        }

        return $o;
    }

    /**
     * Format a column of data for display
     *
     * @param stdClass $row
     * @return string
     */
    public function col_status(stdClass $row) {
        $o = '';

        $instance = $this->edusignment->get_instance();

        $due = $instance->duedate;
        if ($row->extensionduedate) {
            $due = $row->extensionduedate;
        } else if (!empty($row->duedate)) {
            // The override due date.
            $due = $row->duedate;
        }

        $group = false;
        $submission = false;

        if ($instance->teamsubmission) {
            $this->get_group_and_submission($row->id, $group, $submission, -1);
        }

        if ($instance->teamsubmission && !$group && !$instance->preventsubmissionnotingroup) {
            $group = true;
        }

        if ($group && $submission) {
            $timesubmitted = $submission->timemodified;
            $status = $submission->status;
        } else {
            $timesubmitted = $row->timesubmitted;
            $status = $row->status;
        }

        $displaystatus = $status;
        if ($displaystatus == 'new') {
            $displaystatus = '';
        }

        if ($this->edusignment->is_any_submission_plugin_enabled()) {
            $o .= $this->output->container(
                get_string('submissionstatus_' . $displaystatus, 'edusign'),
                array('class' => 'submissionstatus' . $displaystatus)
            );
            if ($due && $timesubmitted > $due && $status != EDUSIGN_SUBMISSION_STATUS_NEW) {
                $usertime = format_time($timesubmitted - $due);
                $latemessage = get_string(
                    'submittedlateshort',
                    'edusign',
                    $usertime
                );
                $o .= $this->output->container($latemessage, 'latesubmission');
            }
            if ($row->locked) {
                $lockedstr = get_string('submissionslockedshort', 'edusign');
                $o .= $this->output->container($lockedstr, 'lockedsubmission');
            }

            // Add status of "grading" if markflow is not enabled.
            if (!$instance->markingworkflow) {
                if ($row->grade !== null && $row->grade >= 0) {
                    $o .= $this->output->container(get_string('graded', 'edusign'), 'submissiongraded');
                } else if (!$timesubmitted || $status == EDUSIGN_SUBMISSION_STATUS_NEW) {
                    $now = time();
                    if ($due && ($now > $due)) {
                        $overduestr = get_string('overdue', 'edusign', format_time($now - $due));
                        $o .= $this->output->container($overduestr, 'overduesubmission');
                    }
                }
            }
        }

        if ($instance->markingworkflow) {
            $o .= $this->col_workflowstatus($row);
        }
        if ($row->extensionduedate) {
            $userdate = userdate($row->extensionduedate);
            $extensionstr = get_string('userextensiondate', 'edusign', $userdate);
            $o .= $this->output->container($extensionstr, 'extensiondate');
        }

        if ($this->is_downloading()) {
            $o = strip_tags(rtrim(str_replace('</div>', ' - ', $o), '- '));
        }

        return $o;
    }

    /**
     * Format a column of data for display.
     *
     * @param stdClass $row
     * @return string
     */
    public function col_allowsubmissionsfromdate(stdClass $row) {
        $o = '';

        if ($row->allowsubmissionsfromdate) {
            $userdate = userdate($row->allowsubmissionsfromdate);
            $o = ($this->is_downloading()) ? $userdate : $this->output->container($userdate, 'allowsubmissionsfromdate');
        }

        return $o;
    }

    /**
     * Format a column of data for display.
     *
     * @param stdClass $row
     * @return string
     */
    public function col_duedate(stdClass $row) {
        $o = '';

        if ($row->duedate) {
            $userdate = userdate($row->duedate);
            $o = ($this->is_downloading()) ? $userdate : $this->output->container($userdate, 'duedate');
        }

        return $o;
    }

    /**
     * Format a column of data for display.
     *
     * @param stdClass $row
     * @return string
     */
    public function col_cutoffdate(stdClass $row) {
        $o = '';

        if ($row->cutoffdate) {
            $userdate = userdate($row->cutoffdate);
            $o = ($this->is_downloading()) ? $userdate : $this->output->container($userdate, 'cutoffdate');
        }

        return $o;
    }

    /**
     * Format a column of data for display.
     *
     * @param stdClass $row
     * @return string
     */
    public function col_userid(stdClass $row) {
        global $USER;

        $edit = '';

        $actions = array();

        $urlparams = array('id' => $this->edusignment->get_course_module()->id,
            'rownum' => 0,
            'action' => 'grader');

        if ($this->edusignment->is_blind_marking()) {
            if (empty($row->recordid)) {
                $row->recordid = $this->edusignment->get_uniqueid_for_user($row->userid);
            }
            $urlparams['blindid'] = $row->recordid;
        } else {
            $urlparams['userid'] = $row->userid;
        }
        $url = new moodle_url('/mod/edusign/view.php', $urlparams);
        $noimage = null;

        if (!$row->grade) {
            $description = get_string('grade');
        } else {
            $description = get_string('updategrade', 'edusign');
        }
        $actions['grade'] = new action_menu_link_secondary(
            $url,
            $noimage,
            $description
        );

        // Everything we need is in the row.
        $submission = $row;
        $flags = $row;
        if ($this->edusignment->get_instance()->teamsubmission) {
            // Use the cache for this.
            $submission = false;
            $group = false;
            $this->get_group_and_submission($row->id, $group, $submission, -1);
        }

        $submissionsopen = $this->edusignment->submissions_open(
            $row->id,
            true,
            $submission,
            $flags,
            $this->gradinginfo
        );
        $caneditsubmission = $this->edusignment->can_edit_submission($row->id, $USER->id);

        // Hide for offline edusignments.
        if ($this->edusignment->is_any_submission_plugin_enabled()) {
            if (!$row->status ||
                $row->status == EDUSIGN_SUBMISSION_STATUS_DRAFT ||
                !$this->edusignment->get_instance()->submissiondrafts) {
                if (!$row->locked) {
                    $urlparams = array('id' => $this->edusignment->get_course_module()->id,
                        'userid' => $row->id,
                        'action' => 'lock',
                        'sesskey' => sesskey(),
                        'page' => $this->currpage);
                    $url = new moodle_url('/mod/edusign/view.php', $urlparams);

                    $description = get_string('preventsubmissionsshort', 'edusign');
                    $actions['lock'] = new action_menu_link_secondary(
                        $url,
                        $noimage,
                        $description
                    );
                } else {
                    $urlparams = array('id' => $this->edusignment->get_course_module()->id,
                        'userid' => $row->id,
                        'action' => 'unlock',
                        'sesskey' => sesskey(),
                        'page' => $this->currpage);
                    $url = new moodle_url('/mod/edusign/view.php', $urlparams);
                    $description = get_string('allowsubmissionsshort', 'edusign');
                    $actions['unlock'] = new action_menu_link_secondary(
                        $url,
                        $noimage,
                        $description
                    );
                }
            }

            if ($submissionsopen &&
                $USER->id != $row->id &&
                $caneditsubmission) {
                $urlparams = array('id' => $this->edusignment->get_course_module()->id,
                    'userid' => $row->id,
                    'action' => 'editsubmission',
                    'sesskey' => sesskey(),
                    'page' => $this->currpage);
                $url = new moodle_url('/mod/edusign/view.php', $urlparams);
                $description = get_string('editsubmission', 'edusign');
                $actions['editsubmission'] = new action_menu_link_secondary(
                    $url,
                    $noimage,
                    $description
                );
            }
        }
        if (($this->edusignment->get_instance()->duedate ||
                $this->edusignment->get_instance()->cutoffdate) &&
            $this->hasgrantextension) {
            $urlparams = array('id' => $this->edusignment->get_course_module()->id,
                'userid' => $row->id,
                'action' => 'grantextension',
                'sesskey' => sesskey(),
                'page' => $this->currpage);
            $url = new moodle_url('/mod/edusign/view.php', $urlparams);
            $description = get_string('grantextension', 'edusign');
            $actions['grantextension'] = new action_menu_link_secondary(
                $url,
                $noimage,
                $description
            );
        }
        if ($row->status == EDUSIGN_SUBMISSION_STATUS_SUBMITTED &&
            $this->edusignment->get_instance()->submissiondrafts) {
            $urlparams = array('id' => $this->edusignment->get_course_module()->id,
                'userid' => $row->id,
                'action' => 'reverttodraft',
                'sesskey' => sesskey(),
                'page' => $this->currpage);
            $url = new moodle_url('/mod/edusign/view.php', $urlparams);
            $description = get_string('reverttodraftshort', 'edusign');
            $actions['reverttodraft'] = new action_menu_link_secondary(
                $url,
                $noimage,
                $description
            );
        }
        if ($row->status == EDUSIGN_SUBMISSION_STATUS_DRAFT &&
            $this->edusignment->get_instance()->submissiondrafts &&
            $caneditsubmission &&
            $submissionsopen &&
            $row->id != $USER->id) {
            $urlparams = array('id' => $this->edusignment->get_course_module()->id,
                'userid' => $row->id,
                'action' => 'submitotherforgrading',
                'sesskey' => sesskey(),
                'page' => $this->currpage);
            $url = new moodle_url('/mod/edusign/view.php', $urlparams);
            $description = get_string('submitforgrading', 'edusign');
            $actions['submitforgrading'] = new action_menu_link_secondary(
                $url,
                $noimage,
                $description
            );
        }

        $ismanual = $this->edusignment->get_instance()->attemptreopenmethod == EDUSIGN_ATTEMPT_REOPEN_METHOD_MANUAL;
        $hassubmission = !empty($row->status);
        $notreopened = $hassubmission && $row->status != EDUSIGN_SUBMISSION_STATUS_REOPENED;
        $isunlimited = $this->edusignment->get_instance()->maxattempts == EDUSIGN_UNLIMITED_ATTEMPTS;
        $hasattempts = $isunlimited || $row->attemptnumber < $this->edusignment->get_instance()->maxattempts - 1;

        if ($ismanual && $hassubmission && $notreopened && $hasattempts) {
            $urlparams = array('id' => $this->edusignment->get_course_module()->id,
                'userid' => $row->id,
                'action' => 'addattempt',
                'sesskey' => sesskey(),
                'page' => $this->currpage);
            $url = new moodle_url('/mod/edusign/view.php', $urlparams);
            $description = get_string('addattempt', 'edusign');
            $actions['addattempt'] = new action_menu_link_secondary(
                $url,
                $noimage,
                $description
            );
        }

        $menu = new action_menu();
        $menu->set_owner_selector('.gradingtable-actionmenu');
        $menu->set_alignment(action_menu::TL, action_menu::BL);
        $menu->set_constraint('.gradingtable > .no-overflow');
        $menu->set_menu_trigger(get_string('edit'));
        foreach ($actions as $action) {
            $menu->add($action);
        }

        // Prioritise the menu ahead of all other actions.
        $menu->prioritise = true;

        $edit .= $this->output->render($menu);

        return $edit;
    }

    /**
     * Write the plugin summary with an optional link to view the full feedback/submission.
     *
     * @param edusign_plugin $plugin Submission plugin or feedback plugin
     * @param stdClass $item Submission or grade
     * @param string $returnaction The return action to pass to the
     *                             view_submission page (the current page)
     * @param string $returnparams The return params to pass to the view_submission
     *                             page (the current page)
     * @return string The summary with an optional link
     */
    private function format_plugin_summary_with_link(
        edusign_plugin $plugin,
        stdClass $item,
        $returnaction,
        $returnparams
    ) {
        $link = '';
        $showviewlink = false;

        $summary = $plugin->view_summary($item, $showviewlink);
        $separator = '';
        if ($showviewlink) {
            $viewstr = get_string('view' . substr($plugin->get_subtype(), strlen('edusign')), 'edusign');
            $icon = $this->output->pix_icon('t/preview', $viewstr);
            $urlparams = array('id' => $this->edusignment->get_course_module()->id,
                'sid' => $item->id,
                'gid' => $item->id,
                'plugin' => $plugin->get_type(),
                'action' => 'viewplugin' . $plugin->get_subtype(),
                'returnaction' => $returnaction,
                'returnparams' => http_build_query($returnparams));
            $url = new moodle_url('/mod/edusign/view.php', $urlparams);
            $link = $this->output->action_link($url, $icon);
            $separator = $this->output->spacer(array(), true);
        }

        return $link . $separator . $summary;
    }

    /**
     * Format the submission and feedback columns.
     *
     * @param string $colname The column name
     * @param stdClass $row The submission row
     * @return mixed string or NULL
     */
    public function other_cols($colname, $row) {
        // For extra user fields the result is already in $row.
        if (empty($this->plugincache[$colname])) {
            return $row->$colname;
        }

        // This must be a plugin field.
        $plugincache = $this->plugincache[$colname];

        $plugin = $plugincache[0];

        $field = null;
        if (isset($plugincache[1])) {
            $field = $plugincache[1];
        }

        if ($plugin->is_visible() && $plugin->is_enabled()) {
            if ($plugin->get_subtype() == 'edusignsubmission') {
                if ($this->edusignment->get_instance()->teamsubmission) {
                    $group = false;
                    $submission = false;

                    $this->get_group_and_submission($row->id, $group, $submission, -1);
                    if ($submission) {
                        if ($submission->status == EDUSIGN_SUBMISSION_STATUS_REOPENED) {
                            // For a newly reopened submission - we want to show the previous submission in the table.
                            $this->get_group_and_submission($row->id, $group, $submission, $submission->attemptnumber - 1);
                        }
                        if (isset($field)) {
                            return $plugin->get_editor_text($field, $submission->id);
                        }
                        return $this->format_plugin_summary_with_link(
                            $plugin,
                            $submission,
                            'grading',
                            array()
                        );
                    }
                } else if ($row->submissionid) {
                    if ($row->status == EDUSIGN_SUBMISSION_STATUS_REOPENED) {
                        // For a newly reopened submission - we want to show the previous submission in the table.
                        $submission = $this->edusignment->get_user_submission($row->userid, false, $row->attemptnumber - 1);
                    } else {
                        $submission = new stdClass();
                        $submission->id = $row->submissionid;
                        $submission->timecreated = $row->firstsubmission;
                        $submission->timemodified = $row->timesubmitted;
                        $submission->edusignment = $this->edusignment->get_instance()->id;
                        $submission->userid = $row->userid;
                        $submission->attemptnumber = $row->attemptnumber;
                    }
                    // Field is used for only for import/export and refers the the fieldname for the text editor.
                    if (isset($field)) {
                        return $plugin->get_editor_text($field, $submission->id);
                    }
                    return $this->format_plugin_summary_with_link(
                        $plugin,
                        $submission,
                        'grading',
                        array()
                    );
                }
            } else {
                $grade = null;
                if (isset($field)) {
                    return $plugin->get_editor_text($field, $row->gradeid);
                }

                if ($row->gradeid) {
                    $grade = new stdClass();
                    $grade->id = $row->gradeid;
                    $grade->timecreated = $row->firstmarked;
                    $grade->timemodified = $row->timemarked;
                    $grade->edusignment = $this->edusignment->get_instance()->id;
                    $grade->userid = $row->userid;
                    $grade->grade = $row->grade;
                    $grade->mailed = $row->mailed;
                    $grade->attemptnumber = $row->attemptnumber;
                }
                if ($this->quickgrading && $plugin->supports_quickgrading()) {
                    return $plugin->get_quickgrading_html($row->userid, $grade);
                } else if ($grade) {
                    return $this->format_plugin_summary_with_link(
                        $plugin,
                        $grade,
                        'grading',
                        array()
                    );
                }
            }
        }
        return '';
    }

    /**
     * Using the current filtering and sorting - load all rows and return a single column from them.
     *
     * @param string $columnname The name of the raw column data
     * @return array of data
     */
    public function get_column_data($columnname) {
        $this->setup();
        $this->currpage = 0;
        $this->query_db($this->tablemaxrows);
        $result = array();
        foreach ($this->rawdata as $row) {
            $result[] = $row->$columnname;
        }
        return $result;
    }

    /**
     * Return things to the renderer.
     *
     * @return string the edusignment name
     */
    public function get_edusignment_name() {
        return $this->edusignment->get_instance()->name;
    }

    /**
     * Return things to the renderer.
     *
     * @return int the course module id
     */
    public function get_course_module_id() {
        return $this->edusignment->get_course_module()->id;
    }

    /**
     * Return things to the renderer.
     *
     * @return int the course id
     */
    public function get_course_id() {
        return $this->edusignment->get_course()->id;
    }

    /**
     * Return things to the renderer.
     *
     * @return stdClass The course context
     */
    public function get_course_context() {
        return $this->edusignment->get_course_context();
    }

    /**
     * Return things to the renderer.
     *
     * @return bool Does this edusignment accept submissions
     */
    public function submissions_enabled() {
        return $this->edusignment->is_any_submission_plugin_enabled();
    }

    /**
     * Return things to the renderer.
     *
     * @return bool Can this user view all grades (the gradebook)
     */
    public function can_view_all_grades() {
        $context = $this->edusignment->get_course_context();
        return has_capability('gradereport/grader:view', $context) &&
            has_capability('moodle/grade:viewall', $context);
    }

    /**
     * Always return a valid sort - even if the userid column is missing.
     *
     * @return array column name => SORT_... constant.
     */
    public function get_sort_columns() {
        $result = parent::get_sort_columns();

        $edusignment = $this->edusignment->get_instance();
        if (empty($edusignment->blindmarking)) {
            $result = array_merge($result, array('userid' => SORT_ASC));
        } else {
            $result = array_merge($result, [
                'COALESCE(s.timecreated, ' . time() . ')' => SORT_ASC,
                'COALESCE(s.id, ' . PHP_INT_MAX . ')' => SORT_ASC,
                'um.id' => SORT_ASC,
            ]);
        }
        return $result;
    }

    /**
     * Override the table show_hide_link to not show for select column.
     *
     * @param string $column the column name, index into various names.
     * @param int $index numerical index of the column.
     * @return string HTML fragment.
     */
    protected function show_hide_link($column, $index) {
        if ($index > 0 || !$this->hasgrade) {
            return parent::show_hide_link($column, $index);
        }
        return '';
    }

    /**
     * Overides setup to ensure it will only run a single time.
     */
    public function setup() {
        // Check if the setup function has been called before, we should not run it twice.
        // If we do the sortorder of the table will be broken.
        if (!empty($this->setup)) {
            return;
        }
        parent::setup();
    }
}
