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
 * Confirmation form
 *
 * File         confirmation.php
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
 * Confirmation form
 *
 * @package     tool_tcpdffonts
 *
 * @copyright   2021 Ing. R.J. van Dongen
 * @author      Ing. R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * */
class confirmation extends \moodleform {

    /**
     * Form definition
     */
    protected function definition() {
        $mform = & $this->_form;

        list($headertext, $description, $confirmmessage) = $this->_customdata['confirmation'];

        $mform->addElement('static', '_head', '', '<h4>' . $headertext . '</h4>');
        $mform->addElement('static', '_desc', '', $description);

        $mform->addElement('advcheckbox', 'confirm', '', $confirmmessage, null, array(0, 1));
        $mform->setType('confirm', PARAM_BOOL);

        if (empty($this->_customdata['nobuttons'])) {
            $this->add_action_buttons();
        }
    }

    /**
     * Verify if this was a validated POST and confirmed.
     * This utility method can be used when processing POSTbacks.
     *
     * @return boolean
     */
    public function is_confirmed() {
        if (!$this->is_validated()) {
            return false;
        }
        $data = $this->get_data();
        return (bool)$data->confirm;
    }

}
