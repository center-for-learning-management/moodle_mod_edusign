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
 * The mod_edusign abstract base event.
 *
 * @package    mod_edusign
 * @copyright  2014 Mark Nelson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_edusign\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_edusign abstract base event class.
 *
 * Most mod_edusign events can extend this class.
 *
 * @package    mod_edusign
 * @since      Moodle 2.7
 * @copyright  2014 Mark Nelson
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base extends \core\event\base {

    /** @var \edusign */
    protected $edusign;

    /**
     * Legacy log data.
     *
     * @var array
     */
    protected $legacylogdata;

    /**
     * Set edusign instance for this event.
     *
     * @param \edusign $edusign
     * @throws \coding_exception
     */
    public function set_edusign(\edusign $edusign) {
        if ($this->is_triggered()) {
            throw new \coding_exception('set_edusign() must be done before triggerring of event');
        }
        if ($edusign->get_context()->id != $this->get_context()->id) {
            throw new \coding_exception('Invalid edusign isntance supplied!');
        }
        if ($edusign->is_blind_marking()) {
            $this->data['anonymous'] = 1;
        }
        $this->edusign = $edusign;
    }

    /**
     * Get edusign instance.
     *
     * NOTE: to be used from observers only.
     *
     * @return \edusign
     * @throws \coding_exception
     */
    public function get_edusign() {
        if ($this->is_restored()) {
            throw new \coding_exception('get_edusign() is intended for event observers only');
        }
        if (!isset($this->edusign)) {
            debugging('edusign property should be initialised in each event', DEBUG_DEVELOPER);
            global $CFG;
            require_once($CFG->dirroot . '/mod/edusign/locallib.php');
            $cm = get_coursemodule_from_id('edusign', $this->contextinstanceid, 0, false, MUST_EXIST);
            $course = get_course($cm->course);
            $this->edusign = new \edusign($this->get_context(), $cm, $course);
        }
        return $this->edusign;
    }

    /**
     * Returns relevant URL.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/mod/edusign/view.php', array('id' => $this->contextinstanceid));
    }

    /**
     * Sets the legacy event log data.
     *
     * @param string $action The current action
     * @param string $info A detailed description of the change. But no more than 255 characters.
     * @param string $url The url to the edusign module instance.
     */
    public function set_legacy_logdata($action = '', $info = '', $url = '') {
        $fullurl = 'view.php?id=' . $this->contextinstanceid;
        if ($url != '') {
            $fullurl .= '&' . $url;
        }

        $this->legacylogdata = array($this->courseid, 'edusign', $action, $fullurl, $info, $this->contextinstanceid);
    }

    /**
     * Return legacy data for add_to_log().
     *
     * @return array
     */
    protected function get_legacy_logdata() {
        if (isset($this->legacylogdata)) {
            return $this->legacylogdata;
        }

        return null;
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     */
    protected function validate_data() {
        parent::validate_data();

        if ($this->contextlevel != CONTEXT_MODULE) {
            throw new \coding_exception('Context level must be CONTEXT_MODULE.');
        }
    }
}
