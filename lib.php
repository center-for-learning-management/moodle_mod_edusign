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
 * This file contains the moodle hooks for the edusign module.
 *
 * It delegates most functions to the edusignment class.
 *
 * @package   mod_edusign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Adds an edusignment instance
 *
 * This is done by calling the add_instance() method of the edusignment type class
 *
 * @param stdClass $data
 * @param mod_edusign_mod_form $form
 * @return int The instance id of the new edusignment
 */
function edusign_add_instance(stdClass $data, mod_edusign_mod_form $form = null) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/edusign/locallib.php');

    $edusignment = new edusign(context_module::instance($data->coursemodule), null, null);
    return $edusignment->add_instance($data, true);
}

/**
 * delete an edusignment instance
 *
 * @param int $id
 * @return bool
 */
function edusign_delete_instance($id) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/edusign/locallib.php');
    $cm = get_coursemodule_from_instance('edusign', $id, 0, false, MUST_EXIST);
    $context = context_module::instance($cm->id);

    $edusignment = new edusign($context, null, null);
    return $edusignment->delete_instance();
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * This function will remove all edusignment submissions and feedbacks in the database
 * and clean up any related data.
 *
 * @param stdClass $data the data submitted from the reset course.
 * @return array
 */
function edusign_reset_userdata($data) {
    global $CFG, $DB;
    require_once($CFG->dirroot . '/mod/edusign/locallib.php');

    $status = array();
    $params = array('courseid' => $data->courseid);
    $sql = "SELECT a.id FROM {edusign} a WHERE a.course=:courseid";
    $course = $DB->get_record('course', array('id' => $data->courseid), '*', MUST_EXIST);
    if ($edusigns = $DB->get_records_sql($sql, $params)) {
        foreach ($edusigns as $edusign) {
            $cm = get_coursemodule_from_instance(
                    'edusign',
                    $edusign->id,
                    $data->courseid,
                    false,
                    MUST_EXIST
            );
            $context = context_module::instance($cm->id);
            $edusignment = new edusign($context, $cm, $course);
            $status = array_merge($status, $edusignment->reset_userdata($data));
        }
    }
    return $status;
}

/**
 * This standard function will check all instances of this module
 * and make sure there are up-to-date events created for each of them.
 * If courseid = 0, then every edusignment event in the site is checked, else
 * only edusignment events belonging to the course specified are checked.
 *
 * @param int $courseid
 * @param int|stdClass $instance edusign module instance or ID.
 * @param int|stdClass $cm Course module object or ID (not used in this module).
 * @return bool
 */
function edusign_refresh_events($courseid = 0, $instance = null, $cm = null) {
    global $CFG, $DB;
    require_once($CFG->dirroot . '/mod/edusign/locallib.php');

    // If we have instance information then we can just update the one event instead of updating all events.
    if (isset($instance)) {
        if (!is_object($instance)) {
            $instance = $DB->get_record('edusign', array('id' => $instance), '*', MUST_EXIST);
        }
        if (isset($cm)) {
            if (!is_object($cm)) {
                edusign_prepare_update_events($instance);
                return true;
            } else {
                $course = get_course($instance->course);
                edusign_prepare_update_events($instance, $course, $cm);
                return true;
            }
        }
    }

    if ($courseid) {
        // Make sure that the course id is numeric.
        if (!is_numeric($courseid)) {
            return false;
        }
        if (!$edusigns = $DB->get_records('edusign', array('course' => $courseid))) {
            return false;
        }
        // Get course from courseid parameter.
        if (!$course = $DB->get_record('course', array('id' => $courseid), '*')) {
            return false;
        }
    } else {
        if (!$edusigns = $DB->get_records('edusign')) {
            return false;
        }
    }
    foreach ($edusigns as $edusign) {
        edusign_prepare_update_events($edusign);
    }

    return true;
}

/**
 * This actually updates the normal and completion calendar events.
 *
 * @param stdClass $edusign edusignment object (from DB).
 * @param stdClass $course Course object.
 * @param stdClass $cm Course module object.
 */
function edusign_prepare_update_events($edusign, $course = null, $cm = null) {
    global $DB;
    if (!isset($course)) {
        // Get course and course module for the edusignment.
        list($course, $cm) = get_course_and_cm_from_instance($edusign->id, 'edusign', $edusign->course);
    }
    // Refresh the edusignment's calendar events.
    $context = context_module::instance($cm->id);
    $edusignment = new edusign($context, $cm, $course);
    $edusignment->update_calendar($cm->id);
    // Refresh the calendar events also for the edusignment overrides.
    $overrides = $DB->get_records(
            'edusign_overrides',
            ['edusignid' => $edusign->id],
            '',
            'id, groupid, userid, duedate, sortorder'
    );
    foreach ($overrides as $override) {
        if (empty($override->userid)) {
            unset($override->userid);
        }
        if (empty($override->groupid)) {
            unset($override->groupid);
        }
        edusign_update_events($edusignment, $override);
    }
}

/**
 * Removes all grades from gradebook
 *
 * @param int $courseid The ID of the course to reset
 * @param string $type Optional type of edusignment to limit the reset to a particular edusignment type
 */
function edusign_reset_gradebook($courseid, $type = '') {
    global $CFG, $DB;

    $params = array('moduletype' => 'edusign', 'courseid' => $courseid);
    $sql = 'SELECT a.*, cm.idnumber as cmidnumber, a.course as courseid
            FROM {edusign} a, {course_modules} cm, {modules} m
            WHERE m.name=:moduletype AND m.id=cm.module AND cm.instance=a.id AND a.course=:courseid';

    if ($edusignments = $DB->get_records_sql($sql, $params)) {
        foreach ($edusignments as $edusignment) {
            edusign_grade_item_update($edusignment, 'reset');
        }
    }
}

/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the edusignment.
 *
 * @param moodleform $mform form passed by reference
 */
function edusign_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'edusignheader', get_string('modulenameplural', 'edusign'));
    $name = get_string('deleteallsubmissions', 'edusign');
    $mform->addElement('advcheckbox', 'reset_edusign_submissions', $name);
    $mform->addElement(
            'advcheckbox',
            'reset_edusign_user_overrides',
            get_string('removealluseroverrides', 'edusign')
    );
    $mform->addElement(
            'advcheckbox',
            'reset_edusign_group_overrides',
            get_string('removeallgroupoverrides', 'edusign')
    );
}

/**
 * Course reset form defaults.
 *
 * @param object $course
 * @return array
 */
function edusign_reset_course_form_defaults($course) {
    return array('reset_edusign_submissions' => 1,
            'reset_edusign_group_overrides' => 1,
            'reset_edusign_user_overrides' => 1);
}

/**
 * Update an edusignment instance
 *
 * This is done by calling the update_instance() method of the edusignment type class
 *
 * @param stdClass $data
 * @param stdClass $form - unused
 * @return object
 */
function edusign_update_instance(stdClass $data, $form) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/edusign/locallib.php');
    $context = context_module::instance($data->coursemodule);
    $edusignment = new edusign($context, null, null);
    return $edusignment->update_instance($data);
}

/**
 * This function updates the events associated to the edusign.
 * If $override is non-zero, then it updates only the events
 * associated with the specified override.
 *
 * @param edusign $edusign the edusign object.
 * @param object $override (optional) limit to a specific override
 */
