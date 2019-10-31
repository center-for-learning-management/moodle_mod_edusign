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
 * This file contains the definition for the library class for signing submission plugin
 *
 * This class provides all the functionality for the new edusign module.
 *
 * @package edusignsubmission_signing
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
// File area for online text submission edusignment.
define('EDUSIGNSUBMISSION_SIGNING_FILEAREA', 'submissions_signing');

/**
 * library class for signing submission plugin extending submission plugin base class
 *
 * @package edusignsubmission_signing
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class edusign_submission_signing extends edusign_submission_plugin
{

    /**
     * Get the name of the online text submission plugin
     *
     * @return string
     */
    public function get_name()
    {
        return get_string('signing', 'edusignsubmission_signing');
    }

    /**
     * Get signing submission information from the database
     *
     * @param int $submissionid
     * @return mixed
     */
    private function get_signing_submission($submissionid)
    {
        global $DB;

        return $DB->get_record('edusignsubmission_signing', array('submission' => $submissionid));
    }

    /**
     * Get the settings for signing submission plugin
     *
     * @param MoodleQuickForm $mform The form to add elements to
     * @return void
     */
    /*public function get_settings(MoodleQuickForm $mform)
    {
        global $CFG, $COURSE;

        $defaultwordlimit = $this->get_config('wordlimit') == 0 ? '' : $this->get_config('wordlimit');
        $defaultwordlimitenabled = $this->get_config('wordlimitenabled');

        $options = array('size' => '6', 'maxlength' => '6');
        $name = get_string('wordlimit', 'edusignsubmission_signing');

        // Create a text box that can be enabled/disabled for signing word limit.
        $wordlimitgrp = array();
        $wordlimitgrp[] = $mform->createElement('text', 'edusignsubmission_signing_wordlimit', '', $options);
        $wordlimitgrp[] = $mform->createElement(
            'checkbox',
            'edusignsubmission_signing_wordlimit_enabled',
            '',
            get_string('enable')
        );
        $mform->addGroup($wordlimitgrp, 'edusignsubmission_signing_wordlimit_group', $name, ' ', false);
        $mform->addHelpButton(
            'edusignsubmission_signing_wordlimit_group',
            'wordlimit',
            'edusignsubmission_signing'
        );
        $mform->disabledIf(
            'edusignsubmission_signing_wordlimit',
            'edusignsubmission_signing_wordlimit_enabled',
            'notchecked'
        );

        // Add numeric rule to text field.
        $wordlimitgrprules = array();
        $wordlimitgrprules['edusignsubmission_signing_wordlimit'][] = array(null, 'numeric', null, 'client');
        $mform->addGroupRule('edusignsubmission_signing_wordlimit_group', $wordlimitgrprules);

        // Rest of group setup.
        $mform->setDefault('edusignsubmission_signing_wordlimit', $defaultwordlimit);
        $mform->setDefault('edusignsubmission_signing_wordlimit_enabled', $defaultwordlimitenabled);
        $mform->setType('edusignsubmission_signing_wordlimit', PARAM_INT);
        $mform->disabledIf(
            'edusignsubmission_signing_wordlimit_group',
            'edusignsubmission_signing_enabled',
            'notchecked'
        );
    }*/

    /**
     * Save the settings for signing submission plugin
     *
     * @param stdClass $data
     * @return bool
     */
    public function save_settings(stdClass $data)
    {
        if (empty($data->edusignsubmission_signing_wordlimit) || empty($data->edusignsubmission_signing_wordlimit_enabled)) {
            $wordlimit = 0;
            $wordlimitenabled = 0;
        } else {
            $wordlimit = $data->edusignsubmission_signing_wordlimit;
            $wordlimitenabled = 1;
        }

        $this->set_config('wordlimit', $wordlimit);
        $this->set_config('wordlimitenabled', $wordlimitenabled);

        return true;
    }

    /**
     * Add form elements for settings
     *
     * @param mixed $submission can be null
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @return true if elements were added to the form
     */
    public function get_form_elements($submission, MoodleQuickForm $mform, stdClass $data)
    {
        global $PAGE;
        $elements = array();

        $editoroptions = $this->get_edit_options();
        $submissionid = $submission ? $submission->id : 0;



        if (!isset($data->signing)) {
            $data->signing = '';
        }
        if (!isset($data->signingformat)) {
            $data->signingformat = editors_get_preferred_format();
        }

        if ($submission) {
            $signingsubmission = $this->get_signing_submission($submission->id);
            if ($signingsubmission) {
                $data->signing = $signingsubmission->signing;
            }
        }
        $data = file_prepare_standard_editor(
            $data,
            'signing',
            $editoroptions,
            $this->edusignment->get_context(),
            'edusignsubmission_signing',
            EDUSIGNSUBMISSION_SIGNING_FILEAREA,
            $submissionid
        );

        $mform->addElement('hidden', 'signing', 'Data/Base64', 'wrap="virtual" rows="1" cols="1"');
        $mform->addElement('html', "<div class='form-group row'><div class='col-md-12'><div class='alert alert-warning'>Bitte benützen Sie für die bessere Usability ein Touchpad</div></div></div>");
        $mform->setType('signing', PARAM_RAW);

        $mform->addElement(
            'html',
            "<div class='form-group row'><div class='col-md-3'>Unterschrift</div><div class='col-md-9'><canvas id='canvas' class='form-control' height='250px' width='1000px'></canvas>
            <canvas id='blank' style='display:none'></canvas><a class='btn btn-secondary' id='clearCanvas'  role='button'>Reset</a></div></div>"
        );
        // $mform->addElement('filepicker', 'userfile', get_string('file'), null,
        // array('maxbytes' => $maxbytes, 'accepted_types' => '*'));
        $PAGE->requires->js_call_amd('edusignsubmission_signing/signingjs', 'save');

        return true;
    }

    /**
     * Editor format options
     *
     * @return array
     */
    private function get_edit_options()
    {
        $editoroptions = array(
                'noclean' => false,
                'maxfiles' => EDITOR_UNLIMITED_FILES,
                'maxbytes' => $this->edusignment->get_course()->maxbytes,
                'context' => $this->edusignment->get_context(),
                'return_types' => (FILE_INTERNAL | FILE_EXTERNAL | FILE_CONTROLLED_LINK),
                'removeorphaneddrafts' => true // Whether or not to remove any draft files which aren't referenced in the text.
        );
        return $editoroptions;
    }

    /**
     * Save data to the database and trigger plagiarism plugin,
     * if enabled, to scan the uploaded content via events trigger
     *
     * @param stdClass $submission
     * @param stdClass $data
     * @return bool
     */
    public function save(stdClass $submission, stdClass $data)
    {
        global $USER, $DB;
        $editoroptions = $this->get_edit_options();
        $signingsubmission = $this->get_signing_submission($submission->id);
        $fs = get_file_storage();

        $files = $fs->get_area_files(
            $this->edusignment->get_context()->id,
            'edusignsubmission_signing',
            EDUSIGNSUBMISSION_SIGNING_FILEAREA,
            $submission->id,
            'id',
            false
        );

        $params = array(
                'context' => context_module::instance($this->edusignment->get_course_module()->id),
                'courseid' => $this->edusignment->get_course()->id,
                'objectid' => $submission->id,
                'other' => array(
                        'pathnamehashes' => array_keys($files),
                        'content' => $data->signing,
                        'format' => PARAM_RAW,
                )
        );
     
        if (!empty($submission->userid) && ($submission->userid != $USER->id)) {
            $params['relateduserid'] = $submission->userid;
        }
        if ($this->edusignment->is_blind_marking()) {
            $params['anonymous'] = 1;
        }
        $event = \edusignsubmission_signing\event\assessable_uploaded::create($params);
        $event->trigger();

        $groupname = null;
        $groupid = 0;

        // Get the group name as other fields are not transcribed in the logs and this information is important.
        if (empty($submission->userid) && !empty($submission->groupid)) {
            $groupname = $DB->get_field('groups', 'name', array('id' => $submission->groupid), MUST_EXIST);
            $groupid = $submission->groupid;
        } else {
            $params['relateduserid'] = $submission->userid;
        }

        $count = count_words($data->signing);

        // Unset the objectid and other field from params for use in submission events.
        unset($params['objectid']);
        unset($params['other']);
        $params['other'] = array(
                'submissionid' => $submission->id,
                'submissionattempt' => $submission->attemptnumber,
                'submissionstatus' => $submission->status,
                'signingwordcount' => $count,
        );

        if ($signingsubmission) {
            $signingsubmission->signing = $data->signing;
            $signingsubmission->onlineformat = 1;
            $params['objectid'] = $signingsubmission->id;
            $updatestatus = $DB->update_record('edusignsubmission_signing', $signingsubmission);
            $event = \edusignsubmission_signing\event\submission_updated::create($params);
            $event->set_edusign($this->edusignment);
            $event->trigger();
            return $updatestatus;
        } else {
            $signingsubmission = new stdClass();
            $signingsubmission->signing = $data->signing;
            $signingsubmission->onlineformat = 1;
            $signingsubmission->submission = $submission->id;
            $signingsubmission->edusignment = $this->edusignment->get_instance()->id;
            $signingsubmission->id = $DB->insert_record('edusignsubmission_signing', $signingsubmission);
            $params['objectid'] = $signingsubmission->id;
            $event = \edusignsubmission_signing\event\submission_created::create($params);
            $event->set_edusign($this->edusignment);
            $event->trigger();
            return $signingsubmission->id > 0;
        }
    }

    /**
     * Return a list of the text fields that can be imported/exported by this plugin
     *
     * @return array An array of field names and descriptions. (name=>description, ...)
     */
    public function get_editor_fields()
    {
        return array('signing' => get_string('pluginname', 'edusignsubmission_signing'));
    }

    /**
     * Get the saved text content from the editor
     *
     * @param string $name
     * @param int $submissionid
     * @return string
     */
    public function get_editor_text($name, $submissionid)
    {
        if ($name == 'signing') {
            $signingsubmission = $this->get_signing_submission($submissionid);
            if ($signingsubmission) {
                return $signingsubmission->signing;
            }
        }

        return '';
    }

    /**
     * Get the content format for the editor
     *
     * @param string $name
     * @param int $submissionid
     * @return int
     */
    public function get_editor_format($name, $submissionid)
    {
        if ($name == 'signing') {
            $signingsubmission = $this->get_signing_submission($submissionid);
            if ($signingsubmission) {
                return $signingsubmission->onlineformat;
            }
        }

        return 0;
    }

    /**
     * Display signing word count in the submission status table
     *
     * @param stdClass $submission
     * @param bool $showviewlink - If the summary has been truncated set this to true
     * @return string
     */
    public function view_summary(stdClass $submission, & $showviewlink)
    {
        global $CFG;

        $signingsubmission = $this->get_signing_submission($submission->id);
        // Always show the view link.
        $showviewlink = false;

        if ($signingsubmission) {
            // This contains the shortened version of the text plus an optional 'Export to portfolio' button.
            $text = $this->edusignment->render_editor_content(
                EDUSIGNSUBMISSION_SIGNING_FILEAREA,
                $signingsubmission->submission,
                $this->get_type(),
                'signing',
                'edusignsubmission_signing',
                true
            );

            // The actual submission text.
            $signing = trim($signingsubmission->signing);
            $text = "<img src='" . strip_tags($signing) . "'>";

            return $text;
        }
        return '';
    }

    /**
     * Produce a list of files suitable for export that represent this submission.
     *
     * @param stdClass $submission - For this is the submission data
     * @param stdClass $user - This is the user record for this submission
     * @return array - return an array of files indexed by filename
     */
    public function get_files(stdClass $submission, stdClass $user)
    {
        global $DB;

        $files = array();
        $signingsubmission = $this->get_signing_submission($submission->id);

        // Note that this check is the same logic as the result from the is_empty function but we do
        // not call it directly because we already have the submission record.
        if ($signingsubmission && !empty($signingsubmission->signing)) {
            // Do not pass the text through format_text. The result may not be displayed in Moodle and
            // may be passed to external services such as document conversion or portfolios.
            $formattedtext = $this->edusignment->download_rewrite_pluginfile_urls($signingsubmission->signing, $user, $this);
            $head = '<head><meta charset="UTF-8"></head>';
            $submissioncontent = '<!DOCTYPE html><html>' . $head . '<body>' . $formattedtext . '</body></html>';

            $filename = get_string('signingfilename', 'edusignsubmission_signing');
            $files[$filename] = array($submissioncontent);

            $fs = get_file_storage();

            $fsfiles = $fs->get_area_files(
                $this->edusignment->get_context()->id,
                'edusignsubmission_signing',
                EDUSIGNSUBMISSION_SIGNING_FILEAREA,
                $submission->id,
                'timemodified',
                false
            );

            foreach ($fsfiles as $file) {
                $files[$file->get_filename()] = $file;
            }
        }

        return $files;
    }

    /**
     * Display the saved text content from the editor in the view table
     *
     * @param stdClass $submission
     * @return string
     */
    public function view(stdClass $submission)
    {
        global $CFG;
        $result = '';

        $signingsubmission = $this->get_signing_submission($submission->id);

        if ($signingsubmission) {
            // Render for portfolio API.
            $result .= $this->edusignment->render_editor_content(
                EDUSIGNSUBMISSION_SIGNING_FILEAREA,
                $signingsubmission->submission,
                $this->get_type(),
                'signing',
                'edusignsubmission_signing'
            );

            $result = "<img src='" . strip_tags($result) . "'>";
        }

        return $result;
    }

    /**
     * Return true if this plugin can upgrade an old Moodle 2.2 edusignment of this type and version.
     *
     * @param string $type old edusignment subtype
     * @param int $version old edusignment version
     * @return bool True if upgrade is possible
     */
    public function can_upgrade($type, $version)
    {
        if ($type == 'online' && $version >= 2011112900) {
            return true;
        }
        return false;
    }

    /**
     * Upgrade the settings from the old edusignment to the new plugin based one
     *
     * @param context $oldcontext - the database for the old edusignment context
     * @param stdClass $oldedusignment - the database for the old edusignment instance
     * @param string $log record log events here
     * @return bool Was it a success?
     */
    public function upgrade_settings(context $oldcontext, stdClass $oldedusignment, & $log)
    {
        // No settings to upgrade.
        return true;
    }

    /**
     * Upgrade the submission from the old edusignment to the new one
     *
     * @param context $oldcontext - the database for the old edusignment context
     * @param stdClass $oldedusignment The data record for the old edusignment
     * @param stdClass $oldsubmission The data record for the old submission
     * @param stdClass $submission The data record for the new submission
     * @param string $log Record upgrade messages in the log
     * @return bool true or false - false will trigger a rollback
     */
    public function upgrade(
        context $oldcontext,
        stdClass $oldedusignment,
        stdClass $oldsubmission,
        stdClass $submission,
        & $log
    ) {
        global $DB;

        $signingsubmission = new stdClass();
        $signingsubmission->signing = $oldsubmission->data1;
        $signingsubmission->onlineformat = $oldsubmission->data2;

        $signingsubmission->submission = $submission->id;
        $signingsubmission->edusignment = $this->edusignment->get_instance()->id;

        if ($signingsubmission->signing === null) {
            $signingsubmission->signing = '';
        }

        if ($signingsubmission->onlineformat === null) {
            $signingsubmission->onlineformat = editors_get_preferred_format();
        }

        if (!$DB->insert_record('edusignsubmission_signing', $signingsubmission) > 0) {
            $log .= get_string('couldnotconvertsubmission', 'mod_edusign', $submission->userid);
            return false;
        }

        // Now copy the area files.
        $this->edusignment->copy_area_files_for_upgrade(
            $oldcontext->id,
            'mod_edusignment',
            'submission',
            $oldsubmission->id,
            $this->edusignment->get_context()->id,
            'edusignsubmission_signing',
            EDUSIGNSUBMISSION_SIGNING_FILEAREA,
            $submission->id
        );
        return true;
    }

    /**
     * Formatting for log info
     *
     * @param stdClass $submission The new submission
     * @return string
     */
    public function format_for_log(stdClass $submission)
    {
        // Format the info for each submission plugin (will be logged).
        $signingsubmission = $this->get_signing_submission($submission->id);
        $signingloginfo = '';
        $signingloginfo .= get_string(
            'numwordsforlog',
            'edusignsubmission_signing',
            count_words($signingsubmission->signing)
        );

        return $signingloginfo;
    }

    /**
     * The edusignment has been deleted - cleanup
     *
     * @return bool
     */
    public function delete_instance()
    {
        global $DB;
        $DB->delete_records(
            'edusignsubmission_signing',
            array('edusignment' => $this->edusignment->get_instance()->id)
        );

        return true;
    }

    /**
     * No text is set for this plugin
     *
     * @param stdClass $submission
     * @return wordcount
     */
    public function is_empty(stdClass $submission)
    {
        $signingsubmission = $this->get_signing_submission($submission->id);
        $wordcount = 0;
        $hasinsertedresources = false;

        if (isset($signingsubmission->signing)) {
            $wordcount = count_words(trim($signingsubmission->signing));
            // Check if the online text submission contains video, audio or image elements
            // that can be ignored and stripped by count_words().
        }

        return $wordcount == 0;
    }

    /**
     * Determine if a submission is empty
     *
     * This is distinct from is_empty in that it is intended to be used to
     * determine if a submission made before saving is empty.
     *
     * @param stdClass $data The submission data
     * @return bool
     */
    public function submission_is_empty(stdClass $data)
    {
        if (!isset($data->signing)) {
            return true;
        }
        return false;
    }

    /**
     * Get file areas returns a list of areas this plugin stores files
     *
     * @return array - An array of fileareas (keys) and descriptions (values)
     */
    public function get_file_areas()
    {
        return array(EDUSIGNSUBMISSION_SIGNING_FILEAREA => $this->get_name());
    }

    /**
     * Copy the student's submission from a previous submission. Used when a student opts to base their resubmission
     * on the last submission.
     *
     * @param stdClass $sourcesubmission
     * @param stdClass $destsubmission
     */
    public function copy_submission(stdClass $sourcesubmission, stdClass $destsubmission)
    {
        global $DB;

        // Copy the files across (attached via the text editor).
        $contextid = $this->edusignment->get_context()->id;
        $fs = get_file_storage();
        $files = $fs->get_area_files(
            $contextid,
            'edusignsubmission_signing',
            EDUSIGNSUBMISSION_SIGNING_FILEAREA,
            $sourcesubmission->id,
            'id',
            false
        );
        foreach ($files as $file) {
            $fieldupdates = array('itemid' => $destsubmission->id);
            $fs->create_file_from_storedfile($fieldupdates, $file);
        }

        // Copy the edusignsubmission_signing record.
        $signingsubmission = $this->get_signing_submission($sourcesubmission->id);
        if ($signingsubmission) {
            unset($signingsubmission->id);
            $signingsubmission->submission = $destsubmission->id;
            $DB->insert_record('edusignsubmission_signing', $signingsubmission);
        }
        return true;
    }

    /**
     * Return a description of external params suitable for uploading an signing submission from a webservice.
     *
     * @return external_description|null
     */
    public function get_external_parameters()
    {
        $editorparams = array('text' => new external_value(PARAM_RAW, 'The text for this submission.'),
                'format' => new external_value(PARAM_INT, 'The format for this submission'),
                'itemid' => new external_value(PARAM_INT, 'The draft area id for files attached to the submission'));
        $editorstructure = new external_single_structure($editorparams, 'Editor structure', VALUE_OPTIONAL);
        return array('signing_editor' => $editorstructure);
    }

    /**
     * Compare word count of signing submission to word limit, and return result.
     *
     * @param string $submissiontext signing submission text from editor
     * @return string Error message if limit is enabled and exceeded, otherwise null
     */
    public function check_word_count($submissiontext)
    {
        global $OUTPUT;

        $wordlimitenabled = $this->get_config('wordlimitenabled');
        $wordlimit = $this->get_config('wordlimit');

        if ($wordlimitenabled == 0) {
            return null;
        }

        // Count words and compare to limit.
        $wordcount = count_words($submissiontext);
        if ($wordcount <= $wordlimit) {
            return null;
        } else {
            $errormsg = get_string(
                'wordlimitexceeded',
                'edusignsubmission_signing',
                array('limit' => $wordlimit, 'count' => $wordcount)
            );
            return $OUTPUT->error_text($errormsg);
        }
    }

    /**
     * Return the plugin configs for external functions.
     *
     * @return array the list of settings
     * @since Moodle 3.2
     */
    public function get_config_for_external()
    {
        return (array) $this->get_config();
    }
}
