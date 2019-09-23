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
 * Renderable that initialises the grading "app".
 *
 * @package    mod_edusign
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_edusign\output;

defined('MOODLE_INTERNAL') || die();

use renderer_base;
use renderable;
use templatable;
use stdClass;

/**
 * Grading app renderable.
 *
 * @package    mod_edusign
 * @since      Moodle 3.1
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grading_app implements templatable, renderable {

    /**
     * @var $userid - The initial user id.
     */
    public $userid = 0;

    /**
     * @var $groupid - The initial group id.
     */
    public $groupid = 0;

    /**
     * @var $edusignment - The edusignment instance.
     */
    public $edusignment = null;

    /**
     * Constructor for this renderable.
     *
     * @param int $userid The user we will open the grading app too.
     * @param int $groupid If groups are enabled this is the current course group.
     * @param edusign $edusignment The edusignment class
     */
    public function __construct($userid, $groupid, $edusignment) {
        $this->userid = $userid;
        $this->groupid = $groupid;
        $this->edusignment = $edusignment;
        $this->participants = $edusignment->list_participants_with_filter_status_and_group($groupid);
        if (!$this->userid && count($this->participants)) {
            $this->userid = reset($this->participants)->id;
        }
    }

    /**
     * Export this class data as a flat list for rendering in a template.
     *
     * @param renderer_base $output The current page renderer.
     * @return stdClass - Flat list of exported data.
     */
    public function export_for_template(renderer_base $output) {
        global $CFG;

        $export = new stdClass();
        $export->userid = $this->userid;
        $export->edusignmentid = $this->edusignment->get_instance()->id;
        $export->cmid = $this->edusignment->get_course_module()->id;
        $export->contextid = $this->edusignment->get_context()->id;
        $export->groupid = $this->groupid;
        $export->name = $this->edusignment->get_context()->get_context_name();
        $export->courseid = $this->edusignment->get_course()->id;
        $export->participants = array();
        $num = 1;
        foreach ($this->participants as $idx => $record) {
            $user = new stdClass();
            $user->id = $record->id;
            $user->fullname = fullname($record);
            $user->requiregrading = $record->requiregrading;
            $user->grantedextension = $record->grantedextension;
            $user->submitted = $record->submitted;
            if (!empty($record->groupid)) {
                $user->groupid = $record->groupid;
                $user->groupname = $record->groupname;
            }
            if ($record->id == $this->userid) {
                $export->index = $num;
                $user->current = true;
            }
            $export->participants[] = $user;
            $num++;
        }

        $feedbackplugins = $this->edusignment->get_feedback_plugins();
        $showreview = false;
        foreach ($feedbackplugins as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible()) {
                if ($plugin->supports_review_panel()) {
                    $showreview = true;
                }
            }
        }

        $export->actiongrading = 'grading';
        $export->viewgrading = get_string('viewgrading', 'mod_edusign');

        $export->showreview = $showreview;

        $time = time();
        $export->count = count($export->participants);
        $export->coursename = $this->edusignment->get_course_context()->get_context_name();
        $export->caneditsettings = has_capability('mod/edusign:addinstance', $this->edusignment->get_context());
        $export->duedate = $this->edusignment->get_instance()->duedate;
        $export->duedatestr = userdate($this->edusignment->get_instance()->duedate);

        // Time remaining.
        $due = '';
        if ($export->duedate - $time <= 0) {
            $due = get_string('edusignmentisdue', 'edusign');
        } else {
            $due = get_string('timeremainingcolon', 'edusign', format_time($export->duedate - $time));
        }
        $export->timeremainingstr = $due;

        if ($export->duedate < $time) {
            $export->cutoffdate = $this->edusignment->get_instance()->cutoffdate;
            $cutoffdate = $export->cutoffdate;
            if ($cutoffdate) {
                if ($cutoffdate > $time) {
                    $late = get_string('latesubmissionsaccepted', 'edusign', userdate($export->cutoffdate));
                } else {
                    $late = get_string('nomoresubmissionsaccepted', 'edusign');
                }
                $export->cutoffdatestr = $late;
            }
        }

        $export->defaultsendnotifications = $this->edusignment->get_instance()->sendstudentnotifications;
        $export->rarrow = $output->rarrow();
        $export->larrow = $output->larrow();
        // List of identity fields to display (the user info will not contain any fields the user cannot view anyway).
        $export->showuseridentity = $CFG->showuseridentity;

        return $export;
    }

}
