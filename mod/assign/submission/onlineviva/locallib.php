<?php

defined('MOODLE_INTERNAL') || die();
define('ASSIGN_FILEAREA_SUBMISSION_ONLINEVIVA', 'submission_onlineviva');
define('ASSIGN_SUBMISSION_ONLINEVIVA_MAX_SUMMARY_FILES', 25);//这里应该是configurable
require_once($CFG->libdir . '/portfoliolib.php');

class assign_submission_onlineviva extends assign_submission_plugin
{

    /**
     * Get the name of the online text submission plugin
     * @return string
     */
    public function get_name()
    {
        return get_string('onlineviva', 'assignsubmission_onlineviva');
    }

    /**
     * Get viva submission information from the database
     *
     * @param  int $submissionid
     * @return mixed
     */
    private function get_onlineviva_submission($submissionid) {
        global $DB;
        return $DB->get_record('assignsubmission_onlineviva', array('submission'=>$submissionid));
    }

    public function remove(stdClass $submission) {
        global $DB;

        $submissionid = $submission ? $submission->id : 0;
        if ($submissionid) {
            $DB->delete_records('assignsubmission_onlineviva', array('submission' => $submissionid));
        }
        return true;
    }
    public function get_settings(MoodleQuickForm $mform)
    {
        $assignmentid=$this->assignment->get_instance()->id;

        $timelimit = $this->get_config('timelimit');
        $chancelimit = $this->get_config('chancelimit');
        $maxquestion = $this->get_config('maxquestion');
        $chosenquestion = $this->get_config('chosenquestion');

        //time limit setting
        $mform->addElement('duration', 'assignsubmission_onlineviva_timelimit',
            get_string('timelimit', 'assignsubmission_onlineviva'));
        $mform->setDefault('assignsubmission_onlineviva_timelimit', $timelimit);
        $mform->disabledIf('assignsubmission_onlineviva_timelimit',
            'assignsubmission_onlineviva_enabled', 'notchecked');
        $mform->hideIf('assignsubmission_onlineviva_timelimit',
            'assignsubmission_onlineviva_enabled',
            'notchecked');

        //attempt chances setting
        $chanceoptions = array('1', '2', '3', '4', '5');
        $mform->addElement('select', 'assignsubmission_onlineviva_chancelimit',
            get_string('chancelimit', 'assignsubmission_onlineviva'), $chanceoptions);
        $mform->setDefault('assignsubmission_onlineviva_chancelimit', $chancelimit);
        $mform->disabledIf('assignsubmission_onlineviva_chancelimit',
            'assignsubmission_onlineviva_enabled', 'notchecked');
        $mform->hideIf('assignsubmission_onlineviva_chancelimit',
            'assignsubmission_onlineviva_enabled',
            'notchecked');


        $name = get_string('maxquestion', 'assignsubmission_onlineviva');
        $nums=array("assignmentid"=> $assignmentid,
                     );

        //add questions in the setting page
        $url=new moodle_url('../mod/assign/submission/onlineviva/addQuestions.php', $nums);
        $ds= \html_writer::tag('button',
            get_string('maxquestion_add','assignsubmission_onlineviva'),
            array('type'=>'button','id'=>'addQuestionbtn','onclick'=>"window.open('{$url}' );"));

        $attributes = array('size' => '20');
        $maxquestiongrp = array();
        $maxquestiongrp[] = $mform->createElement('text', 'assignsubmission_onlineviva_maxquestion',
            get_string('maxquestion', 'assignsubmission_onlineviva'), $attributes);
        $maxquestiongrp[] = $mform->createElement('static','addQuestionbtn',
            get_string('maxquestion_add','assignsubmission_onlineviva'),$ds);
        $mform->addGroup($maxquestiongrp, 'assignsubmission_onlineviva_question_group', $name, ' ', false);

        $mform->setDefault('assignsubmission_onlineviva_maxquestion', $maxquestion);
        $mform->disabledIf('assignsubmission_onlineviva_maxquestion',
            'assignsubmission_onlineviva_enabled', 'notchecked');
        $mform->setType('assignsubmission_onlineviva_maxquestion', PARAM_INT);
        $mform->hideIf('assignsubmission_onlineviva_maxquestion',
            'assignsubmission_onlineviva_enabled',
            'notchecked');

        //chosenquestions
        $mform->addElement('text', 'assignsubmission_onlineviva_chosenquestion',
            get_string('chosenquestion', 'assignsubmission_onlineviva'), $attributes);
        $mform->setDefault('assignsubmission_onlineviva_chosenquestion', $chosenquestion);
        $mform->disabledIf('assignsubmission_onlineviva_chosenquestion',
            'assignsubmission_onlineviva_enabled', 'notchecked');
        $mform->setType('assignsubmission_onlineviva_chosenquestion', PARAM_INT);
        $mform->hideIf('assignsubmission_onlineviva_chosenquestion',
            'assignsubmission_onlineviva_enabled',
            'notchecked');
    }



