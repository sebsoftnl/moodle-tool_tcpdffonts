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
 * Services for tool_tcpdffonts.
 *
 * File         services.php
 * Encoding     UTF-8
 *
 * @package     tool_tcpdffonts
 *
 * @copyright   2021 Ing. R.J. van Dongen
 * @author      Ing. R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
    'tool_tcpdffonts_init_custom_fonts' => array(
        'classname'   => '\tool_tcpdffonts\external',
        'methodname'  => 'init_custom_fonts',
        'description' => 'Initialize custom fonts, aka create custom fonts (CONTRIB) folder and copy over all from core.',
        'type'        => 'write',
        'loginrequired' => 1,
        'ajax' => true
    ),
    'tool_tcpdffonts_core_reset' => array(
        'classname'   => '\tool_tcpdffonts\external',
        'methodname'  => 'core_reset',
        'description' => 'Reset custom fonts folder (aka delete it) so core can take over again.',
        'type'        => 'write',
        'loginrequired' => 1,
        'ajax' => true
    ),
    'tool_tcpdffonts_delete_font' => array(
        'classname'   => '\tool_tcpdffonts\external',
        'methodname'  => 'delete_font',
        'description' => 'Delete a font.',
        'type'        => 'write',
        'loginrequired' => 1,
        'ajax' => true
    ),

);
