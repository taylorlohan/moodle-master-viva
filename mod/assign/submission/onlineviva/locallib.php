<?php

defined('MOODLE_INTERNAL') || die();
define('ASSIGN_FILEAREA_SUBMISSION_ONLINEVIVA', 'submission_onlineviva');
define('ASSIGN_SUBMISSION_ONLINEVIVA_MAX_SUMMARY_FILES', 5);//这里应该是configurable


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
    private function get_onlinetext_submission($submissionid) {
        global $DB;
        //需要数据库设计
        return $DB->get_record('assignsubmission_onlineviva', array('submission'=>$submissionid));
    }

    public function remove(stdClass $submission) {//不知道可不可以用
        global $DB;

        $submissionid = $submission ? $submission->id : 0;
        if ($submissionid) {
            $DB->delete_records('assignsubmission_onlineviva', array('submission' => $submissionid));
        }
        return true;
    }
    public function get_settings(MoodleQuickForm $mform)
    {
        global $CFG, $COURSE;
        $assignmentid=$this->assignment->get_instance()->id;

        $timelimit = $this->get_config('timelimit');
        $chancelimit = $this->get_config('chancelimit');
        $maxquestion = $this->get_config('maxquestion');
        $chosenquestion = $this->get_config('chosenquestion');

        //时间限制
        $mform->addElement('duration', 'assignsubmission_onlineviva_timelimit', get_string('timelimit', 'assignsubmission_onlineviva'));
        $mform->setDefault('assignsubmission_onlineviva_timelimit', $timelimit);
        $mform->disabledIf('assignsubmission_onlineviva_timelimit', 'assignsubmission_onlineviva_enabled', 'notchecked');//能找到enabled吗
        $mform->hideIf('assignsubmission_onlineviva_timelimit',
            'assignsubmission_onlineviva_enabled',
            'notchecked');

        //允许回答次数
        $chanceoptions = array('1', '2', '3', '4', '5');
        $mform->addElement('select', 'assignsubmission_onlineviva_chancelimit', get_string('chancelimit', 'assignsubmission_onlineviva'), $chanceoptions);
        $mform->setDefault('assignsubmission_onlineviva_chancelimit', $chancelimit);
        $mform->disabledIf('assignsubmission_onlineviva_chancelimit', 'assignsubmission_onlineviva_enabled', 'notchecked');
        $mform->hideIf('assignsubmission_onlineviva_chancelimit',
            'assignsubmission_onlineviva_enabled',
            'notchecked');


        $name = get_string('maxquestion', 'assignsubmission_onlineviva');
        $nums=array("assignmentid"=> $assignmentid,
                     );


        //添加问题页面按钮
        $url=new moodle_url('../mod/assign/submission/onlineviva/addQuestions.php', $nums);
        $ds= \html_writer::tag('button',
            get_string('maxquestion_add','assignsubmission_onlineviva'),
            array('type'=>'button','id'=>'addQuestionbtn','onclick'=>"window.open('{$url}' );"));

        $attributes = array('size' => '20');//size设置正确吗？
        $maxquestiongrp = array();
        $maxquestiongrp[] = $mform->createElement('text', 'assignsubmission_onlineviva_maxquestion', get_string('maxquestion', 'assignsubmission_onlineviva'), $attributes);
        $maxquestiongrp[] = $mform->createElement('static','addQuestionbtn',
            get_string('maxquestion_add','assignsubmission_onlineviva'),$ds);
        //$maxquestiongrp[] = $mform->createElement('button', 'add questions', get_string('maxquestion_add', 'assignsubmission_onlineviva'));
        $mform->addGroup($maxquestiongrp, 'assignsubmission_onlineviva_question_group', $name, ' ', false);
        //$mform->addElement('text', 'assignsubmission_onlineviva_maxquestion', get_string('maxquestion', 'assignsubmission_onlineviva'), $attributes);
        $mform->setDefault('assignsubmission_onlineviva_maxquestion', $maxquestion);
        $mform->disabledIf('assignsubmission_onlineviva_maxquestion', 'assignsubmission_onlineviva_enabled', 'notchecked');
        //$questionlimitgrprules = array();
        //$questionlimitgrprules['onlineviva_maxquestion'][] = array(null, 'numeric', null, 'client');
        //$mform->addGroupRule('onlineviva_maxquestion', $questionlimitgrprules);
        $mform->setType('assignsubmission_onlineviva_maxquestion', PARAM_INT);
        $mform->hideIf('assignsubmission_onlineviva_maxquestion',
            'assignsubmission_onlineviva_enabled',
            'notchecked');

        //chosenquestions
        $mform->addElement('text', 'assignsubmission_onlineviva_chosenquestion', get_string('chosenquestion', 'assignsubmission_onlineviva'), $attributes);
        $mform->setDefault('assignsubmission_onlineviva_chosenquestion', $chosenquestion);
        $mform->disabledIf('assignsubmission_onlineviva_chosenquestion', 'assignsubmission_onlineviva_enabled', 'notchecked');
        $mform->setType('assignsubmission_onlineviva_chosenquestion', PARAM_INT);
        $mform->hideIf('assignsubmission_onlineviva_chosenquestion',
            'assignsubmission_onlineviva_enabled',
            'notchecked');
    }



        public function save_settings(stdClass $data) {
            //时间限制
            if (empty($data->assignsubmission_onlineviva_timelimit)) {
                $timelimit = 0;
            } else {
                $timelimit = $data->assignsubmission_onlineviva_timelimit;
            }

            $this->set_config('timelimit', $timelimit);
            //$this->set_config('wordlimitenabled', $wordlimitenabled);

            //允许回答次数
            if (empty($data->assignsubmission_onlineviva_chancelimit)) {
                $chancelimit = 0;
            } else {
                $chancelimit = $data->assignsubmission_onlineviva_chancelimit;
            }

            $this->set_config('chancelimit', $chancelimit);

            //问题数量
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
            global $CFG, $USER, $PAGE;
            $timelimit = $this->get_config('timelimit');
            $chancelimit = $this->get_config('chancelimit');
            $chosenquestion=$this->get_config('chosenquestion');
            $submissionid = $submission ? $submission->id : 0;

            $opts=array("component"=> 'assignsubmission_onlineviva',
                "timelimit"=> $timelimit,
                "chancelimit"=>$chancelimit,
                "chosenquestion"=> $chosenquestion,
            );

            //开始录音页面点击出现
            $url=new moodle_url('submission/onlineviva/recording.php',$opts);
            $ds= \html_writer::tag('button',
                get_string('startviva','assignsubmission_onlineviva'),
                array('type'=>'button','id'=>'startviva','onclick'=>"window.open('{$url}' );"));
            $mform->addElement('static','startvivabtn',
                get_string('startviva','assignsubmission_onlineviva'),$ds);

        }

    public function get_files(stdClass $submission, stdClass $user=null) {
        $result = array();
        $fs = get_file_storage();

        $files = $fs->get_area_files($this->assignment->get_context()->id, 'assignsubmission_onlineviva', ASSIGN_FILEAREA_SUBMISSION_ONLINEVIVA, $submission->id, "timemodified", false);

        foreach ($files as $file) {
            $result[$file->get_filename()] = $file;
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
        return $this->assignment->render_area_files('assignsubmission_onlineviva', ASSIGN_FILEAREA_SUBMISSION_ONLINEVIVA, $submission->id);
    }

    public function can_upgrade($type, $version) {
        if ($type == 'onlineviva') {
            return true;
        }
        return false;
    }

    /*public function upgrade_settings(context $oldcontext,stdClass $oldassignment, & $log) {
        // Old assignment plugin ran out of vars so couldn't do max files, just default to module max
        $this->set_config('maxfilesubmissions', ASSIGN_MAX_SUBMISSION_ONLINERECORDINGS);
        $this->set_config('defaultname', $oldassignment->var2);
        $this->set_config('nameoverride', $oldassignment->var3);
        return true;
    }*/

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
            'assignsubmission_onlineaudio',
            ASSIGN_FILEAREA_SUBMISSION_ONLINEAUDIO,
            $submission->id);

        return true;
    }
    public function is_empty(stdClass $submission) {
        return $this->count_files($submission->id, ASSIGN_FILEAREA_SUBMISSION_ONLINEVIVA) == 0;
    }

    private function count_files($submissionid, $area) {

        $fs = get_file_storage();
        $files = $fs->get_area_files($this->assignment->get_context()->id, 'assignsubmission_onlineviva', $area, $submissionid, "id", false);

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
