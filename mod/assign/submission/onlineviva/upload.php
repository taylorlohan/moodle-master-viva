<?php

//defined('MOODLE_INTERNAL') || die();
//require_once(dirname(__FILE__).'/locallib.php');

print "hello upload";
$video = (isset($_FILES['file'])) ? $_FILES['file'] : 'video not found';
echo var_dump($video);
echo $_POST['name'];//传输基本数据类型的对象要用post对象接收，传输blob文件要用files对象接收

$str = __DIR__;
$web_path = str_replace('\\','/',$str);
if (is_dir($web_path))
{
    define('WEBPATH', $web_path);
}

if (($_FILES['file']['type'] == "video/mp4"))//单双引号？
{
    if ($_FILES["file"]["error"] > 0)
    {
        echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
    }
    else
    {
        $tmp = $video["tmp_name"];
        //命名方案1
        $ext = "mp4"; //提取扩展名

        $filename = date("YmdHis").uniqid();//生成唯一的名称
        $basename = $filename.".".$ext;//拼接完整的命名
//        $basename = $data["upload"]["avatar"]["name"] 获取原有名称的信息

        $path = WEBPATH."/upload/";
        $is_path = is_dir($path);
        if($is_path==false) mkdir($path,0777,true);
        $new_path = $path.$basename;

        if (file_exists($new_path))
        {
            echo $_FILES["file"]["name"] . " already exists. ";
        }
        else
        {
            move_uploaded_file($tmp,$new_path);
            echo "Stored in: " . $new_path;//将文件地址放入数据库
        }
    }
}
else
{
    echo "Invalid file";
}
