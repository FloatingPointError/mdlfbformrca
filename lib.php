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

/**
 * Callback implementations for Feedback Form RCA
 *
 * Documentation: {@link https://moodledev.io/docs/apis/plugintypes/mod}
 *
 * @package    mod_fbformrca
 * @copyright  2023 Michel Labruyère <mlabrudev@proton.me>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * List of features supported in module
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know or string for the module purpose.
 */
function fbformrca_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_MOD_PURPOSE:
            return MOD_PURPOSE_CONTENT;
        default:
            return null;
    }
}

/**
 * Add Feedback Form RCA instance
 *
 * Given an object containing all the necessary data, (defined by the form in mod_form.php)
 * this function will create a new instance and return the id of the instance
 *
 * @param stdClass $moduleinstance form data
 * @param mod_fbformrca_mod_form $form the form
 * @return int new instance id
 */
function fbformrca_add_instance($moduleinstance, $form = null) {
    global $DB;

    $moduleinstance->timecreated = time();
    $moduleinstance->timemodified = time();

    $id = $DB->insert_record('fbformrca', $moduleinstance);
    $completiontimeexpected = !empty($moduleinstance->completionexpected) ? $moduleinstance->completionexpected : null;
    \core_completion\api::update_completion_date_event($moduleinstance->coursemodule,
        'fbformrca', $id, $completiontimeexpected);
    return $id;
}

/**
 * Updates an instance of the Feedback Form RCA in the database.
 *
 * Given an object containing all the necessary data (defined in mod_form.php),
 * this function will update an existing instance with new data.
 *
 * @param stdClass $moduleinstance An object from the form in mod_form.php
 * @param mod_fbformrca_mod_form $form The form
 * @return bool True if successful, false otherwis
 */
function fbformrca_update_instance($moduleinstance, $form = null) {
    global $DB;

    $moduleinstance->timemodified = time();
    $moduleinstance->id = $moduleinstance->instance;

    $DB->update_record('fbformrca', $moduleinstance);

    $completiontimeexpected = !empty($moduleinstance->completionexpected) ? $moduleinstance->completionexpected : null;
    \core_completion\api::update_completion_date_event($moduleinstance->coursemodule, 'fbformrca',
      $moduleinstance->id, $completiontimeexpected);

    return true;
}

/**
 * Removes an instance of the Feedback Form RCA from the database.
 *
 * @param int $id Id of the module instance
 * @return bool True if successful, false otherwise
 */
function fbformrca_delete_instance($id) {
    global $DB;

    $record = $DB->get_record('fbformrca', ['id' => $id]);
    if (!$record) {
        return false;
    }

    // Delete all calendar events.
    $events = $DB->get_records('event', ['modulename' => 'fbformrca', 'instance' => $record->id]);
    foreach ($events as $event) {
        calendar_event::load($event)->delete();
    }

    // Delete the instance.
    $DB->delete_records('fbformrca', ['id' => $id]);

    return true;
}
