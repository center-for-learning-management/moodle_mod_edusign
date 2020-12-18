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
 * Confirm deletion of signature.
 *
 * @package    mod_edusign
 * @copyright  2020 David Bogner <david.bogner@wunderbyte.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.9
 */
require(['core/str', 'core/notification’], function(str, notification) {
    let linkid = "#edusign-delete-signature";
    let href = $(linkid).attr('href');
    str.get_strings([
        {'key' : 'delete'},
        {'key' : 'deletesignature', component : 'edusign'},
        {'key' : 'yes'},
        {'key' : 'no'},
    ]).done(function(s) {
            notification.confirm(s[0], s[1], s[2], s[3], function() {
                window.location.href = href;
            });
        }
    ).fail(notification.exception);
});
