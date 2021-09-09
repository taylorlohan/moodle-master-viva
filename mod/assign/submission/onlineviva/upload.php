<?php

//defined('MOODLE_INTERNAL') || die();
//require_once(dirname(__FILE__).'/locallib.php');

print "hello upload";
$video = (isset($_FILES['file'])) ? $_FILES['file'] : 'video not found';
echo var_dump($video);
echo $_POST['name'];//传输基本数据类型的对象要用post对象接收，传输blob文件要用files对象接收

if (($_FILES['file']['type'] == "video/mp4"))//单双引号？
{
    if ($_FILES["file"]["error"] > 0)
    {
        echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
    }
    else
    {
        echo "Upload: " . $_FILES["file"]["name"] . "<br />";
        echo "Type: " . $_FILES["file"]["type"] . "<br />";
        //echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
        echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";

        if (file_exists("upload/" . $_FILES["file"]["name"]))
        {
            echo $_FILES["file"]["name"] . " already exists. ";
        }
        else
        {
            move_uploaded_file($_FILES["file"]["tmp_name"],
                "upload/" . $_FILES["file"]["name"]);
            echo "Stored in: " . "upload/" . $_FILES["file"]["name"];//将文件地址放入数据库
        }
    }
}
else
{
    echo "Invalid file";
}

/*上传服务器
 * $ext = $_POST['idx'] == 1 ? '.ogg' : '.mp4';

if(isset($_FILES['file']) and !$_FILES['file']['error']) {
  $fname = "audio_" . $_POST['sessID'] . $ext;

  move_uploaded_file($_FILES['file']['tmp_name'], "../../data/wav/" . $fname);
}
 * */