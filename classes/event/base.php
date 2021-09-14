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
 * The tool_tcpdffonts event base.
 *
 * File         base.php
 * Encoding     UTF-8
 *
 * @package     tool_tcpdffonts
 *
 * @copyright   2021 Ing. R.J. van Dongen
 * @author      Ing. R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_tcpdffonts\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The tool_tcpdffonts course module viewed event class.
 *
 * @package     tool_tcpdffonts
 *
 * @copyright   2021 Ing. R.J. van Dongen
 * @author      Ing. R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base extends \core\event\base {

    /**
     * Generic event name (used when generating locale-aware name)
     */
    const EVT_NAME = '';
    /**
     * Event action. Reflects the type of action performed (usually created, read, updated, deleted).
     */
    const EVT_ACTION = '';

    /**
     * Set basic properties for the event.
     */
    protected function init() {
        $this->data['objecttable'] = '';
        $this->data['crud'] = '0';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->context = \context_system::instance();
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string(static::EVT_NAME, 'tool_tcpdffonts');
    }

    /**
     * Returns non-localised event description with id's for admin use only.
     *
     * @return string
     */
    public function get_description() {
        return "The font with name '{$this->other['fontname']}' was ".static::EVT_ACTION." by the user with id '$this->userid'.";
    }

    /**
     * Returns relevant URL based on the anonymous mode of the response.
     * @return \moodle_url
     */
    public function get_url() {
        return null;
    }

    /**
     * Custom validations.
     *
     * @throws \coding_exception in case of any problems.
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->other['fontname'])) {
            throw new \coding_exception('The \'fontname\' field must be set in "other".');
        }
    }

    /**
     * Create event from a given font name
     *
     * @param string $fontname
     * @return static
     */
    public static function create_from_font($fontname) {
        $self = static::create([
            'other' => [
                'fontname' => $fontname
            ]
        ]);
        return $self;
    }

    /**
     * Trigger event using a given font name.
     *
     * @param string $fontname
     */
    public static function trigger_from_font($fontname) {
        $self = static::create_from_font($fontname);
        $self->trigger();
    }

}
