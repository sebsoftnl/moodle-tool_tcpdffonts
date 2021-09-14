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
 * Renderer class.
 *
 * File         renderer.php
 * Encoding     UTF-8
 *
 * @package     tool_tcpdffonts
 *
 * @copyright   2021 Ing. R.J. van Dongen
 * @author      Ing. R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_tcpdffonts\output;

defined('MOODLE_INTERNAL') or die('NO_ACCESS');

/**
 * tool_tcpdffonts_renderer
 *
 * @package     tool_tcpdffonts
 *
 * @copyright   2021 Ing. R.J. van Dongen
 * @author      Ing. R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {

    /**
     * Create a tab object with a nice image view, instead of just a regular tabobject
     *
     * @param string $id unique id of the tab in this tree, it is used to find selected and/or inactive tabs
     * @param string $pix image name
     * @param string $component component where the image will be looked for
     * @param string|moodle_url $link
     * @param string $text text on the tab
     * @param string $title title under the link, by defaul equals to text
     * @param bool $linkedwhenselected whether to display a link under the tab name when it's selected
     * @return \tabobject
     */
    protected function create_pictab($id, $pix = null, $component = null, $link = null,
            $text = '', $title = '', $linkedwhenselected = false) {
        $img = '';
        if (!empty($pix)) {
            $img = $this->pix_icon($pix, $title, $component, $attributes = null);
        }
        return new \tabobject($id, $link, $img . $text, empty($title) ? $text : $title, $linkedwhenselected);
    }

    /**
     * Render navigation tabs
     * @param string $active active tab
     * @param array $params extra params
     * @return string rendered tabs
     */
    public function tabs($active, $params = []) {
        global $CFG;
        $tabs = [];
        $inactive = null;
        $activated = null;

        $active1 = $active2 = null;
        if (strpos($active, '-') !== false) {
            list($active1, $active2) = explode('-', $active);
        } else {
            $active1 = $active;
        }

        $dashboardtab = $this->create_pictab('tcpdffonts', '', null,
                new \moodle_url($CFG->wwwroot . '/' . $CFG->admin . '/tool/tcpdffonts/index.php', $params),
                get_string('pluginname', 'tool_tcpdffonts'), '', true);
        if ($active1 == 'tcpdffonts' && !empty($active2)) {
            $dashboardtab->subtree[] = $this->create_pictab('tcpdffonts', 'i/up', null,
                    new \moodle_url($CFG->wwwroot . '/' . $CFG->admin . '/tool/tcpdffonts/index.php', $params),
                    get_string('backtolist', 'tool_tcpdffonts'), '', true);
        }
        if ($active1 == 'tcpdffonts' && $active2 === 'deletefont') {
            $dashboardtab->subtree[] = $this->create_pictab('tcpdffonts-deletefont', 'i/delete', null,
                    new \moodle_url($CFG->wwwroot . '/' . $CFG->admin . '/tool/tcpdffonts/index.php',
                            $params + ['action' => 'delete']),
                    get_string('font:delete', 'tool_tcpdffonts'), '', true);
        }
        if ($active1 == 'tcpdffonts' && $active2 === 'add') {
            $dashboardtab->subtree[] = $this->create_pictab('tcpdffonts-add', 't/add', null,
                    null, get_string('font:add', 'tool_tcpdffonts'), '', true);
        }
        if ($active1 == 'tcpdffonts' && $active2 === 'reset') {
            $dashboardtab->subtree[] = $this->create_pictab('tcpdffonts-reset', 'i/settings', null,
                    null, get_string('action:resetcorefonts', 'tool_tcpdffonts'), '', true);
        }
        if ($active1 == 'tcpdffonts' && $active2 === 'init') {
            $dashboardtab->subtree[] = $this->create_pictab('tcpdffonts-init', 'i/settings', null,
                    null, get_string('action:initcustomfonts', 'tool_tcpdffonts'), '', true);
        }
        if ($active1 == 'tcpdffonts' && $active2 === 'preview') {
            $dashboardtab->subtree[] = $this->create_pictab('tcpdffonts-preview', 'i/preview', null,
                    null, get_string('action:font:preview', 'tool_tcpdffonts'), '', true);
        }
        $tabs[] = $dashboardtab;

        return $this->tabtree($tabs, $active, $inactive);
    }


    /**
     * Fetch rendered font list
     *
     * @param string|\moodle_url $baseurl
     * @param bool $groupbyfamily
     * @return string
     */
    public function fontlist($baseurl, $groupbyfamily = false) {
        $widget = new component\fontlist($baseurl, $groupbyfamily);
        return $this->render_fontlist($widget);
    }

    /**
     * Render font list
     *
     * @param \tool_tcpdffonts\output\component\fontlist $widget
     * @return string
     */
    public function render_fontlist(component\fontlist $widget) {
        $context = $widget->export_for_template($this);
        return $this->render_from_template('tool_tcpdffonts/fonts', $context);
    }

}
