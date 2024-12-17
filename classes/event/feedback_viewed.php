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
 * The mod_edusign feedback viewed event.
 *
 * @package    mod_edusign
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_edusign\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_edusign feedback viewed event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int edusignid: the id of the edusignment.
 * }
 *
 * @package    mod_edusign
 * @since      Moodle 2.7
 * @copyright  2014 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class feedback_viewed extends base {
    /**
     * Create instance of event.
     *
     * @param \edusign $edusign
     * @param \stdClass $grade
     * @return feedback_viewed
     */
    public static function create_from_grade(\edusign $edusign, \stdClass $grade) {
        $data = array(
                'objectid' => $grade->id,
                'relateduserid' => $grade->userid,
                'context' => $edusign->get_context(),
                'other' => array(
                        'edusignid' => $edusign->get_instance()->id,
                ),
        );
        /** @var feedback_viewed $event */
        $event = self::create($data);
        $event->set_edusign($edusign);
        $event->add_record_snapshot('edusign_grades', $grade);
        return $event;
    }

    /**
     * Init method.
     */
    protected function init() {
        $this->data['objecttable'] = 'edusign_grades';
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventfeedbackviewed', 'mod_edusign');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' viewed the feedback for the user with id '$this->relateduserid' " .
                "for the edusignment with course module id '$this->contextinstanceid'.";
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->relateduserid)) {
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }

        if (!isset($this->other['edusignid'])) {
            throw new \coding_exception('The \'edusignid\' value must be set in other.');
        }
    }

    public static function get_objectid_mapping() {
        return array('db' => 'edusign_grades', 'restore' => 'grade');
    }

    public static function get_other_mapping() {
        $othermapped = array();
        $othermapped['edusignid'] = array('db' => 'edusign', 'restore' => 'edusign');

        return $othermapped;
    }
}