function edusign_update_events($edusign, $override = null) {
    global $CFG, $DB;

    require_once($CFG->dirroot . '/calendar/lib.php');

    $edusigninstance = $edusign->get_instance();

    // Load the old events relating to this edusign.
    $conds = array('modulename' => 'edusign', 'instance' => $edusigninstance->id);
    if (!empty($override)) {
        // Only load events for this override.
        if (isset($override->userid)) {
            $conds['userid'] = $override->userid;
        } else if (isset($override->groupid)) {
            $conds['groupid'] = $override->groupid;
        } else {
            // This is not a valid override, it may have been left from a bad import or restore.
            $conds['groupid'] = $conds['userid'] = 0;
        }
    }
    $oldevents = $DB->get_records('event', $conds, 'id ASC');

    // Now make a to-do list of all that needs to be updated.
    if (empty($override)) {
        // We are updating the primary settings for the edusignment, so we need to add all the overrides.
        $overrides = $DB->get_records('edusign_overrides', array('edusignid' => $edusigninstance->id), 'id ASC');
        // It is necessary to add an empty stdClass to the beginning of the array as the $oldevents
        // list contains the original (non-override) event for the module. If this is not included
        // the logic below will end up updating the wrong row when we try to reconcile this $overrides
        // list against the $oldevents list.
        array_unshift($overrides, new stdClass());
    } else {
        // Just do the one override.
        $overrides = array($override);
    }

    if (!empty($edusign->get_course_module())) {
        $cmid = $edusign->get_course_module()->id;
    } else {
        $cmid = get_coursemodule_from_instance('edusign', $edusigninstance->id, $edusigninstance->course)->id;
    }

    foreach ($overrides as $current) {
        $groupid = isset($current->groupid) ? $current->groupid : 0;
        $userid = isset($current->userid) ? $current->userid : 0;
        $duedate = isset($current->duedate) ? $current->duedate : $edusigninstance->duedate;

        // Only add 'due' events for an override if they differ from the edusign default.
        $addclose = empty($current->id) || !empty($current->duedate);

        $event = new stdClass();
        $event->type = CALENDAR_EVENT_TYPE_ACTION;
        $event->description = format_module_intro('edusign', $edusigninstance, $cmid);
        // Events module won't show user events when the courseid is nonzero.
        $event->courseid = ($userid) ? 0 : $edusigninstance->course;
        $event->groupid = $groupid;
        $event->userid = $userid;
        $event->modulename = 'edusign';
        $event->instance = $edusigninstance->id;
        $event->timestart = $duedate;
        $event->timeduration = 0;
        $event->timesort = $event->timestart + $event->timeduration;
        $event->visible = instance_is_visible('edusign', $edusigninstance);
        $event->eventtype = EDUSIGN_EVENT_TYPE_DUE;
        $event->priority = null;

        // Determine the event name and priority.
        if ($groupid) {
            // Group override event.
            $params = new stdClass();
            $params->edusign = $edusigninstance->name;
            $params->group = groups_get_group_name($groupid);
            if ($params->group === false) {
                // Group doesn't exist, just skip it.
                continue;
            }
            $eventname = get_string('overridegroupeventname', 'edusign', $params);
            // Set group override priority.
            if (isset($current->sortorder)) {
                $event->priority = $current->sortorder;
            }
        } else if ($userid) {
            // User override event.
            $params = new stdClass();
            $params->edusign = $edusigninstance->name;
            $eventname = get_string('overrideusereventname', 'edusign', $params);
            // Set user override priority.
            $event->priority = CALENDAR_EVENT_USER_OVERRIDE_PRIORITY;
        } else {
            // The parent event.
            $eventname = $edusigninstance->name;
        }

        if ($duedate && $addclose) {
            if ($oldevent = array_shift($oldevents)) {
                $event->id = $oldevent->id;
            } else {
                unset($event->id);
            }
            $event->name = $eventname . ' (' . get_string('duedate', 'edusign') . ')';
            calendar_event::create($event, false);
        }
    }

    // Delete any leftover events.
    foreach ($oldevents as $badevent) {
        $badevent = calendar_event::load($badevent);
        $badevent->delete();
    }
}

/**
 * Return the list if Moodle features this module supports
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, null if doesn't know
 */
function edusign_supports($feature) {
    switch ($feature) {
        case FEATURE_GROUPS:
            return true;
        case FEATURE_GROUPINGS:
            return true;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_COMPLETION_HAS_RULES:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_GRADE_OUTCOMES:
            return false;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_ADVANCED_GRADING:
            return false;
        case FEATURE_PLAGIARISM:
            return false;
        case FEATURE_COMMENT:
            return true;

        default:
            return null;
    }
}

/**
 * Lists all gradable areas for the advanced grading methods gramework
 *
 * @return array('string'=>'string') An array with area names as keys and descriptions as values
 */
function edusign_grading_areas_list() {
    return array('submissions' => get_string('submissions', 'edusign'));
}

/**
 * extend an assigment navigation settings
 *
 * @param settings_navigation $settings
 * @param navigation_node $navref
 * @return void
 */
function edusign_extend_settings_navigation(settings_navigation $settings, navigation_node $navref) {
    global $PAGE, $DB;

    // We want to add these new nodes after the Edit settings node, and before the
    // Locally edusigned roles node. Of course, both of those are controlled by capabilities.
    $keys = $navref->get_children_key_list();
    $beforekey = null;
    $i = array_search('modedit', $keys);
    if ($i === false and array_key_exists(0, $keys)) {
        $beforekey = $keys[0];
    } else if (array_key_exists($i + 1, $keys)) {
        $beforekey = $keys[$i + 1];
    }

    $cm = $PAGE->cm;
    if (!$cm) {
        return;
    }

    $context = $cm->context;
    $course = $PAGE->course;

    if (!$course) {
        return;
    }

    if (has_capability('mod/assign:manageoverrides', $PAGE->cm->context)) {
        $url = new moodle_url('/mod/edusign/overrides.php', array('cmid' => $PAGE->cm->id));
        $node = navigation_node::create(
                get_string('groupoverrides', 'edusign'),
                new moodle_url($url, array('mode' => 'group')),
                navigation_node::TYPE_SETTING,
                null,
                'mod_edusign_groupoverrides'
        );
        $navref->add_node($node, $beforekey);

        $node = navigation_node::create(
                get_string('useroverrides', 'edusign'),
                new moodle_url($url, array('mode' => 'user')),
                navigation_node::TYPE_SETTING,
                null,
                'mod_edusign_useroverrides'
        );
        $navref->add_node($node, $beforekey);
    }

    // Link to gradebook.
    if (has_capability('gradereport/grader:view', $cm->context) &&
            has_capability('moodle/grade:viewall', $cm->context)) {
        $link = new moodle_url('/grade/report/grader/index.php', array('id' => $course->id));
        $linkname = get_string('viewgradebook', 'edusign');
        $node = $navref->add($linkname, $link, navigation_node::TYPE_SETTING);
    }

    // Link to download all submissions.
    if (has_any_capability(array('mod/assign:grade', 'mod/edusign:viewsignings'), $context)) {
        $link = new moodle_url('/mod/edusign/view.php', array('id' => $cm->id, 'action' => 'grading'));
        $node = $navref->add(get_string('viewgrading', 'edusign'), $link, navigation_node::TYPE_SETTING);

        $link = new moodle_url('/mod/edusign/view.php', array('id' => $cm->id, 'action' => 'downloadall'));
        $node = $navref->add(get_string('downloadall', 'edusign'), $link, navigation_node::TYPE_SETTING);
    }

    if (has_capability('mod/assign:revealidentities', $context)) {
        $dbparams = array('id' => $cm->instance);
        $edusignment = $DB->get_record('edusign', $dbparams, 'blindmarking, revealidentities');

        if ($edusignment && $edusignment->blindmarking && !$edusignment->revealidentities) {
            $urlparams = array('id' => $cm->id, 'action' => 'revealidentities');
            $url = new moodle_url('/mod/edusign/view.php', $urlparams);
            $linkname = get_string('revealidentities', 'edusign');
            $node = $navref->add($linkname, $url, navigation_node::TYPE_SETTING);
        }
    }
}

/**
 * Add a get_coursemodule_info function in case any edusignment type wants to add 'extra' information
 * for the course (see resource).
 *
 * Given a course_module object, this function returns any "extra" information that may be needed
 * when printing this activity in a course listing.  See get_array_of_activities() in course/lib.php.
 *
 * @param stdClass $coursemodule The coursemodule object (record).
 * @return cached_cm_info An object on information that the courses
 *                        will know about (most noticeably, an icon).
 */
