<?php
require_once($CFG->dirroot . '/course/moodleform_mod.php');

class mod_geogebra_mod_form extends moodleform_mod {

    function definition() {
        global $CFG, $COURSE;
        
        $mform = & $this->_form;

        // ------ Adding the "general" fieldset, where all the common settings are showed ------
        $mform->addElement('header', 'general', get_string('general', 'form'));


        // Adding fields
        $mform->addElement('text', 'name', get_string('name', 'geogebra'), array('size' => 64));

        $mform->addElement('htmleditor', 'intro', get_string('description', 'geogebra'));
        $mform->setType('intro', PARAM_RAW);
        $mform->addRule('intro', null, 'required', null, 'client');
        $mform->setHelpButton('intro', array('writing', 'questions', 'richtext'), false, 'editorhelpbutton');

        // ------ Adding the "settings" fieldset, where the module settings are showed ------
        $mform->addElement('header', 'settings', get_string('settings'));
        
        $mform->addElement('choosecoursefile', 'filename', get_string('filename', 'geogebra'), array('maxlength' => 255, 'size' => 64));
        $filenamegrprules = array();
        $filenamegrprules['value'][] = array(get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $filenamegrprules['value'][] = array(null, 'required', null, 'client');
        $mform->addGroupRule('filename', $filenamegrprules);
        $mform->addRule('filename', null, 'required', null, 'client');
        
        $mform->setHelpButton('filename', array('filename', get_string('filename', 'geogebra'), 'geogebra'));        
        
        $mform->addElement('text', 'width', get_string('width', 'geogebra'), array('size' => 2, 'value' => 800));
        $mform->addElement('text', 'height', get_string('height', 'geogebra'), array('size' => 2, 'value' => 600));

        $options = geogebra_get_languages();
        $mform->addElement('select', 'language', get_string('language', 'geogebra'), $options);
        $mform->setDefault('language', substr($CFG->lang, 0, -5));
        
        //From flashapplet module
        $mform->addElement('hidden', 'showsubmit', 1);

        $functionalityoptionsgrp=array();
        $functionalityoptionsgrp[] = &$mform->createElement('checkbox', 'enableRightClick', '', get_string('enableRightClick', 'geogebra'));
        $functionalityoptionsgrp[] = &$mform->createElement('checkbox', 'enableLabelDrags', '', get_string('enableLabelDrags', 'geogebra'));
        $functionalityoptionsgrp[] = &$mform->createElement('checkbox', 'showResetIcon', '', get_string('showResetIcon', 'geogebra'));
        $mform->addGroup($functionalityoptionsgrp, 'functionalityoptionsgrp', get_string('functionalityoptionsgrp', 'geogebra'), "<br>", false);
        $mform->setDefault('enableRightClick', 0);
        $mform->setDefault('enableLabelDrags', 0);
        $mform->setDefault('showResetIcon', 0);
        $mform->setAdvanced('functionalityoptionsgrp');
        
        $interfaceoptionsgrp=array();
        $interfaceoptionsgrp[] = &$mform->createElement('checkbox', 'showMenuBar', '', get_string('showMenuBar', 'geogebra'));
        $interfaceoptionsgrp[] = &$mform->createElement('checkbox', 'showToolBar', '', get_string('showToolBar', 'geogebra'));
        $interfaceoptionsgrp[] = &$mform->createElement('checkbox', 'showToolBarHelp', '', get_string('showToolBarHelp', 'geogebra'));
        //$interfaceoptionsgrp[] = &$mform->createElement('checkbox', 'showAlgebraInput', '', get_string('showAlgebraInput', 'geogebra'));
        $mform->addGroup($interfaceoptionsgrp, 'interfaceoptionsgrp', get_string('interfaceoptionsgrp', 'geogebra'), "<br>", false);
        $mform->setDefault('showMenuBar', 0);
        $mform->setDefault('showToolBar', 0);
        $mform->setDefault('showToolBarHelp', 0);
        //$mform->setDefault('showAlgebraInput', 0);
        $mform->setAdvanced('interfaceoptionsgrp');


        // ------ Adding the "grades" fieldset, where all the grade settings are showed ------
        $mform->addElement('header', 'grades', get_string('grades'));

        $mform->addElement('modgrade', 'grade', get_string('grade'));
        $mform->setDefault('grade', 100);
        
        $maxattempts = array('-1' => get_string('unlimited'));
        for ($i = 1; $i <= 10; $i++) {
            $maxattempts[$i] = "$i";
        }
        $mform->addElement('select', 'maxattempts', get_string('maxattempts', 'geogebra'), $maxattempts);
        $mform->setDefault('maxattempts', '1');
        
  //      $mform->disabledIf('maxattempts', 'grade', 'gt' , 0);
        
        $grademethod = array(
            GEOGEBRA_AVERAGE_GRADE => get_string('average', 'geogebra'),
            GEOGEBRA_HIGHEST_GRADE => get_string('highestattempt', 'geogebra'),
            GEOGEBRA_LOWEST_GRADE => get_string('lowestattempt', 'geogebra'),
            GEOGEBRA_FIRST_GRADE => get_string('firstattempt', 'geogebra'),
            GEOGEBRA_LAST_GRADE => get_string('lastattempt', 'geogebra'),
            GEOGEBRA_NO_GRADING => get_string('nograding', 'geogebra'));
        $mform->addElement('select', 'grademethod', get_string('grademethod', 'geogebra'), $grademethod);
        $mform->disabledIf('grademethod', 'grade', 'eq' , 0);
/*
        for ($i = 1; $i <= 100; $i++) {
            $grades[$i] = "$i";
        }
        $mform->addElement('select', 'maxgrade', get_string('maximumgrade'), $grades);
        $mform->setDefault('maxgrade', 10);
*/

        $mform->addElement('date_time_selector', 'timeavailable', get_string('availabledate', 'assignment'), array('optional' => true));
        $mform->setDefault('timeavailable', time());
        $mform->addElement('date_time_selector', 'timedue', get_string('duedate', 'assignment'), array('optional' => true));
        $mform->setDefault('timedue', time() + 7 * 24 * 3600);

        // Setting types

        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }

        $mform->setType('width', PARAM_INT);
        $mform->setType('height', PARAM_INT);
        $mform->setType('showsubmit', PARAM_BOOL);

        // Setting rules
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addRule('width', null, 'required', null, 'client');
        $mform->addRule('height', null, 'required', null, 'client');

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    function set_data($question) {
        if (isset($question->url)) {
            parse_str($question->url, $attributes);
            $question->filename = $attributes['filename'];
            $question->enableRightClick = isset($attributes['enableRightClick'])?$attributes['enableRightClick']:0;
            $question->enableLabelDrags = isset($attributes['enableLabelDrags'])?$attributes['enableLabelDrags']:0;
            $question->showResetIcon = isset($attributes['showResetIcon'])?$attributes['showResetIcon']:0;
            $question->showMenuBar = isset($attributes['showMenuBar'])?$attributes['showMenuBar']:0;
            $question->showToolBar = isset($attributes['showToolBar'])?$attributes['showToolBar']:0;
            $question->showToolBarHelp = isset($attributes['showToolBarHelp'])?$attributes['showToolBarHelp']:0;
            //$question->showAlgebraInput = isset($attributes['showAlgebraInput'])?$attributes['showAlgebraInput']:0;

        }
        return parent::set_data($question);
    }
    
    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $validate = geogebra_validate($data);

        if (!$validate->result) {
            $errors = $errors + $validate->errors;
        }

        return $errors;
    }
    

}
?>