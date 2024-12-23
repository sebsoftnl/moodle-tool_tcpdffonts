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
 * Initialise custom fonts action.
 *
 * File         initcustomfonts.php
 * Encoding     UTF-8
 *
 * @package     tool_tcpdffonts
 *
 * @copyright   2021 RvD
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_tcpdffonts\output\component;

use renderable;
use templatable;
use moodle_url;
use pix_icon;

/**
 * tool_tcpdffonts\output\output\component\initcustomfonts
 *
 * @package     tool_tcpdffonts
 *
 * @copyright   2021 RvD
 * @author      RvD <helpdesk@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class initcustomfonts implements renderable, templatable {

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
     * Create new instance
     *
     * @param string|moodle_url $baseurl
     * @param string $actionstring
     */
    public function __construct($baseurl, $actionstring = null) {
        $this->baseurl = $baseurl;
        if (empty($actionstring)) {
            $this->actionstring = get_string('action:initcustomfonts', 'tool_tcpdffonts');
        } else {
            $this->actionstring = $actionstring;
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
            'data-action' => 'initcustomfonts',
            'data-contextid' => $context->id,
            'class' => 'action-icon action-icon-hide',
        ];
        return (object) [
            'action' => $output->action_link(
                    new moodle_url($this->baseurl, ['action' => 'initcustomfonts']),
                    $this->actionstring,
                    null,
                    ['alt' => $this->actionstring] + $dataactionattributes,
                    new pix_icon('t/edit', $this->actionstring, 'moodle', ['class' => 'icon'])
                ),
        ];
    }

}
