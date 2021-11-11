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
 * High level external interface library for tcpdf fonts
 *
 * File         external.php
 * Encoding     UTF-8
 *
 * @package     tool_tcpdffonts
 *
 * @copyright   2021 Ing. R.J. van Dongen
 * @author      Ing. R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_tcpdffonts;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . "/externallib.php");

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use context_system;
use Exception;

/**
 * External interface library for fonts
 *
 * @package     tool_tcpdffonts
 *
 * @copyright   2021 Ing. R.J. van Dongen
 * @author      Ing. R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {

    /**
     * Initialize custom fonts.
     * This initializes the standard fonts (CONTRIB) folder to your moodle data folder
     * and copies over all tcpdf/moodle core fonts.
     *
     * @return array
     */
    public static function init_custom_fonts() {
        // We always must pass webservice params through validate_parameters.
        self::validate_parameters(
            self::init_custom_fonts_parameters(), []
        );

        $context = context_system::instance();
        self::validate_context($context);
        require_capability('tool/tcpdffonts:managefonts', $context);

        $result = local\helper::initialise_custom_fonts();

        return (object)['result' => $result];
    }

    /**
     * Return function parameter definition for init_custom_fonts()
     *
     * @return external_function_parameters
     */
    public static function init_custom_fonts_parameters() {
        return new external_function_parameters([]);
    }

    /**
     * Return return definition for init_custom_fonts()
     */
    public static function init_custom_fonts_returns() {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'True if successful, false otherwise'),
            'error' => new external_value(PARAM_RAW, 'Error message if any', VALUE_OPTIONAL)
        ]);
    }

    /**
     * Core reset of fonts.
     * This performs a reset to core, aka remove ALL custom fonts and the indicated contrib folder
     *
     * @return array
     */
    public static function core_reset() {
        // We always must pass webservice params through validate_parameters.
        self::validate_parameters(
            self::core_reset_parameters(), []
        );

        $context = context_system::instance();
        self::validate_context($context);
        require_capability('tool/tcpdffonts:managefonts', $context);

        $result = local\helper::reset_custom_fonts();

        return (object)['result' => $result];
    }

    /**
     * Return function parameter definition for core_reset()
     *
     * @return external_function_parameters
     */
    public static function core_reset_parameters() {
        return new external_function_parameters([]);
    }

    /**
     * Return return definition for core_reset()
     *
     * @return external_single_structure
     */
    public static function core_reset_returns() {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'True if successful, false otherwise'),
            'error' => new external_value(PARAM_RAW, 'Error message if any', VALUE_OPTIONAL)
        ]);
    }

    /**
     * Delete a font
     *
     * @param string $fontidentifier the internal font name
     * @return array
     */
    public static function delete_font($fontidentifier) {
        // We always must pass webservice params through validate_parameters.
        $params = self::validate_parameters(
            self::delete_font_parameters(), [
                'fontidentifier' => $fontidentifier
            ]
        );

        $context = context_system::instance();
        self::validate_context($context);
        require_capability('tool/tcpdffonts:managefonts', $context);

        try {
            local\helper::delete_font($params['fontidentifier']);
            return (object)['result' => true];
        } catch (Exception $ex) {
            (object)['result' => true, 'error' => $ex->getMessage()];
        }
    }

    /**
     * Return function parameter definition for delete_font()
     *
     * @return external_function_parameters
     */
    public static function delete_font_parameters() {
        return new external_function_parameters([
            'fontidentifier' => new external_value(PARAM_ALPHANUMEXT, 'font identifier')
        ]);
    }

    /**
     * Return return definition for delete_font()
     */
    public static function delete_font_returns() {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'True if successful, false otherwise'),
            'error' => new external_value(PARAM_RAW, 'Error message if any', VALUE_OPTIONAL)
        ]);
    }

}