    public function save_settings(stdClass $data) {

            if (empty($data->assignsubmission_onlineviva_timelimit)) {
                $timelimit = 0;
            } else {
                $timelimit = $data->assignsubmission_onlineviva_timelimit;
            }

            $this->set_config('timelimit', $timelimit);
            //$this->set_config('wordlimitenabled', $wordlimitenabled);

            //attempt chances limit
            if (empty($data->assignsubmission_onlineviva_chancelimit)) {
                $chancelimit = 0;
            } else {
                $chancelimit = $data->assignsubmission_onlineviva_chancelimit;
            }

            $this->set_config('chancelimit', $chancelimit+1);

            if (empty($data->assignsubmission_onlineviva_maxquestion)) {
                $maxquestion = 0;
            } else {
                $maxquestion = $data->assignsubmission_onlineviva_maxquestion;
            }

            $this->set_config('maxquestion', $maxquestion);

            if (empty($data->assignsubmission_onlineviva_chosenquestion)) {
                $chosenquestion = 0;
            } else {
                $chosenquestion = $data->assignsubmission_onlineviva_chosenquestion;
            }

            $this->set_config('chosenquestion', $chosenquestion);
            return true;
        }

        public function get_form_elements($submission, MoodleQuickForm $mform, stdClass $data) {

            $cm=$this->assignment->get_course_module()->id;
            $chancelimit = $this->get_config('chancelimit');
            $submissionid = $submission ? $submission->id : 0;
            $nums=array("submission"=> $submissionid, "cmid"=>$cm);

            //start recording page
            $url=new moodle_url('submission/onlineviva/recording.php',$nums);
            $ds= \html_writer::tag('button',
                get_string('startviva','assignsubmission_onlineviva'),
                array('type'=>'button','id'=>'startviva','onclick'=>"window.open('{$url}' );"));
            $mform->addElement('static','startvivabtn',
                get_string('startviva','assignsubmission_onlineviva'),$ds);

            if($chancelimit<=$this->count_files($submissionid,ASSIGN_FILEAREA_SUBMISSION_ONLINEVIVA)){
                $mform->addElement('static','chances', 'attempt chances','You have reached the maximum number of attempt chances!');
            }
            else{
                $mform->addElement('static','chances', 'attempt chances',$chancelimit);

            }
            $mform->addElement('static', 'onlinevivafiles',
                get_string('vivafiles','assignsubmission_onlineviva'),$this->print_user_files($submissionid));

        }

    public function print_user_files($submissionid)
    {
        $contextid=$this->assignment->get_context()->id;
        $fs = get_file_storage();
        $files = $fs->get_area_files($contextid, 'assignsubmission_onlineviva',
            'submission_onlineviva',
            $submissionid, "id",);
        if($files){
            foreach ($files as $file) {
                $filename = $file->get_filename();
                $filepath = $file->get_filepath();
                $mimetype = $file->get_mimetype();
                $url = moodle_url::make_pluginfile_url($file->get_contextid(),
                    $file->get_component(), $file->get_filearea(),
                    $file->get_itemid(), $filepath, $filename, true);

                $output .= '<a href="' . $url . '">' . s($filename) . ' </a> ';
        }
            return $output;
        }
        else{
            $filename='no files uploaded!';
            $url=new moodle_url('submission/onlineviva/recording.php');
            $output='<a href="' . $url . '">'.s($filename).' </a> ';
            return $output;
        }
    }

    public function get_files(stdClass $submission, stdClass $user) {
        $result = array();
        $fs = get_file_storage();

        $files = $fs->get_area_files($this->assignment->get_context()->id,
            'assignsubmission_onlineviva',
            ASSIGN_FILEAREA_SUBMISSION_ONLINEVIVA,
            $submission->id,
            'timemodified',
            false);

        foreach ($files as $file) {
            // Do we return the full folder path or just the file name?
            if (isset($submission->exportfullpath) && $submission->exportfullpath == false) {
                $result[$file->get_filename()] = $file;
            } else {
                $result[$file->get_filepath().$file->get_filename()] = $file;
            }
        }
        return $result;
    }

    public function view_summary(stdClass $submission, & $showviewlink) {
        $count = $this->count_files($submission->id, ASSIGN_FILEAREA_SUBMISSION_ONLINEVIVA);

        // Show we show a link to view all files for this plugin?
        $showviewlink = $count > ASSIGN_SUBMISSION_ONLINEVIVA_MAX_SUMMARY_FILES;
        if ($count <= ASSIGN_SUBMISSION_ONLINEVIVA_MAX_SUMMARY_FILES) {
            return $this->assignment->render_area_files('assignsubmission_onlineviva',
                ASSIGN_FILEAREA_SUBMISSION_ONLINEVIVA,
                $submission->id);
        } else {
            return get_string('countfiles', 'assignsubmission_onlineviva', $count);
        }
    }

    public function view(stdClass $submission) {
        return $this->assignment->render_area_files('assignsubmission_onlineviva',
            ASSIGN_FILEAREA_SUBMISSION_ONLINEVIVA,
            $submission->id);
    }

