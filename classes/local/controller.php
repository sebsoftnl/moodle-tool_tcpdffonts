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
 * The tool_tcpdffonts main controller class.
 *
 * File         controller.php
 * Encoding     UTF-8
 *
 * @package     tool_tcpdffonts
 *
 * @copyright   2021 Ing. R.J. van Dongen
 * @author      Ing. R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_tcpdffonts\local;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/pdflib.php');

use context_system;

/**
 * The tool_tcpdffonts controller class.
 *
 * @package     tool_tcpdffonts
 *
 * @copyright   2021 Ing. R.J. van Dongen
 * @author      Ing. R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class controller {

    /**
     * @var \moodle_page
     */
    protected $page;
    /**
     * @var \core_renderer
     */
    protected $output;
    /**
     * @var \tool_tcpdffonts\output\renderer
     */
    protected $renderer;

    /**
     * Create new administration instance
     * @param \moodle_page $page
     * @param \core\output_renderer $output
     * @param \core_renderer|null $renderer
     */
    public function __construct($page, $output, $renderer = null) {
        $this->page = $page;
        $this->output = $output;
        $this->renderer = $renderer;
    }

    /**
     * Execute page request
     */
    public function execute_request() {
        $action = optional_param('action', null, PARAM_ALPHA);
        switch ($action) {
            case 'resetcorefonts':
                $this->process_resetcorefonts();
                break;
            case 'initcustomfonts':
                $this->process_initcustomfonts();
                break;
            case 'deletefont':
                $this->process_deletefont();
                break;
            case 'downloadfont':
                $this->process_downloadfont();
                break;
            case 'preview':
                $this->process_preview();
                break;
            case 'previewfont':
                $this->process_previewfont();
                break;
            case 'addfont':
                $this->process_addfont();
                break;
            case 'addzippedfont':
                $this->process_addzippedfont();
                break;
            case 'togglefamilyview':
                $this->process_toggle_familyview();
                break;
            case 'list':
            default:
                $this->process_index();
                break;
        }
    }

    /**
     * Get url based on current page url
     * @param array $mergeparams any params to merge
     * @return \moodle_url
     */
    protected function get_url($mergeparams = []) {
        $url = $this->page->url;
        $url->params($mergeparams);
        return $url;
    }

    /**
     * Process front page / font overview
     */
    protected function process_toggle_familyview() {
        $context = context_system::instance();
        require_capability('tool/tcpdffonts:viewfonts', $context);

        set_user_preference('tool_fontmanager_familyview', optional_param('value', 1, PARAM_INT));
        redirect($this->get_url());
    }

    /**
     * Process front page / font overview
     */
    protected function process_index() {
        $context = context_system::instance();
        require_capability('tool/tcpdffonts:viewfonts', $context);

        $familyview = (bool)get_user_preferences('tool_fontmanager_familyview', 1);

        echo $this->renderer->header();
        echo $this->renderer->tabs('tcpdffonts');
        echo $this->renderer->fontlist($this->get_url(), $familyview);
        echo $this->renderer->footer();
    }

    /**
     * Process adding a font.
     */
    protected function process_addfont() {
        $context = context_system::instance();
        require_capability('tool/tcpdffonts:managefonts', $context);

        // Register font mime types.
        helper::add_mimetypes_for_upload();
        $uploadform = new \tool_tcpdffonts\local\upload($this->get_url([
            'action' => 'addfont'
        ], ['types' => ['.ttf', '.otf']]));

        if ($uploadform->is_cancelled()) {
            redirect($this->get_url());
        } else if ($data = $uploadform->get_data()) {
            // Unregister font mimetypes.
            helper::remove_mimetypes_for_upload();
            // Store user file in temp location.
            $temppath = make_request_directory();
            $name = $uploadform->get_new_filename('userfile');
            $fullpath = rtrim($temppath, '/') . '/' . $name;
            $success = $uploadform->save_file('userfile', $fullpath, true);
            if ($success) {
                // Pre-flight check.
                $error = null;
                $canprocess = helper::check_font_file($fullpath, $error);
                if (!$canprocess) {
                    \core\notification::error($error);
                    redirect($this->get_url());
                }

                $fontpathfound = null;
                $fontexists = helper::has_font($fullpath, $fontpathfound);
                if ($fontexists && !$data->forceoverwrite) {
                    $error = get_string('upload:font:exists', 'tool_tcpdffonts', $name);
                    \core\notification::error($error);
                    redirect($this->get_url());
                }
                if ($fontexists && $data->forceoverwrite && file_exists($fontpathfound)) {
                    unlink($fontpathfound);
                }

                // Process font.
                $fontname = helper::process_font_file($fullpath, $data->fonttype,
                        $data->enc, (int)$data->flags, (int)$data->platid, (int)$data->encid,
                        (bool)$data->addcbbox);
                $success = $success && !empty($fontname);
                // Notify user.
                if ($success) {
                    \core\notification::success(get_string('font:addfont:success', 'tool_tcpdffonts', $fontname));
                } else {
                    \core\notification::error(get_string('font:addfont:fail', 'tool_tcpdffonts', $fontname));
                }
            }
            // Redirect.
            redirect($this->get_url());
        }

        echo $this->renderer->header();
        echo $this->renderer->tabs('tcpdffonts-add');
        echo $uploadform->render();
        echo $this->renderer->footer();
        // Unregister font mimetypes.
        helper::remove_mimetypes_for_upload();
    }

    /**
     * Process adding a TCPDF zipped font (containing the php file, ctg and z file)
     */
    protected function process_addzippedfont() {
        $context = context_system::instance();
        require_capability('tool/tcpdffonts:managefonts', $context);

        $uploadform = new \tool_tcpdffonts\local\uploadzip($this->get_url([
            'action' => 'addzippedfont'
        ], ['types' => ['.zip']]));

        if ($uploadform->is_cancelled()) {
            redirect($this->get_url());
        } else if ($data = $uploadform->get_data()) {
            // Store user file in temp location.
            $temppath = make_request_directory();
            $name = $uploadform->get_new_filename('userfile');
            $fullpath = rtrim($temppath, '/') . '/' . $name;
            $success = $uploadform->save_file('userfile', $fullpath, true);
            if ($success) {
                // Pre-flight check.
                $error = null;
                $canprocess = helper::check_font_file_zipped($fullpath, $error);
                if (!$canprocess) {
                    \core\notification::error($error);
                    redirect($this->get_url());
                }
                // Process font.
                $results = helper::process_font_file_zipped($fullpath);
                foreach ($results as $result) {
                    $xfontid = strtolower(basename($result->pathname, '.php'));
                    if (!empty($result->fontfileerror)) {
                        \core\notification::warning(get_string('font:addfont:fail', 'tool_tcpdffonts', $xfontid));
                    } else if (!empty($result->errors)) {
                        \core\notification::error(get_string('font:addfont:fail', 'tool_tcpdffonts', $xfontid) . '<br/>'
                                .implode('<br/>', $result->errors));
                    } else {
                        \core\notification::success(get_string('font:addfont:success', 'tool_tcpdffonts', $xfontid));

                    }
                }
            }
            // Redirect.
            redirect($this->get_url());
        }

        echo $this->renderer->header();
        echo $this->renderer->tabs('tcpdffonts-add');
        echo $uploadform->render();
        echo $this->renderer->footer();
    }

    /**
     * Download zipped version of font.
     */
    protected function process_downloadfont() {
        $fontid = required_param('id', PARAM_ALPHANUMEXT);
        $fontisfamily = optional_param('isfamily', false, PARAM_BOOL);

        $context = context_system::instance();
        require_capability('tool/tcpdffonts:managefonts', $context);

        helper::download_font($fontid, $fontisfamily);
        return;
    }

    /**
     * Generate a PDF to preview a font.
     */
    protected function process_previewfont() {
        $fontid = required_param('id', PARAM_ALPHANUMEXT);

        $context = context_system::instance();
        require_capability('tool/tcpdffonts:viewfonts', $context);

        // Gather variables.
        $fontfile = '';
        $previewtext = optional_param('t', get_string('defaultpreviewtext', 'tool_tcpdffonts'), PARAM_TEXT);
        $subset = (bool)optional_param('sub', false, PARAM_BOOL);
        $size = optional_param('s', 12, PARAM_INT);
        $style = optional_param('st', '', PARAM_ALPHA);

        // Initiate PDF.
        $pdf = new \pdf();
        $pdf->AddPage();
        $pdf->SetFont($fontid, $style, $size, $fontfile, $subset);

        // Get font information.
        $path = helper::get_font_path();
        $fontfile = rtrim($path) . '/' . $fontid . '.php';
        $finfo = [];
        helper::load_font_info($finfo, $fontfile, $fontid);

        // Write and output.
        $text = '<h1>'.get_string('previewfor', 'tool_tcpdffonts', $finfo[$fontid]['name']).'</h1>';
        $text .= $previewtext;
        $pdf->writeHTML($text, true);

        // Clear output buffer or risk PDF output errors.
        ob_end_clean();

        $pdf->Output();
    }

    /**
     * Display the inline preview page.
     */
    protected function process_preview() {
        $fontid = required_param('id', PARAM_ALPHANUMEXT);

        $this->page->requires->js_call_amd('tool_tcpdffonts/module', 'init', []);

        $context = context_system::instance();
        require_capability('tool/tcpdffonts:viewfonts', $context);
        echo $this->renderer->header();
        echo $this->renderer->tabs('tcpdffonts-preview');

        $url = $this->get_url(['action' => 'previewfont', 'id' => $fontid]);
        $previewcontext = (object)[
            'size' => 12,
            'previewtext' => get_string('defaultpreviewtext', 'tool_tcpdffonts'),
            'labelsubmit' => get_string('updatepreview', 'tool_tcpdffonts'),
            'fontid' => $fontid,
            'frameurl' => $url->out(false) . '#toolbar=0&navpanes=0&scrollbar=0"'
        ];
        echo $this->renderer->render_from_template('tool_tcpdffonts/fontpreview', $previewcontext);
        echo $this->renderer->footer();
    }

    /**
     * Delete a font
     */
    protected function process_deletefont() {
        $fontid = required_param('id', PARAM_ALPHANUMEXT);
        $fontisfamily = optional_param('isfamily', false, PARAM_BOOL);

        $context = context_system::instance();
        require_capability('tool/tcpdffonts:managefonts', $context);

        // Gather font(s).
        if ($fontisfamily) {
            // We need all fonts here.
            $fulllist = helper::fetch_font_list(true);
            $fontidlist = [];
            foreach ($fulllist as $fontinfo) {
                if ($fontinfo['family'] == $fontid) {
                    $fontidlist[] = $fontinfo['fontname'];
                }
            }
            $customdata = [
                'confirmation' => [
                    get_string('font:family:delete:header', 'tool_tcpdffonts', $fontid),
                    get_string('font:family:delete:body', 'tool_tcpdffonts', implode(', ', $fontidlist)),
                    get_string('font:family:delete:confirmation', 'tool_tcpdffonts', $fontid),
                ]
            ];
            $successmessage = get_string('font:family:delete:success', 'tool_tcpdffonts', implode(', ', $fontidlist));
            $failmessage = get_string('font:family:delete:fail', 'tool_tcpdffonts', implode(', ', $fontidlist));

        } else {
            $fontidlist = [$fontid];
            $customdata = [
                'confirmation' => [
                    get_string('font:delete:header', 'tool_tcpdffonts', $fontid),
                    get_string('font:delete:body', 'tool_tcpdffonts', $fontid),
                    get_string('font:delete:confirmation', 'tool_tcpdffonts', $fontid),
                ]
            ];
            $successmessage = get_string('font:delete:success', 'tool_tcpdffonts', $fontid);
            $failmessage = get_string('font:delete:fail', 'tool_tcpdffonts', $fontid);
        }

        $confirmationform = new \tool_tcpdffonts\local\confirmation($this->get_url([
            'action' => 'deletefont', 'id' => $fontid, 'isfamily' => $fontisfamily ? 1 : 0
        ]), $customdata);

        if ($confirmationform->is_cancelled()) {
            redirect($this->get_url());
        } else if ($confirmationform->is_confirmed()) {
            // Call services, no need for code duplicates.
            foreach ($fontidlist as $fontid) {
                $result = \tool_tcpdffonts\external::delete_font($fontid);
            }
            // Notify user.
            if ($result) {
                \core\notification::success($successmessage);
            } else {
                \core\notification::error($failmessage);
            }
            // Redirect.
            redirect($this->get_url());
        }

        echo $this->renderer->header();
        echo $this->renderer->tabs('tcpdffonts-deletefont', ['id' => $fontid]);
        echo $confirmationform->render();
        echo $this->renderer->footer();
    }

    /**
     * Reset customisation to core
     */
    protected function process_resetcorefonts() {
        $context = context_system::instance();
        require_capability('tool/tcpdffonts:managefonts', $context);

        $customdata = [
            'confirmation' => [
                get_string('font:resetcore:header', 'tool_tcpdffonts'),
                get_string('font:resetcore:body', 'tool_tcpdffonts'),
                get_string('font:resetcore:confirmation', 'tool_tcpdffonts'),
            ]
        ];
        $confirmationform = new \tool_tcpdffonts\local\confirmation($this->get_url([
            'action' => 'resetcorefonts']), $customdata);

        if ($confirmationform->is_cancelled()) {
            redirect($this->get_url());
        } else if ($confirmationform->is_confirmed()) {
            try {
                // Call services, no need for code duplicates.
                $result = \tool_tcpdffonts\external::core_reset();
                // Notify user.
                \core\notification::success(get_string('font:resetcore:success', 'tool_tcpdffonts'));
            } catch (\Exception $e) {
                \core\notification::error(get_string('font:resetcore:fail', 'tool_tcpdffonts', $e->getMessage()));
            }
            // Redirect.
            redirect($this->get_url());
        }

        echo $this->renderer->header();
        echo $this->renderer->tabs('tcpdffonts-reset');
        echo $confirmationform->render();
        echo $this->renderer->footer();
    }

    /**
     * Initialise customisation of fonts.
     */
    protected function process_initcustomfonts() {
        $context = context_system::instance();
        require_capability('tool/tcpdffonts:managefonts', $context);

        $customdata = [
            'confirmation' => [
                get_string('font:initcustom:header', 'tool_tcpdffonts'),
                get_string('font:initcustom:body', 'tool_tcpdffonts'),
                get_string('font:initcustom:confirmation', 'tool_tcpdffonts'),
            ]
        ];
        $confirmationform = new \tool_tcpdffonts\local\confirmation($this->get_url([
            'action' => 'initcustomfonts']), $customdata);

        if ($confirmationform->is_cancelled()) {
            redirect($this->get_url());
        } else if ($confirmationform->is_confirmed()) {
            try {
                // Call services, no need for code duplicates.
                \tool_tcpdffonts\external::init_custom_fonts();
                // Notify user.
                \core\notification::success(get_string('font:initcustom:success', 'tool_tcpdffonts'));
            } catch (\Exception $e) {
                \core\notification::error(get_string('font:initcustom:fail', 'tool_tcpdffonts', $e->getMessage()));
            }
            // Redirect.
            redirect($this->get_url());
        }

        echo $this->renderer->header();
        echo $this->renderer->tabs('tcpdffonts-init');
        echo $confirmationform->render();
        echo $this->renderer->footer();
    }

}
