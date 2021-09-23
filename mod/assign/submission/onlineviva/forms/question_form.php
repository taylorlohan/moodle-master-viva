<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

require_once("$CFG->libdir/formslib.php");

class question_form extends moodleform
{
    public function definition()
    {
        global $CFG;

        $mform = $this->_form;
        $mform->addElement('html', '<strong>Question form</strong><br><br>');


        $mform->addElement('text', 'content', 'content of a question', ' size="100%" ');
        $mform->setType('content', PARAM_TEXT);
        $mform->setDefault('content', '');


        $buttonarray=array();
        $buttonarray[] = $mform->createElement('submit', 'Submit', 'Save');
        $buttonarray[] = $mform->createElement('cancel');
        $mform->addgroup($buttonarray, 'buttonar', '', ' ', false);

    }
    public function validation($data, $files)
    {
        return array();
    }
}
