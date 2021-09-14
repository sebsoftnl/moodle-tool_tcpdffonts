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
 * Upload zip form
 *
 * File         uploadzip.php
 * Encoding     UTF-8
 *
 * @package     tool_tcpdffonts
 *
 * @copyright   2021 Ing. R.J. van Dongen
 * @author      Ing. R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * */

namespace tool_tcpdffonts\local;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Upload form
 *
 * @package     tool_tcpdffonts
 *
 * @copyright   2021 Ing. R.J. van Dongen
 * @author      Ing. R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * */
class uploadzip extends \moodleform {

    /**
     * Form definition
     */
    protected function definition() {
        $mform = & $this->_form;

        $mform->addElement('static', '_head', '', '<h4>' . get_string('uploadzipfile:header', 'tool_tcpdffonts') . '</h4>');
        $mform->addElement('static', '_desc', '', get_string('uploadzipfile:description', 'tool_tcpdffonts'));

        $options = [
            'maxbytes' => 0,
            'accepted_types' => $this->_customdata['types']
        ];
        $mform->addElement('filepicker', 'userfile', get_string('fontfile', 'tool_tcpdffonts'), null, $options);
        $mform->setType('confirm', PARAM_BOOL);

        if (empty($this->_customdata['nobuttons'])) {
            $this->add_action_buttons();
        }
    }

}
