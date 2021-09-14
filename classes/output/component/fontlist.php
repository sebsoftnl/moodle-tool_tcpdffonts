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
 * fontlist component
 *
 * File         fontlist.php
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

require_once($CFG->dirroot . '/lib/pdflib.php');

use stdClass;
use tool_tcpdffonts\local\helper;

/**
 * fontlist
 *
 * @package     tool_tcpdffonts
 *
 * @copyright   2021 Ing. R.J. van Dongen
 * @author      Ing. R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class fontlist implements \templatable, \renderable {

    /**
     * Base url
     *
     * @var string|moodle_url
     */
    protected $baseurl;
    /**
     * Whether or not to group by family name
     *
     * @var bool
     */
    protected $groupbyfamily = false;

    /**
     * Create new instance
     *
     * @param string|moodle_url $baseurl
     * @param bool $groupbyfamily
     */
    public function __construct($baseurl, $groupbyfamily = false) {
        $this->baseurl = $baseurl;
        $this->groupbyfamily = $groupbyfamily;
    }

    /**
     * Export variables for use in template.
     *
     * @param \renderer_base $output
     * @return stdClass
     */
    public function export_for_template(\renderer_base $output) {
        $rs = new stdClass;

        $rs->locationcustomized = false;
        $rs->groupedbyfamily = $this->groupbyfamily;
        $rs->fonts = [];
        $rs->actions = [];

        // We only have generic actions if we're NOT core.
        // These are "high level" actions.
        if (helper::is_customized()) {
            $rs->locationcustomized = true;
            $rs->actions['add'] = (new addfont($this->baseurl))->export_for_template($output);
            $rs->actions['addzipped'] = (new addzippedfont($this->baseurl))->export_for_template($output);
            $rs->actions['reset'] = (new resetcorefonts($this->baseurl))->export_for_template($output);
        } else {
            $rs->actions['init'] = (new initcustomfonts($this->baseurl))->export_for_template($output);
        }
        $rs->actions['togglefamilyview'] = (new togglefamilyview($this->baseurl))->export_for_template($output);

        // Load fonts list.
        $fontslist = helper::fetch_font_list();
        // Specialise between "all" view and "family" view.
        if ($this->groupbyfamily) {
            $rs->fonts = [];
            foreach ($fontslist as $font) {
                if (!isset($rs->fonts[$font['family']])) {
                    $rs->fonts[$font['family']] = [
                        'name' => $font['family'],
                        'family' => $font['family'],
                        'type' => $font['type'],
                        'iscore' => $font['iscore'],
                        'iscorerestriction' => $font['iscorerestriction'],
                        'styles' => []
                    ];
                }
                $rs->fonts[$font['family']]['styles'][] = empty($font['style']) ? 'Regular' : $font['style'];
                $rs->fonts[$font['family']]['actions'] = [];
                // IF and only IF this is not core...
                if (helper::actions_allowed($font['family'], $rs->fonts[$font['family']]['iscorerestricted'])) {
                    // Delete action.
                    $rs->fonts[$font['family']]['actions']['delete'] = (new deletefont($this->baseurl, $font['family'], true))
                        ->export_for_template($output);
                }
                // Downloading and previewing fonts should always be possible.
                $rs->fonts[$font['family']]['actions']['download'] = (new downloadfont($this->baseurl, $font['family'], true))
                    ->export_for_template($output);
                $rs->fonts[$font['family']]['actions']['preview'] = (new previewfont($this->baseurl, $font['family'], true))
                    ->export_for_template($output);
            }
            // Flatten styles.
            foreach ($rs->fonts as &$font) {
                $font['styles'] = implode(',', $font['styles']);
            }
            unset($font); // Always unset references!
        } else {
            $rs->fonts = $fontslist;
            foreach ($rs->fonts as $fontname => &$font) {
                $font['actions'] = [];
                // IF and only IF this is not core...
                if (helper::actions_allowed($fontname, $rs->fonts[$fontname]['iscorerestricted'])) {
                    // Delete action.
                    $font['actions']['delete'] = (new deletefont($this->baseurl, $fontname, false))
                        ->export_for_template($output);
                }
                // Downloading and previewing fonts should always be possible.
                $font['actions']['download'] = (new downloadfont($this->baseurl, $fontname, false))
                    ->export_for_template($output);
                $font['actions']['preview'] = (new previewfont($this->baseurl, $fontname, false))
                    ->export_for_template($output);
            }
        }
        ksort($rs->fonts);

        $rs->fonts = array_values($rs->fonts);
        $rs->hasfonts = (count($rs->fonts) > 0);

        return $rs;
    }

}
