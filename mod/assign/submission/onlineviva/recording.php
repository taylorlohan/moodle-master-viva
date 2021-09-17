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

$PAGE->set_url('/mod/assign/submission/onlineviva/recording.php');

//$PAGE->set_context(context_module::instance());

require_login();

//$assignment=optional_param('assignment', '10', PARAM_INT);
$submission=optional_param('submission', 11, PARAM_INT);
$dbparams = array('id'=>$submission,
);
$record = $DB->get_record('assign_submission', $dbparams, '*', IGNORE_MISSING);
$assignmentid=$record->assignment;

function getValue($name, $assignmentid) {//get the specific value we want from the assign plugin configuration
    global $DB;
    $dbparams = array('assignment'=>$assignmentid,
        'plugin'=>'onlineviva',
        'name'=>$name
    );
    $current = $DB->get_record('assign_plugin_config', $dbparams, '*', IGNORE_MISSING);
    return $current->value;
}

$obj = new stdClass();
/*
$obj->chancelimit = $chancelimit;
$obj->chosenquestion = $chosenquestion;*/
//$obj->data=array_values($records);
$obj->assignment =$assignmentid;
$obj->submission = $submission;
$obj->timelimit = getValue('timelimit',$assignmentid);

//$PAGE->requires->js_call_amd('/mod/assign/submission/onlineviva/amd/src/functions.js', 'init', array($obj));
//$PAGE->requires->js('/mod/assign/submission/onlineviva/amd/src/functions.js');

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('assignsubmission_onlineviva/recording', $obj);
//$PAGE->requires->js_call_amd('/mod/assign/submission/onlineviva/amd/src/functions.js','init', array($obj));
echo $OUTPUT->footer();
