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
 * Unit tests for (some of) mod/edusign/upgradelib.php.
 *
 * @package    mod_edusign
 * @category   phpunit
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/edusign/locallib.php');
require_once($CFG->dirroot . '/mod/edusign/upgradelib.php');
require_once($CFG->dirroot . '/mod/edusignment/lib.php');

/**
 * Unit tests for (some of) mod/edusign/upgradelib.php.
 *
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_edusign_upgradelib_testcase extends advanced_testcase {

    /**
     * Data provider for edusignment upgrade.
     *
     * @return  array
     */
    public function edusignment_upgrade_provider() {
        return [
                'upload' => [
                        'type' => 'upload',
                        'submissionplugins' => [
                                'onlinetext' => true,
                                'comments' => true,
                                'file' => false,
                        ],
                        'feedbackplugins' => [
                                'comments' => false,
                                'file' => false,
                                'offline' => true,
                        ],
                ],
                'uploadsingle' => [
                        'type' => 'uploadsingle',
                        'submissionplugins' => [
                                'onlinetext' => true,
                                'comments' => true,
                                'file' => false,
                        ],
                        'feedbackplugins' => [
                                'comments' => false,
                                'file' => false,
                                'offline' => true,
                        ],
                ],
                'online' => [
                        'type' => 'online',
                        'submissionplugins' => [
                                'onlinetext' => false,
                                'comments' => true,
                                'file' => true,
                        ],
                        'feedbackplugins' => [
                                'comments' => false,
                                'file' => true,
                                'offline' => true,
                        ],
                ],
                'offline' => [
                        'type' => 'offline',
                        'submissionplugins' => [
                                'onlinetext' => true,
                                'comments' => true,
                                'file' => true,
                        ],
                        'feedbackplugins' => [
                                'comments' => false,
                                'file' => true,
                                'offline' => true,
                        ],
                ],
        ];
    }

    /**
     * Test assigment upgrade.
     *
     * @dataProvider edusignment_upgrade_provider
     * @param string $type The type of edusignment
     * @param array $plugins Which plugins shuld or shoudl not be enabled
     */
    public function test_upgrade_edusignment($type, $plugins) {
        global $DB, $CFG;

        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');

        $commentconfig = false;
        if (!empty($CFG->usecomments)) {
            $commentconfig = $CFG->usecomments;
        }
        $CFG->usecomments = false;

        // Create the old edusignment.
        $this->setUser($teacher);
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_edusignment');
        $edusignment = $generator->create_instance([
                'course' => $course->id,
                'edusignmenttype' => $type,
        ]);

        // Run the upgrade.
        $this->setAdminUser();
        $log = '';
        $upgrader = new edusign_upgrade_manager();

        $this->assertTrue($upgrader->upgrade_edusignment($edusignment->id, $log));
        $record = $DB->get_record('edusign', ['course' => $course->id]);

        $cm = get_coursemodule_from_instance('edusign', $record->id);
        $context = context_module::instance($cm->id);

        $edusign = new edusign($context, $cm, $course);

        foreach ($plugins as $plugin => $isempty) {
            $plugin = $edusign->get_submission_plugin_by_type($plugin);
            $this->assertEquals($isempty, empty($plugin->is_enabled()));
        }
    }
}
