<?php
require(dirname(dirname(dirname(__FILE__))).'\..\..\config.php');
global $USER, $DB, $CFG;

//defined('MOODLE_INTERNAL') || die();
$PAGE->set_url('/mod/assign/submission/onlineviva/deleteQuestions.php');


require_login();


$id = optional_param('id', '', PARAM_INT);
$assignmentid= optional_param('assignmentid', '', PARAM_INT);

$dbparams = array('id'=>$id,
);
if($DB->delete_records('onlineviva_questions', $dbparams))
    redirect("/moodle-master/mod/assign/submission/onlineviva/addQuestions.php?assignmentid=$assignmentid", 'Question deleted!', 10,  \core\output\notification::NOTIFY_SUCCESS);


