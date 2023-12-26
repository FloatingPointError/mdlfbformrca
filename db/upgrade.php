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
 * Upgrade steps for Feedback Form RCA
 *
 * Documentation: {@link https://moodledev.io/docs/guides/upgrade}
 *
 * @package    mod_fbformrca
 * @category   upgrade
 * @copyright  2023 Michel Labruyère <mlabrudev@proton.me>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Execute the plugin upgrade steps from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_fbformrca_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    $newfields = [];

    if ($oldversion < 2023122503 ) {
        // Define fields to be added to fbformrca.
        $table = new xmldb_table('fbformrca');
        $field = new xmldb_field('written_feedback', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'timemodified');
        $field2 = new xmldb_field('self_reflection', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'written_feedback');
        $field3 = new xmldb_field('graphs', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'self_reflection');
        $field4 = new xmldb_field('show_scores', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'graphs');

        // Add all above to newfields:
        $newfields[] = $field;
        $newfields[] = $field2;
        $newfields[] = $field3;
        $newfields[] = $field4;

        foreach ($newfields as $field) {
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }

        // Fbformrca savepoint reached.
        upgrade_mod_savepoint(true, 2023122503, 'fbformrca');
    }

    if ($oldversion < 2023122504) {

        // Define table fbformrcaskills to be created.
        $table = new xmldb_table('fbformrcaskills');

        // Adding fields to table fbformrcaskills.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('skill', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('programme', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);

        // Adding keys to table fbformrcaskills.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for fbformrcaskills.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Fbformrca savepoint reached.
        upgrade_mod_savepoint(true, 2023122504, 'fbformrca');
    }

    if ($oldversion < 2023122505) {

        // Define table fbformrcafeedback to be created.
        $table = new xmldb_table('fbformrcafeedback');

        // Adding fields to table fbformrcafeedback.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('instanceid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('studentid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('selfreflection', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('feedback', XMLDB_TYPE_BINARY, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table fbformrcafeedback.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('fk_instanceid', XMLDB_KEY_FOREIGN, ['instanceid'], 'fbformrca', ['id']);
        $table->add_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);

        // Conditionally launch create table for fbformrcafeedback.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Fbformrca savepoint reached.
        upgrade_mod_savepoint(true, 2023122505, 'fbformrca');
    }

    if ($oldversion < 2023122506) {

        // Define table fbformrca_skillsinstance to be created.
        $table = new xmldb_table('fbformrca_skillsinstance');

        // Adding fields to table fbformrca_skillsinstance.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('instanceid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('skillsid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table fbformrca_skillsinstance.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('fk_instanceid', XMLDB_KEY_FOREIGN, ['instanceid'], 'fbformrca', ['id']);
        $table->add_key('fk_skillsid', XMLDB_KEY_FOREIGN_UNIQUE, ['skillsid'], 'fbformrcaskills', ['id']);

        // Conditionally launch create table for fbformrca_skillsinstance.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Fbformrca savepoint reached.
        upgrade_mod_savepoint(true, 2023122506, 'fbformrca');
    }

    if ($oldversion < 2023122600) {

        // Define table fbformrcaskillgrades to be created.
        $table = new xmldb_table('fbformrcaskillgrades');

        // Adding fields to table fbformrcaskillgrades.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('instanceid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('studentid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('selfreflection', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('skillid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('score', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table fbformrcaskillgrades.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
        $table->add_key('fk_skillid', XMLDB_KEY_FOREIGN, ['skillid'], 'fbformrcaskills', ['id']);
        $table->add_key('fk_instanceid', XMLDB_KEY_FOREIGN, ['instanceid'], 'fbformrca', ['id']);

        // Define field available to be added to fbformrcaskills.
        $table2 = new xmldb_table('fbformrcaskills');
        $field = new xmldb_field('available', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'programme');
        $field2 = new xmldb_field('upperscore', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '20', 'available');

        // Conditionally launch add field available.
        if (!$dbman->field_exists($table2, $field)) {
            $dbman->add_field($table2, $field);
            $dbman->add_field($table2, $field2);
        }

        // Fbformrca savepoint reached.
        upgrade_mod_savepoint(true, 2023122600, 'fbformrca');
    }

    if ($oldversion < 2023122601) {

        // Define table fbformrcaskillgrades to be created.
        $table = new xmldb_table('fbformrcaskillgrades');

        // Adding fields to table fbformrcaskillgrades.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('instanceid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('studentid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('selfreflection', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
        $table->add_field('skillid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('score', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table fbformrcaskillgrades.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
        $table->add_key('fk_skillid', XMLDB_KEY_FOREIGN, ['skillid'], 'fbformrcaskills', ['id']);
        $table->add_key('fk_instanceid', XMLDB_KEY_FOREIGN, ['instanceid'], 'fbformrca', ['id']);

        // Conditionally launch create table for fbformrcaskillgrades.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Fbformrca savepoint reached.
        upgrade_mod_savepoint(true, 2023122601, 'fbformrca');
    }




    return true;
}
