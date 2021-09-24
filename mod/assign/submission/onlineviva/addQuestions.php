<?php
require(dirname(dirname(dirname(__FILE__))).'\..\..\config.php');
global $USER,$CFG,$DB;

$PAGE->set_url('/mod/assign/submission/onlineviva/addQuestions.php');
require_login();
$assignmentid=optional_param('assignmentid', 3,PARAM_INT);

$dbparams = array('assignment'=>$assignmentid);
$records = $DB->get_records('onlineviva_questions', $dbparams, '', '*');
$results = new stdClass();
$results->data = array_values($records);
$results->assignmentid=$assignmentid;


echo $OUTPUT->header();

echo $OUTPUT->render_from_template('assignsubmission_onlineviva/addQuestions', $results);

echo $OUTPUT->footer();
