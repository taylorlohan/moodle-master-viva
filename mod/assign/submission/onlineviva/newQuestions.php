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

require(dirname(dirname(dirname(__FILE__))).'\..\..\config.php');
global $USER, $DB, $CFG;

require_once("forms/question_form.php");

$PAGE->set_url('/mod/assign/submission/onlineviva/newQuestions.php');
//$PAGE->set_context(context_system::instance());
require_login();

$strpagetitle = get_string('onlineviva', 'assignsubmission_onlineviva');
$strpageheading = get_string('Addquestions', 'assignsubmission_onlineviva');

$PAGE->set_title($strpagetitle);
$PAGE->set_heading($strpageheading);

$assignmentid = optional_param('assignmentid', '', PARAM_INT);
$id = optional_param('id', '', PARAM_INT);

$maxquestion=getValue('maxquestion',$assignmentid);
$chosenquestion=getValue('chosenquestion',$assignmentid);

$mform = new question_form();
$toform = [];

// if no org ID then it is a new org
// if there is an orgid the we show the edit for with save
$mform = new question_form("?assignmentid=$assignmentid");
$toform = [];
//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
    redirect("/moodle-master/mod/assign/submission/onlineviva/addQuestions.php?assignmentid=$assignmentid", '', 10);
} elseif ($fromform = $mform->get_data()) {//executed when the form is submitted
    if ($id) {//insert new record
        $obj = $DB->get_record('onlineviva_questions', ['id'=>$id]);
        $obj->id=$id;
        $obj->content = $fromform->content;
        $obj->qorder=$fromform->qorder;
        $obj->assignment=$fromform->assignment;

        $str=$DB->update_record('onlineviva_questions', $obj);

    }
    else{//update record

        $count=$DB->count_records('onlineviva_questions',['assignment'=>$assignmentid]);
        if ($count<$maxquestion){
            $obj = new stdClass();
            $obj->qorder=$count+1;
            $obj->assignment=$assignmentid;
            $obj->content = $fromform->content;
            $orgid = $DB->insert_record('onlineviva_questions', $obj, true, false);

        }
        else{
            \core\notification::add('You have reached the maximum of the questions!', \core\output\notification::NOTIFY_WARNING);
        }

    }
    /*else {
        \core\notification::add('missing assignmentid or question id', \core\output\notification::NOTIFY_WARNING);
    }*/
    // redirect to units page with qual id
    redirect("/moodle-master/mod/assign/submission/onlineviva/addQuestions.php?assignmentid=$assignmentid", 'Changes saved', 10,  \core\output\notification::NOTIFY_SUCCESS);
} else {
    //this branch is working when the form is not cancelled or submitted, which means editing the question form
    if ($id) {
        $toform = $DB->get_record('onlineviva_questions', ['id'=>$id]);
    }
    //Set default data
    $mform->set_data($toform);

    echo $OUTPUT->header();
    $mform->display();

    echo $OUTPUT->footer();
}

function getValue($name, $assignmentid) {//get the specific value we want from the assign plugin configuration
    global $DB;
    $dbparams = array('assignment'=>$assignmentid,
        'plugin'=>'onlineviva',
        'name'=>$name
    );
    $current = $DB->get_record('assign_plugin_config', $dbparams, '*', IGNORE_MISSING);
    return $current->value;
}
