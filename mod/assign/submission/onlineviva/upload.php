<?php

//defined('MOODLE_INTERNAL') || die();
require_once(dirname(__FILE__).'/locallib.php');


$audio = (isset($_FILES['audio'])) ? $_FILES['audio'] : 'audio not found';//通过Ajax的post方法传送得到的files
echo 'audio prepare to upload';
echo var_dump($audio);
echo 'audio uploaded!';
//echo $audio;