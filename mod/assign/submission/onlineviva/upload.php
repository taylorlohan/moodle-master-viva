<?php

require(dirname(dirname(dirname(__FILE__))).'\..\..\config.php');
require_once($CFG->dirroot . '/mod/assign/locallib.php');
global $USER,$CFG,$DB;

$video = (isset($_FILES['file'])) ? $_FILES['file'] : 'video not found';
$submission=$_POST['submission'];
$assignment=$_POST['assignment'];
echo 'submission id is '.$submission;

$record=$DB->get_record('assign', ['id'=>$assignment], '*', IGNORE_MISSING);
$courseid=$record->course;

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

$cm = get_coursemodule_from_instance('assign', $assignment, $courseid);
$context = context_module::instance($courseid);
$contextid = $context->id;


$newassignment = new assign($context,$cm,$course);
$submission_plugin = $newassignment->get_submission_plugin_by_type('onlineviva');
$submission = $newassignment->get_user_submission($USER->id, true);
if($submission_plugin->add_recording($video,$submission)) {
    echo 'uploaded success!';
} else {
    echo 'uploaded fail';
}