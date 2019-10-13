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
 * The edusignsubmission_signing submission_updated event.
 *
 * @package    edusignsubmission_signing
 * @copyright  2014 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace edusignsubmission_signing\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The edusignsubmission_signing submission_updated event class.
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - int signingwordcount: Word count of the online text submission.
 * }
 *
 * @package    edusignsubmission_signing
 * @since      Moodle 2.7
 * @copyright  2014 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submission_updated extends \mod_edusign\event\submission_updated {

    /**
     * Init method.
     */
    protected function init() {
        parent::init();
        $this->data['objecttable'] = 'edusignsubmission_signing';
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        $descriptionstring = "The user with id '$this->userid' updated an online text submission with " .
                "'{$this->other['signingwordcount']}' words in the edusignment with course module id " .
                "'$this->contextinstanceid'";
        if (!empty($this->other['groupid'])) {
            $descriptionstring .= " for the group with id '{$this->other['groupid']}'.";
        } else {
            $descriptionstring .= ".";
        }

        return $descriptionstring;
    }

    /**
     * Custom validation.
     *
     * @return void
     * @throws \coding_exception
     */
    protected function validate_data() {
        parent::validate_data();
        if (!isset($this->other['signingwordcount'])) {
            throw new \coding_exception('The \'signingwordcount\' value must be set in other.');
        }
    }

    public static function get_objectid_mapping() {
        // No mapping available for 'edusignsubmission_signing'.
        return array('db' => 'edusignsubmission_signing', 'restore' => \core\event\base::NOT_MAPPED);
    }
}
