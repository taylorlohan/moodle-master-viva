<?php

//require_once(dirname(__FILE__).'/locallib.php');
require(dirname(dirname(dirname(__FILE__))).'\..\..\config.php');
require_once($CFG->dirroot . '/mod/assign/locallib.php');
global $USER,$CFG,$DB;
//$PAGE->set_url('/mod/assign/submission/onlineviva/upload.php');

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
    echo $submission_plugin->add_recording($video,$submission);
    echo 'upload new page contextid is '.$contextid;
    //alert();
} else {
    echo 'uploaded fail';
}

/*
$str = __DIR__;
$web_path = str_replace('\\','/',$str);
if (is_dir($web_path))
{
    define('WEBPATH', $web_path);
}

if (($_FILES['file']['type'] == "video/mp4"))
{
    if ($_FILES["file"]["error"] > 0)
    {
        echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
    }
    else
    {
        $tmp = $video["tmp_name"];
        $ext = "mp4";

        $filename = date("YmdHis").uniqid();
        $basename = $filename.".".$ext;

        $path = WEBPATH."/upload/";
        $is_path = is_dir($path);
        if($is_path==false) mkdir($path,0777,true);
        $new_path = $path.$basename;//new path 是要插入的文件的地址

        if (file_exists($new_path))
        {
            echo $_FILES["file"]["name"] . " already exists. ";
        }
        else
        {
            move_uploaded_file($tmp,$new_path);//放到了upload文件夹下
            $obj = new stdClass();
            $obj->assignment=$assignment;
            $obj->submission=$submission;
            $obj->videofile='test content1';//放什么内容?数量还是名字还是地址
            $fileid=$DB->insert_record('assignsubmission_onlineviva', $obj, true, false);//记录放入数据库
            echo 'file id is '.$fileid;
            echo 'upload into database';

            //创建一个新的file area文件
            $path=$CFG->dirroot.'/mod/assign/submission/onlineviva/upload/'.$basename;
            echo 'path is '.$path;//到此之前都正确
            if($fs = get_file_storage())//不能调用
                echo 'fs success';
            else
                echo 'fs fail';
            //$fs->get_area_files();
            $file_record = array(
                'contextid'=>$contextid,
                'component'=>'assignsubmission_onlineviva',
                'filearea'=>ASSIGN_FILEAREA_SUBMISSION_ONLINEVIVA,
                'itemid'=>$fileid,
                'filepath'=>'/',
                'filename'=>$basename
            );

            echo "file record is".$file_record;
            //$file = $fs->create_file_from_pathname($file_record, $path);
            echo "file area created!";
        }
    }
}
else
{
    echo "Invalid file";
}*/
