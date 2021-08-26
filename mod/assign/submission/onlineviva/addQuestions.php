<?php
require(dirname(dirname(dirname(__FILE__))).'\..\..\config.php');
//require_once(dirname(__FILE__).'/locallib.php');
global $USER,$CFG,$DB;

$PAGE->set_url('/mod/assign/submission/onlineviva/addQuestions.php');
//$PAGE->set_context(context_module::instance());需要设定context?
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
