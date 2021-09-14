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
 * Preview font action.
 *
 * File         previewfont.php
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
 * tool_tcpdffonts\output\component\previewfont
 *
 * @package     tool_tcpdffonts
 *
 * @copyright   2021 Ing. R.J. van Dongen
 * @author      Ing. R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class previewfont implements renderable, templatable {

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
     * Whether or not to add link text next to icon
     *
     * @var bool
     */
    protected $linktext = false;
    /**
     * Font "instance".
     *
     * @var string
     */
    protected $instance;
    /**
     * Does the given font "instance" represent a whole font family?
     *
     * @var bool
     */
    protected $instanceisfontfamily;

    /**
     * Create new instance
     *
     * @param string|moodle_url $baseurl
     * @param string $instance
     * @param bool $instanceisfontfamily
     * @param string $actionstring
     */
    public function __construct($baseurl, $instance, $instanceisfontfamily = false, $actionstring = null) {
        $this->baseurl = $baseurl;
        $this->instance = $instance;
        $this->instanceisfontfamily = $instanceisfontfamily;
        if (empty($actionstring)) {
            $this->actionstring = get_string('action:font:preview', 'tool_tcpdffonts');
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
            'data-action' => 'preview',
            'data-font' => $this->instance,
            'data-isfamily' => $this->instanceisfontfamily ? 1 : 0,
            'data-contextid' => $context->id,
            'class' => 'action-icon action-icon-hide'
        ];
        return (object) [
            'action' => $output->action_icon(
                    new moodle_url($this->baseurl, array('action' => 'preview',
                        'id' => $this->instance, 'isfamily' => $this->instanceisfontfamily ? 1 : 0)),
                    new pix_icon('t/preview', $this->actionstring, 'moodle', ['class' => 'icon']),
                    null,
                    ['alt' => $this->actionstring] + $dataactionattributes,
                    $this->linktext
                )
        ];
    }

}
