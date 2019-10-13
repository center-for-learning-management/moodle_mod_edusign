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
 * Definition of log events
 *
 * @package   mod_edusign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$logs = array(
        array('module' => 'edusign', 'action' => 'add', 'mtable' => 'edusign', 'field' => 'name'),
        array('module' => 'edusign', 'action' => 'delete mod', 'mtable' => 'edusign', 'field' => 'name'),
        array('module' => 'edusign', 'action' => 'download all submissions', 'mtable' => 'edusign', 'field' => 'name'),
        array('module' => 'edusign', 'action' => 'grade submission', 'mtable' => 'edusign', 'field' => 'name'),
        array('module' => 'edusign', 'action' => 'lock submission', 'mtable' => 'edusign', 'field' => 'name'),
        array('module' => 'edusign', 'action' => 'reveal identities', 'mtable' => 'edusign', 'field' => 'name'),
        array('module' => 'edusign', 'action' => 'revert submission to draft', 'mtable' => 'edusign', 'field' => 'name'),
        array('module' => 'edusign', 'action' => 'set marking workflow state', 'mtable' => 'edusign', 'field' => 'name'),
        array('module' => 'edusign', 'action' => 'submission statement accepted', 'mtable' => 'edusign', 'field' => 'name'),
        array('module' => 'edusign', 'action' => 'submit', 'mtable' => 'edusign', 'field' => 'name'),
        array('module' => 'edusign', 'action' => 'submit for grading', 'mtable' => 'edusign', 'field' => 'name'),
        array('module' => 'edusign', 'action' => 'unlock submission', 'mtable' => 'edusign', 'field' => 'name'),
        array('module' => 'edusign', 'action' => 'update', 'mtable' => 'edusign', 'field' => 'name'),
        array('module' => 'edusign', 'action' => 'upload', 'mtable' => 'edusign', 'field' => 'name'),
        array('module' => 'edusign', 'action' => 'view', 'mtable' => 'edusign', 'field' => 'name'),
        array('module' => 'edusign', 'action' => 'view all', 'mtable' => 'course', 'field' => 'fullname'),
        array('module' => 'edusign', 'action' => 'view confirm submit edusignment form', 'mtable' => 'edusign', 'field' => 'name'),
        array('module' => 'edusign', 'action' => 'view grading form', 'mtable' => 'edusign', 'field' => 'name'),
        array('module' => 'edusign', 'action' => 'view submission', 'mtable' => 'edusign', 'field' => 'name'),
        array('module' => 'edusign', 'action' => 'view submission grading table', 'mtable' => 'edusign', 'field' => 'name'),
        array('module' => 'edusign', 'action' => 'view submit edusignment form', 'mtable' => 'edusign', 'field' => 'name'),
        array('module' => 'edusign', 'action' => 'view feedback', 'mtable' => 'edusign', 'field' => 'name'),
        array('module' => 'edusign', 'action' => 'view batch set marking workflow state', 'mtable' => 'edusign', 'field' => 'name'),
);
