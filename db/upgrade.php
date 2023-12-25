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

    return true;
}
