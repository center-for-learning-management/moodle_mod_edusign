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
 * Web service for mod edusign
 * @package    mod_edusign
 * @subpackage db
 * @since      Moodle 2.4
 * @copyright  2012 Paul Charsley
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = array(

        'mod_edusign_copy_previous_attempt' => array(
            'classname'     => 'mod_edusign_external',
            'methodname'    => 'copy_previous_attempt',
            'classpath'     => 'mod/edusign/externallib.php',
            'description'   => 'Copy a students previous attempt to a new attempt.',
            'type'          => 'write',
            'capabilities'  => 'mod/edusign:view, mod/edusign:submit'
        ),

        'mod_edusign_get_grades' => array(
                'classname'   => 'mod_edusign_external',
                'methodname'  => 'get_grades',
                'classpath'   => 'mod/edusign/externallib.php',
                'description' => 'Returns grades from the edusignment',
                'type'        => 'read',
                'services'    => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_edusign_get_edusignments' => array(
                'classname'   => 'mod_edusign_external',
                'methodname'  => 'get_edusignments',
                'classpath'   => 'mod/edusign/externallib.php',
                'description' => 'Returns the courses and edusignments for the users capability',
                'type'        => 'read',
                'services'    => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_edusign_get_submissions' => array(
                'classname' => 'mod_edusign_external',
                'methodname' => 'get_submissions',
                'classpath' => 'mod/edusign/externallib.php',
                'description' => 'Returns the submissions for edusignments',
                'type' => 'read',
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_edusign_get_user_flags' => array(
                'classname' => 'mod_edusign_external',
                'methodname' => 'get_user_flags',
                'classpath' => 'mod/edusign/externallib.php',
                'description' => 'Returns the user flags for edusignments',
                'type' => 'read',
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_edusign_set_user_flags' => array(
                'classname'   => 'mod_edusign_external',
                'methodname'  => 'set_user_flags',
                'classpath'   => 'mod/edusign/externallib.php',
                'description' => 'Creates or updates user flags',
                'type'        => 'write',
                'capabilities'=> 'mod/edusign:grade',
                'services'    => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_edusign_get_user_mappings' => array(
                'classname' => 'mod_edusign_external',
                'methodname' => 'get_user_mappings',
                'classpath' => 'mod/edusign/externallib.php',
                'description' => 'Returns the blind marking mappings for edusignments',
                'type' => 'read',
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_edusign_revert_submissions_to_draft' => array(
                'classname' => 'mod_edusign_external',
                'methodname' => 'revert_submissions_to_draft',
                'classpath' => 'mod/edusign/externallib.php',
                'description' => 'Reverts the list of submissions to draft status',
                'type' => 'write',
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_edusign_lock_submissions' => array(
                'classname' => 'mod_edusign_external',
                'methodname' => 'lock_submissions',
                'classpath' => 'mod/edusign/externallib.php',
                'description' => 'Prevent students from making changes to a list of submissions',
                'type' => 'write',
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_edusign_unlock_submissions' => array(
                'classname' => 'mod_edusign_external',
                'methodname' => 'unlock_submissions',
                'classpath' => 'mod/edusign/externallib.php',
                'description' => 'Allow students to make changes to a list of submissions',
                'type' => 'write',
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_edusign_save_submission' => array(
                'classname' => 'mod_edusign_external',
                'methodname' => 'save_submission',
                'classpath' => 'mod/edusign/externallib.php',
                'description' => 'Update the current students submission',
                'type' => 'write',
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_edusign_submit_for_grading' => array(
                'classname' => 'mod_edusign_external',
                'methodname' => 'submit_for_grading',
                'classpath' => 'mod/edusign/externallib.php',
                'description' => 'Submit the current students edusignment for grading',
                'type' => 'write',
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_edusign_save_grade' => array(
                'classname' => 'mod_edusign_external',
                'methodname' => 'save_grade',
                'classpath' => 'mod/edusign/externallib.php',
                'description' => 'Save a grade update for a single student.',
                'type' => 'write',
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_edusign_save_grades' => array(
                'classname' => 'mod_edusign_external',
                'methodname' => 'save_grades',
                'classpath' => 'mod/edusign/externallib.php',
                'description' => 'Save multiple grade updates for an edusignment.',
                'type' => 'write',
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_edusign_save_user_extensions' => array(
                'classname' => 'mod_edusign_external',
                'methodname' => 'save_user_extensions',
                'classpath' => 'mod/edusign/externallib.php',
                'description' => 'Save a list of edusignment extensions',
                'type' => 'write',
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_edusign_reveal_identities' => array(
                'classname' => 'mod_edusign_external',
                'methodname' => 'reveal_identities',
                'classpath' => 'mod/edusign/externallib.php',
                'description' => 'Reveal the identities for a blind marking edusignment',
                'type' => 'write',
                'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_edusign_view_grading_table' => array(
                'classname'     => 'mod_edusign_external',
                'methodname'    => 'view_grading_table',
                'classpath'     => 'mod/edusign/externallib.php',
                'description'   => 'Trigger the grading_table_viewed event.',
                'type'          => 'write',
                'capabilities'  => 'mod/edusign:view, mod/edusign:viewgrades',
                'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_edusign_view_submission_status' => array(
            'classname'     => 'mod_edusign_external',
            'methodname'    => 'view_submission_status',
            'classpath'     => 'mod/edusign/externallib.php',
            'description'   => 'Trigger the submission status viewed event.',
            'type'          => 'write',
            'capabilities'  => 'mod/edusign:view',
            'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_edusign_get_submission_status' => array(
            'classname'     => 'mod_edusign_external',
            'methodname'    => 'get_submission_status',
            'classpath'     => 'mod/edusign/externallib.php',
            'description'   => 'Returns information about an edusignment submission status for a given user.',
            'type'          => 'read',
            'capabilities'  => 'mod/edusign:view',
            'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_edusign_list_participants' => array(
                'classname'     => 'mod_edusign_external',
                'methodname'    => 'list_participants',
                'classpath'     => 'mod/edusign/externallib.php',
                'description'   => 'List the participants for a single edusignment, with some summary info about their submissions.',
                'type'          => 'read',
                'ajax'          => true,
                'capabilities'  => 'mod/edusign:view, mod/edusign:viewgrades',
                'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

        'mod_edusign_submit_grading_form' => array(
                'classname'     => 'mod_edusign_external',
                'methodname'    => 'submit_grading_form',
                'classpath'     => 'mod/edusign/externallib.php',
                'description'   => 'Submit the grading form data via ajax',
                'type'          => 'write',
                'ajax'          => true,
                'capabilities'  => 'mod/edusign:grade',
                'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),
        'mod_edusign_get_participant' => array(
                'classname'     => 'mod_edusign_external',
                'methodname'    => 'get_participant',
                'classpath'     => 'mod/edusign/externallib.php',
                'description'   => 'Get a participant for an edusignment, with some summary info about their submissions.',
                'type'          => 'read',
                'ajax'          => true,
                'capabilities'  => 'mod/edusign:view, mod/edusign:viewgrades',
                'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),
        'mod_edusign_view_edusign' => array(
            'classname'     => 'mod_edusign_external',
            'methodname'    => 'view_edusign',
            'classpath'     => 'mod/edusign/externallib.php',
            'description'   => 'Update the module completion status.',
            'type'          => 'write',
            'capabilities'  => 'mod/edusign:view',
            'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
        ),

);
