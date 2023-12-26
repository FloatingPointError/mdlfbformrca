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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * Form for adding and editing Feedback Form RCA instances
 *
 * @package    mod_fbformrca
 * @copyright  2023 Michel Labruyère <mlabrudev@proton.me>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_fbformrca_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        // General fieldset.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name'), ['size' => '64']);
        $mform->setType('name', empty($CFG->formatstringstriptags) ? PARAM_CLEANHTML : PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        if (!empty($this->_features->introeditor)) {
            // Description element that is usually added to the General fieldset.
            $this->standard_intro_elements();
        }

        // Instance settings.
        $mform->addElement('header', 'feedbackformrcasettings', get_string('feedbackformrcasettings', 'fbformrca'));

        $mform->addElement('checkbox', 'written_feedback', get_string('feedback', 'fbformrca'));
        $mform->addHelpButton('written_feedback', 'feedback', 'fbformrca');

        $mform->addElement('checkbox', 'self_reflection', get_string('self_reflection', 'fbformrca'));
        $mform->setDefault('self_reflection', 1);

        // Add graphs yes or no?
        $mform->addElement('checkbox', 'graphs', get_string('graphs', 'fbformrca'));
        $mform->setDefault('graphs', 1);
        $mform->addHelpButton('graphs', 'graphs', 'fbformrca');

        // Show scores in graphs?
        $mform->addElement('checkbox', 'show_scores', get_string('scores', 'fbformrca'));
        $mform->setDefault('show_scores', 0);
        $mform->addHelpButton('show_scores', 'scores', 'fbformrca');


        // The following goes in another table: fbformrca_skillsinstance. Fix this later...
        // The skillset for the select element will be loaded later from the fbformrcaskills table.
        // TODO: fix this in the model and controller.
        $mform->addElement('select', 'skillsid', get_string('skills', 'fbformrca'), ['1' => '1', '2' => '2', '3' => '3']);
        $mform->addHelpButton('skillsid', 'skills', 'fbformrca');
        $mform->addRule('skillsid', null, 'required', null, 'client');

        // Other standard elements that are displayed in their own fieldsets.
        $this->standard_grading_coursemodule_elements();
        $this->standard_coursemodule_elements();

        $this->add_action_buttons();
    }
}