function edusign_get_coursemodule_info($coursemodule) {
    global $CFG, $DB;

    $dbparams = array('id' => $coursemodule->instance);
    $fields = 'id, name, alwaysshowdescription, allowsubmissionsfromdate, intro, introformat, completionsubmit';
    if (!$edusignment = $DB->get_record('edusign', $dbparams, $fields)) {
        return false;
    }

    $result = new cached_cm_info();
    $result->name = $edusignment->name;
    if ($coursemodule->showdescription) {
        if ($edusignment->alwaysshowdescription || time() > $edusignment->allowsubmissionsfromdate) {
            // Convert intro to html. Do not filter cached version, filters run at display time.
            $result->content = format_module_intro('edusign', $edusignment, $coursemodule->id, false);
        }
    }

    // Populate the custom completion rules as key => value pairs, but only if the completion mode is 'automatic'.
    if ($coursemodule->completion == COMPLETION_TRACKING_AUTOMATIC) {
        $result->customdata['customcompletionrules']['completionsubmit'] = $edusignment->completionsubmit;
    }

    return $result;
}

/**
 * Callback which returns human-readable strings describing the active completion custom rules for the module instance.
 *
 * @param cm_info|stdClass $cm object with fields ->completion and ->customdata['customcompletionrules']
 * @return array $descriptions the array of descriptions for the custom rules.
 */
function mod_edusign_get_completion_active_rule_descriptions($cm) {
    // Values will be present in cm_info, and we assume these are up to date.
    if (empty($cm->customdata['customcompletionrules'])
            || $cm->completion != COMPLETION_TRACKING_AUTOMATIC) {
        return [];
    }

    $descriptions = [];
    foreach ($cm->customdata['customcompletionrules'] as $key => $val) {
        switch ($key) {
            case 'completionsubmit':
                if (!empty($val)) {
                    $descriptions[] = get_string('completionsubmit', 'edusign');
                }
                break;
            default:
                break;
        }
    }
    return $descriptions;
}

/**
 * Return a list of page types
 *
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function edusign_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $modulepagetype = array(
            'mod-edusign-*' => get_string('page-mod-edusign-x', 'edusign'),
            'mod-edusign-view' => get_string('page-mod-edusign-view', 'edusign'),
    );
    return $modulepagetype;
}

/**
 * Print an overview of all edusignments
 * for the courses.
 *
 * @param mixed $courses The list of courses to print the overview for
 * @param array $htmlarray The array of html to return
 * @return true
 * @todo The final deprecation of this function will take place in Moodle 3.7 - see MDL-57487.
 * @deprecated since 3.3
 */
function edusign_print_overview($courses, &$htmlarray) {
    global $CFG, $DB;

    debugging('The function edusign_print_overview() is now deprecated.', DEBUG_DEVELOPER);

    if (empty($courses) || !is_array($courses) || count($courses) == 0) {
        return true;
    }

    if (!$edusignments = get_all_instances_in_courses('edusign', $courses)) {
        return true;
    }

    $edusignmentids = array();

    // Do edusignment_base::isopen() here without loading the whole thing for speed.
    foreach ($edusignments as $key => $edusignment) {
        $time = time();
        $isopen = false;
        if ($edusignment->duedate) {
            $duedate = false;
            if ($edusignment->cutoffdate) {
                $duedate = $edusignment->cutoffdate;
            }
            if ($duedate) {
                $isopen = ($edusignment->allowsubmissionsfromdate <= $time && $time <= $duedate);
            } else {
                $isopen = ($edusignment->allowsubmissionsfromdate <= $time);
            }
        }
        if ($isopen) {
            $edusignmentids[] = $edusignment->id;
        }
    }

    if (empty($edusignmentids)) {
        // No edusignments to look at - we're done.
        return true;
    }

    // Definitely something to print, now include the constants we need.
    require_once($CFG->dirroot . '/mod/edusign/locallib.php');

    $strduedate = get_string('duedate', 'edusign');
    $strcutoffdate = get_string('nosubmissionsacceptedafter', 'edusign');
    $strnolatesubmissions = get_string('nolatesubmissions', 'edusign');
    $strduedateno = get_string('duedateno', 'edusign');
    $stredusignment = get_string('modulename', 'edusign');

    // We do all possible database work here *outside* of the loop to ensure this scales.
    list($sqledusignmentids, $edusignmentidparams) = $DB->get_in_or_equal($edusignmentids);

    $mysubmissions = null;
    $unmarkedsubmissions = null;

    foreach ($edusignments as $edusignment) {
        // Do not show edusignments that are not open.
        if (!in_array($edusignment->id, $edusignmentids)) {
            continue;
        }

        $context = context_module::instance($edusignment->coursemodule);
        $test = has_capability('mod/assign:submit', $context, null, false);
        // Does the submission status of the edusignment require notification?
        if (has_capability('mod/assign:submit', $context, null, false)) {
            // Does the submission status of the edusignment require notification?
            $submitdetails = edusign_get_mysubmission_details_for_print_overview(
                    $mysubmissions,
                    $sqledusignmentids,
                    $edusignmentidparams,
                    $edusignment
            );
        } else {
            $submitdetails = false;
        }

        if (has_capability('mod/assign:grade', $context, null, false)) {
            // Does the grading status of the edusignment require notification ?
            $gradedetails = edusign_get_grade_details_for_print_overview(
                    $unmarkedsubmissions,
                    $sqledusignmentids,
                    $edusignmentidparams,
                    $edusignment,
                    $context
            );
        } else {
            $gradedetails = false;
        }

        if (empty($submitdetails) && empty($gradedetails)) {
            // There is no need to display this edusignment as there is nothing to notify.
            continue;
        }

        $dimmedclass = '';
        if (!$edusignment->visible) {
            $dimmedclass = ' class="dimmed"';
        }
        $href = $CFG->wwwroot . '/mod/edusign/view.php?id=' . $edusignment->coursemodule;
        $basestr = '<div class="edusign overview">' .
                '<div class="name">' .
                $stredusignment . ': ' .
                '<a ' . $dimmedclass .
                'title="' . $stredusignment . '" ' .
                'href="' . $href . '">' .
                format_string($edusignment->name) .
                '</a></div>';
        if ($edusignment->duedate) {
            $userdate = userdate($edusignment->duedate);
            $basestr .= '<div class="info">' . $strduedate . ': ' . $userdate . '</div>';
        } else {
            $basestr .= '<div class="info">' . $strduedateno . '</div>';
        }
        if ($edusignment->cutoffdate) {
            if ($edusignment->cutoffdate == $edusignment->duedate) {
                $basestr .= '<div class="info">' . $strnolatesubmissions . '</div>';
            } else {
                $userdate = userdate($edusignment->cutoffdate);
                $basestr .= '<div class="info">' . $strcutoffdate . ': ' . $userdate . '</div>';
            }
        }

        // Show only relevant information.
        if (!empty($submitdetails)) {
            $basestr .= $submitdetails;
        }

        if (!empty($gradedetails)) {
            $basestr .= $gradedetails;
        }
        $basestr .= '</div>';

        if (empty($htmlarray[$edusignment->course]['edusign'])) {
            $htmlarray[$edusignment->course]['edusign'] = $basestr;
        } else {
            $htmlarray[$edusignment->course]['edusign'] .= $basestr;
        }
    }
    return true;
}

/**
 * This api generates html to be displayed to students in print overview section, related to their submission status of the given
 * edusignment.
 *
 * @param array $mysubmissions list of submissions of current user indexed by edusignment id.
 * @param string $sqledusignmentids sql clause used to filter open edusignments.
 * @param array $edusignmentidparams sql params used to filter open edusignments.
 * @param stdClass $edusignment current edusignment
 *
 * @return bool|string html to display , false if nothing needs to be displayed.
 * @throws coding_exception
 * @deprecated since 3.3
 * @todo The final deprecation of this function will take place in Moodle 3.7 - see MDL-57487.
 */
