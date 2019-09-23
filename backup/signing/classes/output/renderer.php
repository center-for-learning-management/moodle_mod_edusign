<?php
/**
 * Created by PhpStorm.
 * User: ishineguy
 * Date: 2018/06/26
 * Time: 13:16
 */

namespace edusignsubmission_signing\output;



defined('MOODLE_INTERNAL') || die();

class renderer extends \plugin_renderer_base {

    public function fetch_delete_submission(){

        $ds= \html_writer::tag('button',
            get_string('deletesubmission',constants::M_COMPONENT),
            array('type'=>'button','id'=>constants::M_COMPONENT .'_deletesubmissionbutton','class'=>constants::M_COMPONENT .'_deletesubmissionbutton btn btn-secondary'));

        return $ds;
    }

    public function prepare_current_submission($responses, $deletesubmission){
        $toggletext = \html_writer::tag('span',get_string('clicktoshow',constants::M_COMPONENT),array('class'=>'toggletext'));
        $togglebutton = \html_writer::tag('span','',array('class'=>'fa fa-2x fa-toggle-off togglebutton','aria-hidden'=>'true'));
        $toggle =\html_writer::div($togglebutton . $toggletext, constants::M_COMPONENT . '_togglecontainer');
        $cs = \html_writer::div($responses . $deletesubmission, constants::M_COMPONENT . '_currentsubmission',array('style'=>'display: none;'));
        return $toggle . $cs;
    }
}