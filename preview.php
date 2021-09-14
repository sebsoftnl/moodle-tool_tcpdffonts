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
 * Preview fonts script.
 *
 * This script is intended for IFrame loading and sets layout to embedded mode.
 *
 * File         preview.php
 * Encoding     UTF-8
 *
 * @package     tool_tcpdffonts
 *
 * @copyright   2021 Ing. R.J. van Dongen
 * @author      Ing. R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('tooltcpdffonts');

$strmanagefonts = get_string('tcpdffonts:managefonts', 'tool_tcpdffonts');
$url = new moodle_url("{$CFG->wwwroot}/{$CFG->admin}/tool/tcpdffonts/preview.php");

$PAGE->navbar->add($strmanagefonts, $url);
$PAGE->set_url($url);
$PAGE->set_title($strmanagefonts);
$PAGE->set_heading($strmanagefonts);
$PAGE->set_pagelayout('embedded');

$renderer = $PAGE->get_renderer('tool_tcpdffonts');
$controller = new \tool_tcpdffonts\local\controller($PAGE, $OUTPUT, $renderer);
$controller->execute_request();