    public function can_upgrade($type, $version) {
        if ($type == 'onlineviva') {
            return true;
        }
        return false;
    }

    public function add_recording($video, $submission) {
        global $USER, $DB;
        $fs = get_file_storage();
        $filesubmission = $this->get_onlineviva_submission($submission->id);
        $chancelimit = $this->get_config('chancelimit');

        $contextid = $this->assignment->get_context()->id;

        $filename = $video['name'];
        $filesrc = $video['tmp_name'];

        if (!is_uploaded_file($filesrc)) {
            return false;
        }
        if($this->count_files($submission->id,ASSIGN_FILEAREA_SUBMISSION_ONLINEVIVA)>=$chancelimit){
            return false;
        }

        $ext = "mp4";

        $temp_name=basename($filename,".$ext");
        $temp_name=trim(clean_param($temp_name, PARAM_FILE),".");
        $filename=$temp_name.".$ext";
        $n=1;
        while($fs->file_exists($contextid, 'assignsubmission_onlineviva',
            ASSIGN_FILEAREA_SUBMISSION_ONLINEVIVA, $submission->id, '/', $filename))
        {
            $filename=$temp_name.'_'.$n++.".$ext";
        }
        $author = $DB->get_record('user', array('id'=>$USER->id ), '*', MUST_EXIST);
        // Create file
        $fileinfo = array(
            'contextid' => $contextid,
            'component' => 'assignsubmission_onlineviva',
            'filearea' => ASSIGN_FILEAREA_SUBMISSION_ONLINEVIVA,
            'itemid' => $submission->id,
            'filepath' => '/',
            'filename' => $filename,
            'userid'=>$USER->id,
            'author'=>fullname($author)

        );
        if ($newfile = $fs->create_file_from_pathname($fileinfo, $filesrc)) {
            $files = $fs->get_area_files($contextid, 'assignsubmission_onlineviva',
                ASSIGN_FILEAREA_SUBMISSION_ONLINEVIVA, $submission->id, "id", false);
            $count = $this->count_files($submission->id, ASSIGN_FILEAREA_SUBMISSION_ONLINEVIVA);
           //add the submission record into the database tables
            if ($filesubmission) {
                $filesubmission->videofile = $this->count_files($submission->id, ASSIGN_FILEAREA_SUBMISSION_ONLINEVIVA);
                return $DB->update_record('assignsubmission_onlineviva', $filesubmission);
            } else {
                $filesubmission = new stdClass();
                $filesubmission->videofile = $this->count_files($submission->id, ASSIGN_FILEAREA_SUBMISSION_ONLINEVIVA);
                $filesubmission->submission = $submission->id;
                $filesubmission->assignment = $this->assignment->get_instance()->id;
                return $DB->insert_record('assignsubmission_onlineviva', $filesubmission) > 0;
            }
        }
        else
            return false;
    }

    public function upgrade(context $oldcontext, stdClass $oldassignment, stdClass $oldsubmission, stdClass $submission, & $log) {
        global $DB;

        $filesubmission = new stdClass();

        $filesubmission->numfiles = $oldsubmission->numfiles;
        $filesubmission->submission = $submission->id;
        $filesubmission->assignment = $this->assignment->get_instance()->id;

        if (!$DB->insert_record('assignsubmission_onlineviva', $filesubmission) > 0) {
            $log .= get_string('couldnotconvertsubmission', 'mod_assign', $submission->userid);
            return false;
        }

        // now copy the area files
        $this->assignment->copy_area_files_for_upgrade($oldcontext->id,
            'mod_assignment',
            'submission',
            $oldsubmission->id,
            // New file area
            $this->assignment->get_context()->id,
            'assignsubmission_onlineviva',
            ASSIGN_FILEAREA_SUBMISSION_ONLINEVIVA,
            $submission->id);

        return true;
    }
    public function is_empty(stdClass $submission) {
        return $this->count_files($submission->id, ASSIGN_FILEAREA_SUBMISSION_ONLINEVIVA) == 0;
    }

    private function count_files($submissionid, $area) {

        $fs = get_file_storage();
        $files = $fs->get_area_files($this->assignment->get_context()->id, 'assignsubmission_onlineviva',
            $area, $submissionid, "id", false);

        return count($files);
    }


    public function get_file_areas() {
        return array(ASSIGN_FILEAREA_SUBMISSION_ONLINEVIVA=>$this->get_name());
    }

    public function format_for_log(stdClass $submission) {
        // format the info for each submission plugin add_to_log
        $filecount = $this->count_files($submission->id, ASSIGN_FILEAREA_SUBMISSION_ONLINEVIVA);
        $fileloginfo = '';
        $fileloginfo .= ' the number of file(s) : ' . $filecount . " file(s).<br>";

        return $fileloginfo;
    }

    public function delete_instance() {
        global $DB;
        // will throw exception on failure
        $DB->delete_records('assignsubmission_onlineviva', array('assignment'=>$this->assignment->get_instance()->id));

        return true;
    }


        }
