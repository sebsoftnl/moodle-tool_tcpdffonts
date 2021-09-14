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
 * Upload form
 *
 * File         upload.php
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
class upload extends \moodleform {

    /**
     * Form definition
     */
    protected function definition() {
        $mform = & $this->_form;

        $mform->addElement('static', '_head', '', '<h4>' . get_string('uploadfontfile:header', 'tool_tcpdffonts') . '</h4>');
        $mform->addElement('static', '_desc', '', get_string('uploadfontfile:description', 'tool_tcpdffonts'));

        $options = [
            'maxbytes' => 0,
            'accepted_types' => $this->_customdata['types']
        ];
        $mform->addElement('filepicker', 'userfile', get_string('fontfile', 'tool_tcpdffonts'), null, $options);
        $mform->setType('confirm', PARAM_BOOL);

        $mform->addElement('static', '_fsdesc', '', get_string('addfont:explain', 'tool_tcpdffonts'));

        // Font type setting.
        $mform->addElement('select', 'fonttype', get_string('fonttype', 'tool_tcpdffonts'), helper::fonttype_options());

        // Font enc setting.
        $mform->addElement('text', 'enc', get_string('fontencid', 'tool_tcpdffonts'));
        $mform->setType('enc', PARAM_TEXT);
        $mform->addHelpButton('enc', 'fontenc', 'tool_tcpdffonts');

        // Font flags setting.
        $flagoptions = [
            4 => get_string('nonsymbolfont', 'tool_tcpdffonts'),
            32 => get_string('symbolfont', 'tool_tcpdffonts')
        ];
        $mform->addElement('select', 'flags', get_string('fontflags', 'tool_tcpdffonts'), $flagoptions);
        $mform->addHelpButton('flags', 'fontflags', 'tool_tcpdffonts');

        // Font platform ID setting.
        $platformsettings = [
            1 => 'Macintosh',
            3 => 'Windows'
        ];
        $mform->addElement('select', 'platid', get_string('fontplatid', 'tool_tcpdffonts'), $platformsettings);
        $mform->addHelpButton('platid', 'fontplatid', 'tool_tcpdffonts');
        $mform->setDefault('platid', 3);

        // Font encoding ID setting.
        $encidsettings = [
            0 => 'Symbol',
            1 => 'Unicode',
            2 => 'ShiftJIS',
            3 => 'PRC',
            4 => 'Big5',
            5 => 'Wansung',
            6 => 'Johab',
            7 => 'Reserved',
            8 => 'Reserved',
            9 => 'Reserved',
            10 => 'UCS-4'
        ];
        $mform->addElement('select', 'encid', get_string('fontencid', 'tool_tcpdffonts'), $encidsettings);
        $mform->addHelpButton('encid', 'fontencid', 'tool_tcpdffonts');
        $mform->setDefault('encid', 1);

        // Font cbbox setting.
        $mform->addElement('advcheckbox', 'addcbbox', get_string('fontaddcbbox', 'tool_tcpdffonts'));
        $mform->addHelpButton('addcbbox', 'fontaddcbbox', 'tool_tcpdffonts');
        $mform->setDefault('addcbbox', 1);

        // Font cbbox setting.
        $mform->addElement('advcheckbox', 'forceoverwrite', get_string('fontforceoverwrite', 'tool_tcpdffonts'));
        $mform->setDefault('forceoverwrite', 0);

        if (empty($this->_customdata['nobuttons'])) {
            $this->add_action_buttons();
        }
    }

}