function edusign_get_mysubmission_details_for_print_overview(
        &$mysubmissions,
        $sqledusignmentids,
        $edusignmentidparams,
        $edusignment
) {
    global $USER, $DB;

    debugging('The function edusign_get_mysubmission_details_for_print_overview() is now deprecated.', DEBUG_DEVELOPER);

    if ($edusignment->nosubmissions) {
        // Offline edusignment. No need to display alerts for offline edusignments.
        return false;
    }

    $strnotsubmittedyet = get_string('notsubmittedyet', 'edusign');

    if (!isset($mysubmissions)) {
        // Get all user submissions, indexed by edusignment id.
        $dbparams = array_merge(array($USER->id), $edusignmentidparams, array($USER->id));
        $mysubmissions = $DB->get_records_sql('SELECT a.id AS edusignment,
                                                      a.nosubmissions AS nosubmissions,
                                                      g.timemodified AS timemarked,
                                                      g.grader AS grader,
                                                      g.grade AS grade,
                                                      s.status AS status
                                                 FROM {edusign} a, {edusign_submission} s
                                            LEFT JOIN {edusign_grades} g ON
                                                      g.edusignment = s.edusignment AND
                                                      g.userid = ? AND
                                                      g.attemptnumber = s.attemptnumber
                                                WHERE a.id ' . $sqledusignmentids . ' AND
                                                      s.latest = 1 AND
                                                      s.edusignment = a.id AND
                                                      s.userid = ?', $dbparams);
    }

    $submitdetails = '';
    $submitdetails .= '<div class="details">';
    $submitdetails .= get_string('mysubmission', 'edusign');
    $submission = false;

    if (isset($mysubmissions[$edusignment->id])) {
        $submission = $mysubmissions[$edusignment->id];
    }

    if ($submission && $submission->status == EDUSIGN_SUBMISSION_STATUS_SUBMITTED) {
        // A valid submission already exists, no need to notify students about this.
        return false;
    }

    // We need to show details only if a valid submission doesn't exist.
    if (!$submission ||
            !$submission->status ||
            $submission->status == EDUSIGN_SUBMISSION_STATUS_DRAFT ||
            $submission->status == EDUSIGN_SUBMISSION_STATUS_NEW
    ) {
        $submitdetails .= $strnotsubmittedyet;
    } else {
        $submitdetails .= get_string('submissionstatus_' . $submission->status, 'edusign');
    }
    if ($edusignment->markingworkflow) {
        $workflowstate = $DB->get_field('edusign_user_flags', 'workflowstate', array('edusignment' => $edusignment->id,
            'userid' => $USER->id));
        if ($workflowstate) {
            $gradingstatus = 'markingworkflowstate' . $workflowstate;
        } else {
            $gradingstatus = 'markingworkflowstate' . EDUSIGN_MARKING_WORKFLOW_STATE_NOTMARKED;
        }
    } else if (!empty($submission->grade) && $submission->grade !== null && $submission->grade >= 0) {
        $gradingstatus = EDUSIGN_GRADING_STATUS_GRADED;
    } else {
        $gradingstatus = EDUSIGN_GRADING_STATUS_NOT_GRADED;
    }
    $submitdetails .= ', ' . get_string($gradingstatus, 'edusign');
    $submitdetails .= '</div>';
    return $submitdetails;
}

/**
 * This api generates html to be displayed to teachers in print overview section, related to the grading status of the given
 * edusignment's submissions.
 *
 * @param array $unmarkedsubmissions list of submissions of that are currently unmarked indexed by edusignment id.
 * @param string $sqledusignmentids sql clause used to filter open edusignments.
 * @param array $edusignmentidparams sql params used to filter open edusignments.
 * @param stdClass $edusignment current edusignment
 * @param context $context context of the edusignment.
 *
 * @return bool|string html to display , false if nothing needs to be displayed.
 * @throws coding_exception
 * @todo The final deprecation of this function will take place in Moodle 3.7 - see MDL-57487.
 * @deprecated since 3.3
 */
function edusign_get_grade_details_for_print_overview(
        &$unmarkedsubmissions,
        $sqledusignmentids,
        $edusignmentidparams,
        $edusignment,
        $context
) {
    global $DB;

    debugging('The function edusign_get_grade_details_for_print_overview() is now deprecated.', DEBUG_DEVELOPER);

    if (!isset($unmarkedsubmissions)) {
        // Build up and array of unmarked submissions indexed by edusignment id/ userid
        // for use where the user has grading rights on edusignment.
        $dbparams = array_merge(array(EDUSIGN_SUBMISSION_STATUS_SUBMITTED), $edusignmentidparams);
        $rs = $DB->get_recordset_sql('SELECT s.edusignment as edusignment,
                                             s.userid as userid,
                                             s.id as id,
                                             s.status as status,
                                             g.timemodified as timegraded
                                        FROM {edusign_submission} s
                                   LEFT JOIN {edusign_grades} g ON
                                             s.userid = g.userid AND
                                             s.edusignment = g.edusignment AND
                                             g.attemptnumber = s.attemptnumber
                                   LEFT JOIN {edusign} a ON
                                             a.id = s.edusignment
                                       WHERE
                                             ( g.timemodified is NULL OR
                                             s.timemodified >= g.timemodified OR
                                             g.grade IS NULL OR
                                             (g.grade = -1 AND
                                             a.grade < 0)) AND
                                             s.timemodified IS NOT NULL AND
                                             s.status = ? AND
                                             s.latest = 1 AND
                                             s.edusignment ' . $sqledusignmentids, $dbparams);

        $unmarkedsubmissions = array();
        foreach ($rs as $rd) {
            $unmarkedsubmissions[$rd->edusignment][$rd->userid] = $rd->id;
        }
        $rs->close();
    }

    // Count how many people can submit.
    $submissions = 0;
    if ($students = get_enrolled_users($context, 'mod/edusign:view', 0, 'u.id')) {
        foreach ($students as $student) {
            if (isset($unmarkedsubmissions[$edusignment->id][$student->id])) {
                $submissions++;
            }
        }
    }

    if ($submissions) {
        $urlparams = array('id' => $edusignment->coursemodule, 'action' => 'grading');
        $url = new moodle_url('/mod/edusign/view.php', $urlparams);
        $gradedetails = '<div class="details">' .
                '<a href="' . $url . '">' .
                get_string('submissionsnotgraded', 'edusign', $submissions) .
                '</a></div>';
        return $gradedetails;
    } else {
        return false;
    }
}

/**
 * Print recent activity from all edusignments in a given course
 *
 * This is used by the recent activity block
 *
 * @param mixed $course the course to print activity for
 * @param bool $viewfullnames boolean to determine whether to show full names or not
 * @param int $timestart the time the rendering started
 * @return bool true if activity was printed, false otherwise.
 */
function edusign_print_recent_activity($course, $viewfullnames, $timestart) {
    global $CFG, $USER, $DB, $OUTPUT;
    require_once($CFG->dirroot . '/mod/edusign/locallib.php');

    // Do not use log table if possible, it may be huge.

    $dbparams = array($timestart, $course->id, 'edusign', EDUSIGN_SUBMISSION_STATUS_SUBMITTED);
    $namefields = user_picture::fields('u', null, 'userid');
    if (!$submissions = $DB->get_records_sql("SELECT asb.id, asb.timemodified, cm.id AS cmid, um.id as recordid,
                                                     $namefields
                                                FROM {edusign_submission} asb
                                                     JOIN {edusign} a      ON a.id = asb.edusignment
                                                     JOIN {course_modules} cm ON cm.instance = a.id
                                                     JOIN {modules} md        ON md.id = cm.module
                                                     JOIN {user} u            ON u.id = asb.userid
                                                LEFT JOIN {edusign_user_mapping} um ON um.userid = u.id AND um.edusignment = a.id
                                               WHERE asb.timemodified > ? AND
                                                     asb.latest = 1 AND
                                                     a.course = ? AND
                                                     md.name = ? AND
                                                     asb.status = ?
                                            ORDER BY asb.timemodified ASC", $dbparams)) {
        return false;
    }

    $modinfo = get_fast_modinfo($course);
    $show = array();
    $grader = array();

    $showrecentsubmissions = get_config('edusign', 'showrecentsubmissions');

    foreach ($submissions as $submission) {
        if (!array_key_exists($submission->cmid, $modinfo->get_cms())) {
            continue;
        }
        $cm = $modinfo->get_cm($submission->cmid);
        if (!$cm->uservisible) {
            continue;
        }
        if ($submission->userid == $USER->id) {
            $show[] = $submission;
            continue;
        }

        $context = context_module::instance($submission->cmid);
        // The act of submitting of edusignment may be considered private -
        // only graders will see it if specified.
        if (empty($showrecentsubmissions)) {
            if (!array_key_exists($cm->id, $grader)) {
                $grader[$cm->id] = has_capability('moodle/grade:viewall', $context);
            }
            if (!$grader[$cm->id]) {
                continue;
            }
        }

        $groupmode = groups_get_activity_groupmode($cm, $course);

        if ($groupmode == SEPARATEGROUPS &&
                !has_capability('moodle/site:accessallgroups', $context)) {
            if (isguestuser()) {
                // Shortcut - guest user does not belong into any group.
                continue;
            }

            // This will be slow - show only users that share group with me in this cm.
            if (!$modinfo->get_groups($cm->groupingid)) {
                continue;
            }
            $usersgroups = groups_get_all_groups($course->id, $submission->userid, $cm->groupingid);
            if (is_array($usersgroups)) {
                $usersgroups = array_keys($usersgroups);
                $intersect = array_intersect($usersgroups, $modinfo->get_groups($cm->groupingid));
                if (empty($intersect)) {
                    continue;
                }
            }
        }
        $show[] = $submission;
    }

    if (empty($show)) {
        return false;
    }

    echo $OUTPUT->heading(get_string('newsubmissions', 'edusign') . ':', 3);

    foreach ($show as $submission) {
        $cm = $modinfo->get_cm($submission->cmid);
        $context = context_module::instance($submission->cmid);
        $edusign = new edusign($context, $cm, $cm->course);
        $link = $CFG->wwwroot . '/mod/edusign/view.php?id=' . $cm->id;
        // Obscure first and last name if blind marking enabled.
        if ($edusign->is_blind_marking()) {
            $submission->firstname = get_string('participant', 'mod_edusign');
            if (empty($submission->recordid)) {
                $submission->recordid = $edusign->get_uniqueid_for_user($submission->userid);
            }
            $submission->lastname = $submission->recordid;
        }
        print_recent_activity_note(
                $submission->timemodified,
                $submission,
                $cm->name,
                $link,
                false,
                $viewfullnames
        );
    }

    return true;
}

/**
 * Returns all edusignments since a given time.
 *
 * @param array $activities The activity information is returned in this array
 * @param int $index The current index in the activities array
 * @param int $timestart The earliest activity to show
 * @param int $courseid Limit the search to this course
 * @param int $cmid The course module id
 * @param int $userid Optional user id
 * @param int $groupid Optional group id
 * @return void
 */
function edusign_get_recent_mod_activity(
        &$activities,
        &$index,
        $timestart,
        $courseid,
        $cmid,
        $userid = 0,
        $groupid = 0
) {
    global $CFG, $COURSE, $USER, $DB;

    require_once($CFG->dirroot . '/mod/edusign/locallib.php');

    if ($COURSE->id == $courseid) {
        $course = $COURSE;
    } else {
        $course = $DB->get_record('course', array('id' => $courseid));
    }

    $modinfo = get_fast_modinfo($course);

    $cm = $modinfo->get_cm($cmid);
    $params = array();
    if ($userid) {
        $userselect = 'AND u.id = :userid';
        $params['userid'] = $userid;
    } else {
        $userselect = '';
    }

    if ($groupid) {
        $groupselect = 'AND gm.groupid = :groupid';
        $groupjoin = 'JOIN {groups_members} gm ON  gm.userid=u.id';
        $params['groupid'] = $groupid;
    } else {
        $groupselect = '';
        $groupjoin = '';
    }

    $params['cminstance'] = $cm->instance;
    $params['timestart'] = $timestart;
    $params['submitted'] = EDUSIGN_SUBMISSION_STATUS_SUBMITTED;

    $userfields = user_picture::fields('u', null, 'userid');

    if (!$submissions = $DB->get_records_sql('SELECT asb.id, asb.timemodified, ' .
            $userfields .
            '  FROM {edusign_submission} asb
                                                JOIN {edusign} a ON a.id = asb.edusignment
                                                JOIN {user} u ON u.id = asb.userid ' .
            $groupjoin .
            '  WHERE asb.timemodified > :timestart AND
                                                     asb.status = :submitted AND
                                                     a.id = :cminstance
                                                     ' . $userselect . ' ' . $groupselect .
            ' ORDER BY asb.timemodified ASC', $params)) {
        return;
    }

    $groupmode = groups_get_activity_groupmode($cm, $course);
    $cmcontext = context_module::instance($cm->id);
    $grader = has_capability('moodle/grade:viewall', $cmcontext);
    $accessallgroups = has_capability('moodle/site:accessallgroups', $cmcontext);
    $viewfullnames = has_capability('moodle/site:viewfullnames', $cmcontext);

    $showrecentsubmissions = get_config('edusign', 'showrecentsubmissions');
    $show = array();
    foreach ($submissions as $submission) {
        if ($submission->userid == $USER->id) {
            $show[] = $submission;
            continue;
        }
        // The act of submitting of edusignment may be considered private -
        // only graders will see it if specified.
        if (empty($showrecentsubmissions)) {
            if (!$grader) {
                continue;
            }
        }

        if ($groupmode == SEPARATEGROUPS and !$accessallgroups) {
            if (isguestuser()) {
                // Shortcut - guest user does not belong into any group.
                continue;
            }

            // This will be slow - show only users that share group with me in this cm.
            if (!$modinfo->get_groups($cm->groupingid)) {
                continue;
            }
            $usersgroups = groups_get_all_groups($course->id, $submission->userid, $cm->groupingid);
            if (is_array($usersgroups)) {
                $usersgroups = array_keys($usersgroups);
                $intersect = array_intersect($usersgroups, $modinfo->get_groups($cm->groupingid));
                if (empty($intersect)) {
                    continue;
                }
            }
        }
        $show[] = $submission;
    }

    if (empty($show)) {
        return;
    }

    if ($grader) {
        require_once($CFG->libdir . '/gradelib.php');
        $userids = array();
        foreach ($show as $id => $submission) {
            $userids[] = $submission->userid;
        }
        $grades = grade_get_grades($courseid, 'mod', 'edusign', $cm->instance, $userids);
    }

    $aname = format_string($cm->name, true);
    foreach ($show as $submission) {
        $activity = new stdClass();

        $activity->type = 'edusign';
        $activity->cmid = $cm->id;
        $activity->name = $aname;
        $activity->sectionnum = $cm->sectionnum;
        $activity->timestamp = $submission->timemodified;
        $activity->user = new stdClass();
        if ($grader) {
            $activity->grade = $grades->items[0]->grades[$submission->userid]->str_long_grade;
        }

        $userfields = explode(',', user_picture::fields());
        foreach ($userfields as $userfield) {
            if ($userfield == 'id') {
                // Aliased in SQL above.
                $activity->user->{$userfield} = $submission->userid;
            } else {
                $activity->user->{$userfield} = $submission->{$userfield};
            }
        }
        $activity->user->fullname = fullname($submission, $viewfullnames);

        $activities[$index++] = $activity;
    }

    return;
}

/**
 * Print recent activity from all edusignments in a given course
 *
 * This is used by course/recent.php
 *
 * @param stdClass $activity
 * @param int $courseid
 * @param bool $detail
 * @param array $modnames
 */
function edusign_print_recent_mod_activity($activity, $courseid, $detail, $modnames) {
    global $CFG, $OUTPUT;

    echo '<table border="0" cellpadding="3" cellspacing="0" class="edusignment-recent">';

    echo '<tr><td class="userpicture" valign="top">';
    echo $OUTPUT->user_picture($activity->user);
    echo '</td><td>';

    if ($detail) {
        $modname = $modnames[$activity->type];
        echo '<div class="title">';
        echo $OUTPUT->image_icon('icon', $modname, 'edusign');
        echo '<a href="' . $CFG->wwwroot . '/mod/edusign/view.php?id=' . $activity->cmid . '">';
        echo $activity->name;
        echo '</a>';
        echo '</div>';
    }

    if (isset($activity->grade)) {
        echo '<div class="grade">';
        echo get_string('grade') . ': ';
        echo $activity->grade;
        echo '</div>';
    }

    echo '<div class="user">';
    echo "<a href=\"$CFG->wwwroot/user/view.php?id={$activity->user->id}&amp;course=$courseid\">";
    echo "{$activity->user->fullname}</a>  - " . userdate($activity->timestamp);
    echo '</div>';

    echo '</td></tr></table>';
}

/**
 * Checks if a scale is being used by an edusignment.
 *
 * This is used by the backup code to decide whether to back up a scale
 *
 * @param int $edusignmentid
 * @param int $scaleid
 * @return boolean True if the scale is used by the edusignment
 */
function edusign_scale_used($edusignmentid, $scaleid) {
    global $DB;

    $return = false;
    $rec = $DB->get_record('edusign', array('id' => $edusignmentid, 'grade' => -$scaleid));

    if (!empty($rec) && !empty($scaleid)) {
        $return = true;
    }

    return $return;
}

/**
 * Checks if scale is being used by any instance of edusignment
 *
 * This is used to find out if scale used anywhere
 *
 * @param int $scaleid
 * @return boolean True if the scale is used by any edusignment
 */
function edusign_scale_used_anywhere($scaleid) {
    global $DB;

    if ($scaleid and $DB->record_exists('edusign', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * List the actions that correspond to a view of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = 'r' and edulevel = LEVEL_PARTICIPATING will
 *       be considered as view action.
 *
 * @return array
 */
function edusign_get_view_actions() {
    return array('view submission', 'view feedback');
}

/**
 * List the actions that correspond to a post of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = ('c' || 'u' || 'd') and edulevel = LEVEL_PARTICIPATING
 *       will be considered as post action.
 *
 * @return array
 */
function edusign_get_post_actions() {
    return array('upload', 'submit', 'submit for grading');
}


/**
 * Returns all other capabilities used by this module.
 *
 * @return array Array of capability strings
 */
function edusign_get_extra_capabilities() {
    return ['gradereport/grader:view', 'moodle/grade:viewall'];
}

/**
 * Create grade item for given edusignment.
 *
 * @param stdClass $edusign record with extra cmidnumber
 * @param array $grades optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return int 0 if ok, error code otherwise
 */
function edusign_grade_item_update($edusign, $grades = null) {
    /*global $CFG;
    require_once($CFG->libdir . '/gradelib.php');

    if (!isset($edusign->courseid)) {
        $edusign->courseid = $edusign->course;
    }

    $params = array('itemname' => $edusign->name, 'idnumber' => $edusign->cmidnumber);

    // Check if feedback plugin for gradebook is enabled, if yes then
    // gradetype = GRADE_TYPE_TEXT else GRADE_TYPE_NONE.
    $gradefeedbackenabled = false;

    if (isset($edusign->gradefeedbackenabled)) {
        $gradefeedbackenabled = $edusign->gradefeedbackenabled;
    } else if ($edusign->grade == 0) { // Grade feedback is needed only when grade == 0.
        require_once($CFG->dirroot . '/mod/edusign/locallib.php');
        $mod = get_coursemodule_from_instance('edusign', $edusign->id, $edusign->courseid);
        $cm = context_module::instance($mod->id);
        $edusignment = new edusign($cm, null, null);
        $gradefeedbackenabled = $edusignment->is_gradebook_feedback_enabled();
    }

    if ($edusign->grade > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax'] = $edusign->grade;
        $params['grademin'] = 0;
    } else if ($edusign->grade < 0) {
        $params['gradetype'] = GRADE_TYPE_SCALE;
        $params['scaleid'] = -$edusign->grade;
    } else if ($gradefeedbackenabled) {
        // $edusign->grade == 0 and feedback enabled.
        $params['gradetype'] = GRADE_TYPE_TEXT;
    } else {
        // $edusign->grade == 0 and no feedback enabled.
        $params['gradetype'] = GRADE_TYPE_NONE;
    }

    if ($grades === 'reset') {
        $params['reset'] = true;
        $grades = null;
    }

    return grade_update(
            'mod/edusign',
            $edusign->courseid,
            'mod',
            'edusign',
            $edusign->id,
            0,
            $grades,
            $params
    );*/
}

/**
 * Return grade for given user or all users.
 *
 * @param stdClass $edusign record of edusign with an additional cmidnumber
 * @param int $userid optional user id, 0 means all users
 * @return array array of grades, false if none
 */
function edusign_get_user_grades($edusign, $userid = 0) {
    global $CFG;

    require_once($CFG->dirroot . '/mod/edusign/locallib.php');

    $cm = get_coursemodule_from_instance('edusign', $edusign->id, 0, false, MUST_EXIST);
    $context = context_module::instance($cm->id);
    $edusignment = new edusign($context, null, null);
    $edusignment->set_instance($edusign);
    return $edusignment->get_user_grades_for_gradebook($userid);
}

/**
 * Update activity grades.
 *
 * @param stdClass $edusign database record
 * @param int $userid specific user only, 0 means all
 * @param bool $nullifnone - not used
 */
function edusign_update_grades($edusign, $userid = 0, $nullifnone = true) {
    global $CFG;
    require_once($CFG->libdir . '/gradelib.php');

    if ($edusign->grade == 0) {
        edusign_grade_item_update($edusign);
    } else if ($grades = edusign_get_user_grades($edusign, $userid)) {
        foreach ($grades as $k => $v) {
            if ($v->rawgrade == -1) {
                $grades[$k]->rawgrade = null;
            }
        }
        edusign_grade_item_update($edusign, $grades);
    } else {
        edusign_grade_item_update($edusign);
    }
}

/**
 * List the file areas that can be browsed.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array
 */
function edusign_get_file_areas($course, $cm, $context) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/edusign/locallib.php');

    $areas = array(EDUSIGN_INTROATTACHMENT_FILEAREA => get_string('introattachments', 'mod_edusign'));

    $edusignment = new edusign($context, $cm, $course);
    foreach ($edusignment->get_submission_plugins() as $plugin) {
        if ($plugin->is_visible()) {
            $pluginareas = $plugin->get_file_areas();

            if ($pluginareas) {
                $areas = array_merge($areas, $pluginareas);
            }
        }
    }
    foreach ($edusignment->get_feedback_plugins() as $plugin) {
        if ($plugin->is_visible()) {
            $pluginareas = $plugin->get_file_areas();

            if ($pluginareas) {
                $areas = array_merge($areas, $pluginareas);
            }
        }
    }

    return $areas;
}

/**
 * File browsing support for edusign module.
 *
 * @param file_browser $browser
 * @param object $areas
 * @param object $course
 * @param object $cm
 * @param object $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return object file_info instance or null if not found
 */
function edusign_get_file_info(
        $browser,
        $areas,
        $course,
        $cm,
        $context,
        $filearea,
        $itemid,
        $filepath,
        $filename
) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/edusign/locallib.php');

    if ($context->contextlevel != CONTEXT_MODULE) {
        return null;
    }

    $urlbase = $CFG->wwwroot . '/pluginfile.php';
    $fs = get_file_storage();
    $filepath = is_null($filepath) ? '/' : $filepath;
    $filename = is_null($filename) ? '.' : $filename;

    // Need to find where this belongs to.
    $edusignment = new edusign($context, $cm, $course);
    if ($filearea === EDUSIGN_INTROATTACHMENT_FILEAREA) {
        if (!has_capability('moodle/course:managefiles', $context)) {
            // Students can not peak here!
            return null;
        }
        if (!($storedfile = $fs->get_file(
                $edusignment->get_context()->id,
                'mod_edusign',
                $filearea,
                0,
                $filepath,
                $filename
        ))) {
            return null;
        }
        return new file_info_stored(
                $browser,
                $edusignment->get_context(),
                $storedfile,
                $urlbase,
                $filearea,
                $itemid,
                true,
                true,
                false
        );
    }

    $pluginowner = null;
    foreach ($edusignment->get_submission_plugins() as $plugin) {
        if ($plugin->is_visible()) {
            $pluginareas = $plugin->get_file_areas();

            if (array_key_exists($filearea, $pluginareas)) {
                $pluginowner = $plugin;
                break;
            }
        }
    }
    if (!$pluginowner) {
        foreach ($edusignment->get_feedback_plugins() as $plugin) {
            if ($plugin->is_visible()) {
                $pluginareas = $plugin->get_file_areas();

                if (array_key_exists($filearea, $pluginareas)) {
                    $pluginowner = $plugin;
                    break;
                }
            }
        }
    }

    if (!$pluginowner) {
        return null;
    }

    $result = $pluginowner->get_file_info($browser, $filearea, $itemid, $filepath, $filename);
    return $result;
}

/**
 * Prints the complete info about a user's interaction with an edusignment.
 *
 * @param stdClass $course
 * @param stdClass $user
 * @param stdClass $coursemodule
 * @param stdClass $edusign the database edusign record
 *
 * This prints the submission summary and feedback summary for this student.
 */
function edusign_user_complete($course, $user, $coursemodule, $edusign) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/edusign/locallib.php');

    $context = context_module::instance($coursemodule->id);

    $edusignment = new edusign($context, $coursemodule, $course);

    echo $edusignment->view_student_summary($user, false);
}

/**
 * Rescale all grades for this activity and push the new grades to the gradebook.
 *
 * @param stdClass $course Course db record
 * @param stdClass $cm Course module db record
 * @param float $oldmin
 * @param float $oldmax
 * @param float $newmin
 * @param float $newmax
 */
function edusign_rescale_activity_grades($course, $cm, $oldmin, $oldmax, $newmin, $newmax) {
    global $DB;

    if ($oldmax <= $oldmin) {
        // Grades cannot be scaled.
        return false;
    }
    $scale = ($newmax - $newmin) / ($oldmax - $oldmin);
    if (($newmax - $newmin) <= 1) {
        // We would lose too much precision, lets bail.
        return false;
    }

    $params = array(
            'p1' => $oldmin,
            'p2' => $scale,
            'p3' => $newmin,
            'a' => $cm->instance
    );

    // Only rescale grades that are greater than or equal to 0. Anything else is a special value.
    $sql = 'UPDATE {edusign_grades} set grade = (((grade - :p1) * :p2) + :p3) where edusignment = :a and grade >= 0';
    $dbupdate = $DB->execute($sql, $params);
    if (!$dbupdate) {
        return false;
    }

    // Now re-push all grades to the gradebook.
    $dbparams = array('id' => $cm->instance);
    $edusign = $DB->get_record('edusign', $dbparams);
    $edusign->cmidnumber = $cm->idnumber;

    edusign_update_grades($edusign);

    return true;
}

/**
 * Print the grade information for the edusignment for this user.
 *
 * @param stdClass $course
 * @param stdClass $user
 * @param stdClass $coursemodule
 * @param stdClass $edusignment
 */
function edusign_user_outline($course, $user, $coursemodule, $edusignment) {
    global $CFG;
    require_once($CFG->libdir . '/gradelib.php');
    require_once($CFG->dirroot . '/grade/grading/lib.php');

    $gradinginfo = grade_get_grades(
            $course->id,
            'mod',
            'edusign',
            $edusignment->id,
            $user->id
    );

    $gradingitem = $gradinginfo->items[0];
    $gradebookgrade = $gradingitem->grades[$user->id];

    if (empty($gradebookgrade->str_long_grade)) {
        return null;
    }
    $result = new stdClass();
    if (!$gradingitem->hidden || has_capability('moodle/grade:viewhidden', context_course::instance($course->id))) {
        $result->info = get_string('outlinegrade', 'edusign', $gradebookgrade->str_long_grade);
    } else {
        $result->info = get_string('grade') . ': ' . get_string('hidden', 'grades');
    }
    $result->time = $gradebookgrade->dategraded;

    return $result;
}

/**
 * Obtains the automatic completion state for this module based on any conditions
 * in edusign settings.
 *
 * @param object $course Course
 * @param object $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not, $type if conditions not set.
 */
function edusign_get_completion_state($course, $cm, $userid, $type) {
    global $CFG, $DB;
    require_once($CFG->dirroot . '/mod/edusign/locallib.php');

    $edusign = new edusign(null, $cm, $course);

    // If completion option is enabled, evaluate it and return true/false.
    if ($edusign->get_instance()->completionsubmit) {
        if ($edusign->get_instance()->teamsubmission) {
            $submission = $edusign->get_group_submission($userid, 0, false);
        } else {
            $submission = $edusign->get_user_submission($userid, false);
        }
        return $submission && $submission->status == EDUSIGN_SUBMISSION_STATUS_SUBMITTED;
    } else {
        // Completion option is not enabled so just return $type.
        return $type;
    }
}

/**
 * Serves intro attachment files.
 *
 * @param mixed $course course or id of the course
 * @param mixed $cm course module or id of the course module
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - just send the file
 */
function edusign_pluginfile(
        $course,
        $cm,
        context $context,
        $filearea,
        $args,
        $forcedownload,
        array $options = array()
) {
    global $CFG;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_login($course, false, $cm);
    if (!has_capability('mod/edusign:view', $context)) {
        return false;
    }

    require_once($CFG->dirroot . '/mod/edusign/locallib.php');
    $edusign = new edusign($context, $cm, $course);

    if ($filearea !== EDUSIGN_INTROATTACHMENT_FILEAREA) {
        return false;
    }
    if (!$edusign->show_intro()) {
        return false;
    }

    $itemid = (int) array_shift($args);
    if ($itemid != 0) {
        return false;
    }

    $relativepath = implode('/', $args);

    $fullpath = "/{$context->id}/mod_edusign/$filearea/$itemid/$relativepath";

    $fs = get_file_storage();
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }
    send_stored_file($file, 0, 0, $forcedownload, $options);
}

/**
 * Serve the grading panel as a fragment.
 *
 * @param array $args List of named arguments for the fragment loader.
 * @return string
 */
function mod_edusign_output_fragment_gradingpanel($args) {
    global $CFG;

    $context = $args['context'];

    if ($context->contextlevel != CONTEXT_MODULE) {
        return null;
    }
    require_once($CFG->dirroot . '/mod/edusign/locallib.php');
    $edusign = new edusign($context, null, null);

    $userid = clean_param($args['userid'], PARAM_INT);
    $attemptnumber = clean_param($args['attemptnumber'], PARAM_INT);
    $formdata = array();
    if (!empty($args['jsonformdata'])) {
        $serialiseddata = json_decode($args['jsonformdata']);
        parse_str($serialiseddata, $formdata);
    }
    $viewargs = array(
            'userid' => $userid,
            'attemptnumber' => $attemptnumber,
            'formdata' => $formdata
    );

    return $edusign->view('gradingpanel', $viewargs);
}

/**
 * Check if the module has any update that affects the current user since a given time.
 *
 * @param cm_info $cm course module data
 * @param int $from the time to check updates from
 * @param array $filter if we need to check only specific updates
 * @return stdClass an object with the different type of areas indicating if they were updated or not
 * @since Moodle 3.2
 */
function edusign_check_updates_since(cm_info $cm, $from, $filter = array()) {
    global $DB, $USER, $CFG;
    require_once($CFG->dirroot . '/mod/edusign/locallib.php');

    $updates = new stdClass();
    $updates = course_check_module_updates_since($cm, $from, array(EDUSIGN_INTROATTACHMENT_FILEAREA), $filter);

    // Check if there is a new submission by the user or new grades.
    $select = 'edusignment = :id AND userid = :userid AND (timecreated > :since1 OR timemodified > :since2)';
    $params = array('id' => $cm->instance, 'userid' => $USER->id, 'since1' => $from, 'since2' => $from);
    $updates->submissions = (object) array('updated' => false);
    $submissions = $DB->get_records_select('edusign_submission', $select, $params, '', 'id');
    if (!empty($submissions)) {
        $updates->submissions->updated = true;
        $updates->submissions->itemids = array_keys($submissions);
    }

    $updates->grades = (object) array('updated' => false);
    $grades = $DB->get_records_select('edusign_grades', $select, $params, '', 'id');
    if (!empty($grades)) {
        $updates->grades->updated = true;
        $updates->grades->itemids = array_keys($grades);
    }

    // Now, teachers should see other students updates.
    if (has_capability('mod/edusign:viewsignings', $cm->context)) {
        $params = array('id' => $cm->instance, 'since1' => $from, 'since2' => $from);
        $select = 'edusignment = :id AND (timecreated > :since1 OR timemodified > :since2)';

        if (groups_get_activity_groupmode($cm) == SEPARATEGROUPS) {
            $groupusers = array_keys(groups_get_activity_shared_group_members($cm));
            if (empty($groupusers)) {
                return $updates;
            }
            list($insql, $inparams) = $DB->get_in_or_equal($groupusers, SQL_PARAMS_NAMED);
            $select .= ' AND userid ' . $insql;
            $params = array_merge($params, $inparams);
        }

        $updates->usersubmissions = (object) array('updated' => false);
        $submissions = $DB->get_records_select('edusign_submission', $select, $params, '', 'id');
        if (!empty($submissions)) {
            $updates->usersubmissions->updated = true;
            $updates->usersubmissions->itemids = array_keys($submissions);
        }

        $updates->usergrades = (object) array('updated' => false);
        $grades = $DB->get_records_select('edusign_grades', $select, $params, '', 'id');
        if (!empty($grades)) {
            $updates->usergrades->updated = true;
            $updates->usergrades->itemids = array_keys($grades);
        }
    }

    return $updates;
}

/**
 * Is the event visible?
 *
 * This is used to determine global visibility of an event in all places throughout Moodle.
 *
 * @param calendar_event $event
 * @param int $userid User id to use for all capability checks, etc. Set to 0 for current user (default).
 * @return bool Returns true if the event is visible to the current user, false otherwise.
 */
function mod_edusign_core_calendar_is_event_visible(calendar_event $event, $userid = 0) {
    global $CFG, $USER;

    require_once($CFG->dirroot . '/mod/edusign/locallib.php');

    if (empty($userid)) {
        $userid = $USER->id;
    }

    $cm = get_fast_modinfo($event->courseid, $userid)->instances['edusign'][$event->instance];
    $context = context_module::instance($cm->id);

    $edusign = new edusign($context, $cm, null);

    return true;
}

/**
 * This function receives a calendar event and returns the action associated with it, or null if there is none.
 *
 * This is used by block_myoverview in order to display the event appropriately. If null is returned then the event
 * is not displayed on the block.
 *
 * @param calendar_event $event
 * @param \core_calendar\action_factory $factory
 * @param int $userid User id to use for all capability checks, etc. Set to 0 for current user (default).
 * @return \core_calendar\local\event\entities\action_interface|null
 */
function mod_edusign_core_calendar_provide_event_action(calendar_event $event, \core_calendar\action_factory $factory,
    $userid = 0) {

    global $CFG, $USER;

    require_once($CFG->dirroot . '/mod/edusign/locallib.php');

    if (empty($userid)) {
        $userid = $USER->id;
    }

    $cm = get_fast_modinfo($event->courseid, $userid)->instances['edusign'][$event->instance];
    $context = context_module::instance($cm->id);

    $edusign = new edusign($context, $cm, null);

    // Apply overrides.
    $edusign->update_effective_access($userid);
    $usersubmission = $edusign->get_user_submission($userid, false);
    if ($usersubmission && $usersubmission->status === EDUSIGN_SUBMISSION_STATUS_SUBMITTED) {
        // The user has already submitted.
        // We do not want to change the text to edit the submission, we want to remove the event from the Dashboard entirely.
        return null;
    }

    $participant = $edusign->get_participant($userid);

    if (!$participant) {
        // If the user is not a participant in the edusignment then they have
        // no action to take. This will filter out the events for teachers.
        return null;
    }

    // The user has not yet submitted anything. Show the addsubmission link.
    $name = get_string('addsubmission', 'edusign');
    $url = new \moodle_url('/mod/edusign/view.php', [
        'id' => $cm->id,
        'action' => 'editsubmission'
    ]);
    $itemcount = 1;
    $actionable = $edusign->is_any_submission_plugin_enabled() && $edusign->can_edit_submission($userid, $userid);

    return $factory->create_instance(
        $name,
        $url,
        $itemcount,
        $actionable
    );
}

/**
 * This function calculates the minimum and maximum cutoff values for the timestart of
 * the given event.
 *
 * It will return an array with two values, the first being the minimum cutoff value and
 * the second being the maximum cutoff value. Either or both values can be null, which
 * indicates there is no minimum or maximum, respectively.
 *
 * If a cutoff is required then the function must return an array containing the cutoff
 * timestamp and error string to display to the user if the cutoff value is violated.
 *
 * A minimum and maximum cutoff return value will look like:
 * [
 *     [1505704373, 'The due date must be after the sbumission start date'],
 *     [1506741172, 'The due date must be before the cutoff date']
 * ]
 *
 * If the event does not have a valid timestart range then [false, false] will
 * be returned.
 *
 * @param calendar_event $event The calendar event to get the time range for
 * @param stdClass $instance The module instance to get the range from
 * @return array
 */
function mod_edusign_core_calendar_get_valid_event_timestart_range(\calendar_event $event, \stdClass $instance) {
    global $CFG;

    require_once($CFG->dirroot . '/mod/edusign/locallib.php');

    $courseid = $event->courseid;
    $modulename = $event->modulename;
    $instanceid = $event->instance;
    $coursemodule = get_fast_modinfo($courseid)->instances[$modulename][$instanceid];
    $context = context_module::instance($coursemodule->id);
    $edusign = new edusign($context, null, null);
    $edusign->set_instance($instance);

    return $edusign->get_valid_calendar_event_timestart_range($event);
}

/**
 * This function will update the edusign module according to the
 * event that has been modified.
 *
 * @param \calendar_event $event
 * @param stdClass $instance The module instance to get the range from
 * @throws \moodle_exception
 */
function mod_edusign_core_calendar_event_timestart_updated(\calendar_event $event, \stdClass $instance) {
    global $CFG, $DB;

    require_once($CFG->dirroot . '/mod/edusign/locallib.php');

    if (empty($event->instance) || $event->modulename != 'edusign') {
        return;
    }

    if ($instance->id != $event->instance) {
        return;
    }

    if (!in_array($event->eventtype, [EDUSIGN_EVENT_TYPE_DUE])) {
        return;
    }

    $courseid = $event->courseid;
    $modulename = $event->modulename;
    $instanceid = $event->instance;
    $modified = false;
    $coursemodule = get_fast_modinfo($courseid)->instances[$modulename][$instanceid];
    $context = context_module::instance($coursemodule->id);

    // The user does not have the capability to modify this activity.
    if (!has_capability('moodle/course:manageactivities', $context)) {
        return;
    }

    $edusign = new edusign($context, $coursemodule, null);
    $edusign->set_instance($instance);

    if ($event->eventtype == EDUSIGN_EVENT_TYPE_DUE) {
        // This check is in here because due date events are currently
        // the only events that can be overridden, so we can save a DB
        // query if we don't bother checking other events.
        if ($edusign->is_override_calendar_event($event)) {
            // This is an override event so we should ignore it.
            return;
        }

        $newduedate = $event->timestart;

        if ($newduedate != $instance->duedate) {
            $instance->duedate = $newduedate;
            $modified = true;
        }
    }

    if ($modified) {
        $instance->timemodified = time();
        // Persist the edusign instance changes.
        $DB->update_record('edusign', $instance);
        $edusign->update_calendar($coursemodule->id);
        $event = \core\event\course_module_updated::create_from_cm($coursemodule, $context);
        $event->trigger();
    }
}
