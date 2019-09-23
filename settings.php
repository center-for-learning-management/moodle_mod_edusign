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
 * This file adds the settings pages to the navigation menu
 *
 * @package   mod_edusign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/edusign/adminlib.php');

$ADMIN->add('modsettings', new admin_category('modedusignfolder', new lang_string('pluginname', 'mod_edusign'), $module->is_enabled() === false));

$settings = new admin_settingpage($section, get_string('settings', 'mod_edusign'), 'moodle/site:config', $module->is_enabled() === false);

if ($ADMIN->fulltree) {
    $menu = array();
    foreach (core_component::get_plugin_list('edusignfeedback') as $type => $notused) {
        $visible = !get_config('edusignfeedback_' . $type, 'disabled');
        if ($visible) {
            $menu['edusignfeedback_' . $type] = new lang_string('pluginname', 'edusignfeedback_' . $type);
        }
    }

    // The default here is feedback_comments (if it exists).
    $name = new lang_string('feedbackplugin', 'mod_edusign');
    $description = new lang_string('feedbackpluginforgradebook', 'mod_edusign');
    $settings->add(new admin_setting_configselect('edusign/feedback_plugin_for_gradebook',
                                                  $name,
                                                  $description,
                                                  'edusignfeedback_comments',
                                                  $menu));

    $name = new lang_string('showrecentsubmissions', 'mod_edusign');
    $description = new lang_string('configshowrecentsubmissions', 'mod_edusign');
    $settings->add(new admin_setting_configcheckbox('edusign/showrecentsubmissions',
                                                    $name,
                                                    $description,
                                                    0));

    $name = new lang_string('sendsubmissionreceipts', 'mod_edusign');
    $description = new lang_string('sendsubmissionreceipts_help', 'mod_edusign');
    $settings->add(new admin_setting_configcheckbox('edusign/submissionreceipts',
                                                    $name,
                                                    $description,
                                                    1));

    $name = new lang_string('submissionstatement', 'mod_edusign');
    $description = new lang_string('submissionstatement_help', 'mod_edusign');
    $default = get_string('submissionstatementdefault', 'mod_edusign');
    $setting = new admin_setting_configtextarea('edusign/submissionstatement',
                                                    $name,
                                                    $description,
                                                    $default);
    $setting->set_force_ltr(false);
    $settings->add($setting);

    $name = new lang_string('maxperpage', 'mod_edusign');
    $options = array(
        -1 => get_string('unlimitedpages', 'mod_edusign'),
        10 => 10,
        20 => 20,
        50 => 50,
        100 => 100,
    );
    $description = new lang_string('maxperpage_help', 'mod_edusign');
    $settings->add(new admin_setting_configselect('edusign/maxperpage',
                                                    $name,
                                                    $description,
                                                    -1,
                                                    $options));

    $name = new lang_string('defaultsettings', 'mod_edusign');
    $description = new lang_string('defaultsettings_help', 'mod_edusign');
    $settings->add(new admin_setting_heading('defaultsettings', $name, $description));

    $name = new lang_string('alwaysshowdescription', 'mod_edusign');
    $description = new lang_string('alwaysshowdescription_help', 'mod_edusign');
    $setting = new admin_setting_configcheckbox('edusign/alwaysshowdescription',
                                                    $name,
                                                    $description,
                                                    1);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('allowsubmissionsfromdate', 'mod_edusign');
    $description = new lang_string('allowsubmissionsfromdate_help', 'mod_edusign');
    $setting = new admin_setting_configduration('edusign/allowsubmissionsfromdate',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_enabled_flag_options(admin_setting_flag::ENABLED, true);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('duedate', 'mod_edusign');
    $description = new lang_string('duedate_help', 'mod_edusign');
    $setting = new admin_setting_configduration('edusign/duedate',
                                                    $name,
                                                    $description,
                                                    604800);
    $setting->set_enabled_flag_options(admin_setting_flag::ENABLED, true);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('cutoffdate', 'mod_edusign');
    $description = new lang_string('cutoffdate_help', 'mod_edusign');
    $setting = new admin_setting_configduration('edusign/cutoffdate',
                                                    $name,
                                                    $description,
                                                    1209600);
    $setting->set_enabled_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('gradingduedate', 'mod_edusign');
    $description = new lang_string('gradingduedate_help', 'mod_edusign');
    $setting = new admin_setting_configduration('edusign/gradingduedate',
                                                    $name,
                                                    $description,
                                                    1209600);
    $setting->set_enabled_flag_options(admin_setting_flag::ENABLED, true);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('submissiondrafts', 'mod_edusign');
    $description = new lang_string('submissiondrafts_help', 'mod_edusign');
    $setting = new admin_setting_configcheckbox('edusign/submissiondrafts',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('requiresubmissionstatement', 'mod_edusign');
    $description = new lang_string('requiresubmissionstatement_help', 'mod_edusign');
    $setting = new admin_setting_configcheckbox('edusign/requiresubmissionstatement',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    // Constants from "locallib.php".
    $options = array(
        'none' => get_string('attemptreopenmethod_none', 'mod_edusign'),
        'manual' => get_string('attemptreopenmethod_manual', 'mod_edusign'),
        'untilpass' => get_string('attemptreopenmethod_untilpass', 'mod_edusign')
    );
    $name = new lang_string('attemptreopenmethod', 'mod_edusign');
    $description = new lang_string('attemptreopenmethod_help', 'mod_edusign');
    $setting = new admin_setting_configselect('edusign/attemptreopenmethod',
                                                    $name,
                                                    $description,
                                                    'none',
                                                    $options);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    // Constants from "locallib.php".
    $options = array(-1 => get_string('unlimitedattempts', 'mod_edusign'));
    $options += array_combine(range(1, 30), range(1, 30));
    $name = new lang_string('maxattempts', 'mod_edusign');
    $description = new lang_string('maxattempts_help', 'mod_edusign');
    $setting = new admin_setting_configselect('edusign/maxattempts',
                                                    $name,
                                                    $description,
                                                    -1,
                                                    $options);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('teamsubmission', 'mod_edusign');
    $description = new lang_string('teamsubmission_help', 'mod_edusign');
    $setting = new admin_setting_configcheckbox('edusign/teamsubmission',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('preventsubmissionnotingroup', 'mod_edusign');
    $description = new lang_string('preventsubmissionnotingroup_help', 'mod_edusign');
    $setting = new admin_setting_configcheckbox('edusign/preventsubmissionnotingroup',
        $name,
        $description,
        0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('requireallteammemberssubmit', 'mod_edusign');
    $description = new lang_string('requireallteammemberssubmit_help', 'mod_edusign');
    $setting = new admin_setting_configcheckbox('edusign/requireallteammemberssubmit',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('teamsubmissiongroupingid', 'mod_edusign');
    $description = new lang_string('teamsubmissiongroupingid_help', 'mod_edusign');
    $setting = new admin_setting_configempty('edusign/teamsubmissiongroupingid',
                                                    $name,
                                                    $description);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('sendnotifications', 'mod_edusign');
    $description = new lang_string('sendnotifications_help', 'mod_edusign');
    $setting = new admin_setting_configcheckbox('edusign/sendnotifications',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('sendlatenotifications', 'mod_edusign');
    $description = new lang_string('sendlatenotifications_help', 'mod_edusign');
    $setting = new admin_setting_configcheckbox('edusign/sendlatenotifications',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('sendstudentnotificationsdefault', 'mod_edusign');
    $description = new lang_string('sendstudentnotificationsdefault_help', 'mod_edusign');
    $setting = new admin_setting_configcheckbox('edusign/sendstudentnotifications',
                                                    $name,
                                                    $description,
                                                    1);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('blindmarking', 'mod_edusign');
    $description = new lang_string('blindmarking_help', 'mod_edusign');
    $setting = new admin_setting_configcheckbox('edusign/blindmarking',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('markingworkflow', 'mod_edusign');
    $description = new lang_string('markingworkflow_help', 'mod_edusign');
    $setting = new admin_setting_configcheckbox('edusign/markingworkflow',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);

    $name = new lang_string('markingallocation', 'mod_edusign');
    $description = new lang_string('markingallocation_help', 'mod_edusign');
    $setting = new admin_setting_configcheckbox('edusign/markingallocation',
                                                    $name,
                                                    $description,
                                                    0);
    $setting->set_advanced_flag_options(admin_setting_flag::ENABLED, false);
    $setting->set_locked_flag_options(admin_setting_flag::ENABLED, false);
    $settings->add($setting);
}

$ADMIN->add('modedusignfolder', $settings);
// Tell core we already added the settings structure.
$settings = null;

$ADMIN->add('modedusignfolder', new admin_category('edusignsubmissionplugins',
    new lang_string('submissionplugins', 'edusign'), !$module->is_enabled()));
$ADMIN->add('edusignsubmissionplugins', new edusign_admin_page_manage_edusign_plugins('edusignsubmission'));
$ADMIN->add('modedusignfolder', new admin_category('edusignfeedbackplugins',
    new lang_string('feedbackplugins', 'edusign'), !$module->is_enabled()));
$ADMIN->add('edusignfeedbackplugins', new edusign_admin_page_manage_edusign_plugins('edusignfeedback'));

foreach (core_plugin_manager::instance()->get_plugins_of_type('edusignsubmission') as $plugin) {
    /** @var \mod_edusign\plugininfo\edusignsubmission $plugin */
    $plugin->load_settings($ADMIN, 'edusignsubmissionplugins', $hassiteconfig);
}

foreach (core_plugin_manager::instance()->get_plugins_of_type('edusignfeedback') as $plugin) {
    /** @var \mod_edusign\plugininfo\edusignfeedback $plugin */
    $plugin->load_settings($ADMIN, 'edusignfeedbackplugins', $hassiteconfig);
}
