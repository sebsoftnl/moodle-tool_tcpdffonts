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
 * Add custom fonts action.
 *
 * File         togglefamilyview.php
 * Encoding     UTF-8
 *
 * @package     tool_tcpdffonts
 *
 * @copyright   2021 Ing. R.J. van Dongen
 * @author      Ing. R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_tcpdffonts\output\component;

defined('MOODLE_INTERNAL') or die('NO_ACCESS');

use renderable;
use templatable;
use moodle_url;
use pix_icon;

/**
 * tool_tcpdffonts\output\component\togglefamilyview
 *
 * @package     tool_tcpdffonts
 *
 * @copyright   2021 Ing. R.J. van Dongen
 * @author      Ing. R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class togglefamilyview implements renderable, templatable {

    /**
     * Base url
     *
     * @var string|moodle_url
     */
    protected $baseurl;
    /**
     * Action string
     *
     * @var string
     */
    protected $actionstring;
    /**
     * Whether or not the current state is a grouped view
     *
     * @var bool
     */
    protected $familyview = false;

    /**
     * Create new instance
     *
     * @param string|moodle_url $baseurl
     */
    public function __construct($baseurl) {
        $this->baseurl = $baseurl;
        $this->familyview = (bool)get_user_preferences('tool_fontmanager_familyview', 1);
        if ($this->familyview) {
            $this->actionstring = get_string('toggle:familyview:on', 'tool_tcpdffonts');
        } else {
            $this->actionstring = get_string('toggle:familyview:off', 'tool_tcpdffonts');
        }
    }

    /**
     * Export variables for template
     *
     * @param \renderer_base $output
     * @return object
     */
    public function export_for_template(\renderer_base $output) {
        $context = \context_system::instance();
        $dataactionattributes = [
            'data-action' => 'togglefamilyview',
            'data-contextid' => $context->id,
            'class' => 'action-icon action-icon-hide'
        ];

        $icon = $this->familyview ? 'i/group' : 'i/user';
        return (object) [
            'action' => $output->action_link(
                    new moodle_url($this->baseurl, array('action' => 'togglefamilyview',
                        'value' => $this->familyview ? 0 : 1)),
                    $this->actionstring,
                    null,
                    ['alt' => $this->actionstring] + $dataactionattributes,
                    new pix_icon($icon, $this->actionstring, 'moodle', ['class' => 'icon'])
                )
        ];
    }

}
