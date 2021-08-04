<?php
//改动的是assignment的submission type的勾选框
$settings->add(new admin_setting_configcheckbox('assignsubmission_onlineviva/default',
    new lang_string('default', 'assignsubmission_onlineviva'),
    new lang_string('default_help', 'assignsubmission_onlineviva'), 0));

