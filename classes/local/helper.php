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
 * The tool_tcpdffonts helper class.
 *
 * File         helper.php
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

use FilesystemIterator;
use RecursiveDirectoryIterator;
use moodle_exception;

/**
 * The tool_tcpdffonts helper class.
 *
 * @package     tool_tcpdffonts
 *
 * @copyright   2021 Ing. R.J. van Dongen
 * @author      Ing. R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Assert or throw
     *
     * @param bool $bool
     * @param string $errorcode
     * @param string $module
     * @param mixed $a
     * @throws moodle_exception
     */
    public static function assert(bool $bool, string $errorcode, string $module = 'moodle', $a = null) {
        if (!$bool) {
            throw new moodle_exception($errorcode, $module, '', $a);
        }
    }

    /**
     * Fetch TCPDF Font path
     *
     * @return string
     */
    public static function get_font_path() {
        return K_PATH_FONTS;
    }

    /**
     * Is TCPDF Fonts yet customized?
     *
     * @return bool
     */
    public static function is_customized() {
        global $CFG;
        $defaultpath = $CFG->dirroot . '/lib/tcpdf/fonts/';
        return (K_PATH_FONTS !== $defaultpath);
    }

    /**
     * Initialize custom fonts.
     * This initializes the standard fonts (CONTRIB) folder to your moodle data folder
     * and copies over all tcpdf/moodle core fonts.
     *
     * @return bool
     */
    public static function initialise_custom_fonts() {
        global $CFG;
        if (static::is_customized()) {
            return true;
        }

        // Require pdflib because it defines some things.
        require_once($CFG->libdir . '/pdflib.php');
        if (!defined('PDF_CUSTOM_FONT_PATH')) {
            throw new \moodle_exception('err:defined:pdf_custom_font_path', 'tool_tcpdffonts');
        }

        // Make sure path exists and is writable.
        if (!is_dir(PDF_CUSTOM_FONT_PATH)) {
            make_writable_directory(PDF_CUSTOM_FONT_PATH);
        }

        // Default path.
        $defaultpath = $CFG->dirroot . '/lib/tcpdf/fonts/';
        // Now copy over files. For NOW, this is only 1 (!!!!!) folder deep.
        $flags = \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS;
        $dh = new \DirectoryIterator($defaultpath);
        foreach ($dh as $fi) {
            if ($fi->getExtension() !== 'php') {
                continue;
            }
            $file = null;
            $ctg = null;
            // Include def file.
            include($fi->getPathname());
            $pathbase = $fi->getPath();
            // Copy over .z files.
            copy($fi->getPathname(), rtrim(PDF_CUSTOM_FONT_PATH, '/') . '/' . $fi->getFilename());
            if (!empty($file)) {
                copy($pathbase . '/' . $file, rtrim(PDF_CUSTOM_FONT_PATH, '/') . '/' . basename($file));
            }
            if (!empty($ctg)) {
                copy($pathbase . '/' . $ctg, rtrim(PDF_CUSTOM_FONT_PATH, '/') . '/' . basename($ctg));
            }
        }

        return true;
    }

    /**
     * Reset custom fonts folder back to core.
     *
     * This performs a reset to core, aka remove ALL custom fonts and the indicated contrib folder
     *
     * @return bool
     */
    public static function reset_custom_fonts() {
        global $CFG;
        if (!static::is_customized()) {
            return true;
        }

        // Require pdflib because it defines some things.
        require_once($CFG->libdir . '/pdflib.php');
        if (!defined('PDF_CUSTOM_FONT_PATH')) {
            throw new \moodle_exception('err:defined:pdf_custom_font_path', 'tool_tcpdffonts');
        }

        // Make sure path exists and is writable.
        if (!is_dir(PDF_CUSTOM_FONT_PATH)) {
            return (object) ['result' => true];
        }
        // And remove.
        $result = remove_dir(PDF_CUSTOM_FONT_PATH);

        return $result;
    }

    /**
     * Is this a core font?
     *
     * @param string $fontbasename ttrailing part of the font path
     *
     * @return bool
     */
    public static function is_core_font($fontbasename) {
        global $CFG;
        $defaultpath = $CFG->dirroot . '/lib/tcpdf/fonts/';
        return file_exists($defaultpath . str_replace('.php', '', $fontbasename) . '.php');
    }

    /**
     * Is this a font that's part of the core restriction?
     * Core restriction means if the font is not present in the custom folder...
     * ... Moodle core will automatically fall back to the internal TCPDF folder.
     *
     * @param string $font font name (you _should_ provide the family name here)
     *
     * @return bool
     */
    public static function is_core_restricted_font($font) {
        // The standard files are a mandate from Moodle core!
        // See <libdir>/pdflib.php - function tcpdf_init_k_font_path.
        $somestandardfiles = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats', 'freeserif', 'freesans');
        if (in_array($font, $somestandardfiles)) {
            // Core REQUIRES these files when it determines it's own internal magic on the custom folder.
            // Actions on these fonts is therefore forbidden (aka: no delete).
            return true;
        }
        return false;
    }

    /**
     * Assert CUSTOMIZED TCPDF Font mode.
     */
    public static function assert_customized() {
        static::assert(static::is_customized(), 'err:tcpdf-is-core', 'tool_tcpdffonts');
    }

    /**
     * Load all fonts.
     * Please do note this loads info for every seperate font and will NOT be grouped by family.
     *
     * @return array
     */
    public static function fetch_font_list() {
        $fonts = [];
        $flags = FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO
                | FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS;
        $dh = new RecursiveDirectoryIterator(static::get_font_path(), $flags);
        foreach ($dh as $fi) {
            // The PHP files represent the font info.
            if ($fi->getExtension() !== 'php') {
                continue;
            }

            $fontname = $fi->getBasename('.' . $fi->getExtension());
            static::load_font_info($fonts, $fi->getPathname(), $fontname);
        }
        // We wish to pre-sort by the font name before this all gets super messy...
        // ... and we ALL should know array_multisort is BLAZINGLY fast compared...
        // ... to any other sorting algo in PHP.
        array_multisort(array_column($fonts, 'name'), SORT_NATURAL, $fonts);

        return $fonts;
    }

    /**
     * Load extra font info from the definition file.
     *
     * @param array $info
     * @param string $fontfile
     * @param string $fontname
     */
    public static function load_font_info(array &$info, $fontfile, $fontname) {
        // Assume the font file is valid and exists and is readable.
        include($fontfile);
        $fontinfo = [
            'basepath' => static::get_font_path(),
            'fontname' => $fontname,
            'name' => $name,
            'type' => $type,
            'style' => '',
            'family' => '',
            'file' => (isset($file) ? $file : ''),
            'ctg' => (isset($ctg) ? $ctg : ''),
        ];

        $family = strtoupper($fontname);
        $style = '';
        // Move embedded styles on $style.
        if (substr($family, -1) == 'I') {
            $style .= 'I';
            $family = substr($family, 0, -1);
        }
        if (substr($family, -1) == 'B') {
            $style .= 'B';
            $family = substr($family, 0, -1);
        }

        $fontinfo['fontfile'] = basename($fontfile);
        $fontinfo['style'] = $style;
        $fontinfo['family'] = strtolower($family); // Normalize family name.
        $fontinfo['iscore'] = static::is_core_font($fontinfo['fontfile']);
        $fontinfo['iscorerestriction'] = static::is_core_restricted_font($fontinfo['family']);

        $info[$fontname] = $fontinfo;
    }

    /**
     * Delete a font
     *
     * This action will remove all files related to the font.
     *
     * @param string $fontidentifier
     * @return void
     */
    public static function delete_font($fontidentifier) {
        global $CFG;
        require_once($CFG->libdir . '/pdflib.php');
        // Assert that we're in TCPDF mode.
        static::assert_customized();
        // Assert custom path.
        static::assert(defined('PDF_CUSTOM_FONT_PATH'), 'err:defined:pdf_custom_font_path', 'tool_tcpdffonts');
        static::assert(is_dir(PDF_CUSTOM_FONT_PATH), 'err:pdf_custom_font_path', 'tool_tcpdffonts');
        // Generate / assert existing.
        $fontfile = rtrim(PDF_CUSTOM_FONT_PATH, '/') . '/' . $fontidentifier . '.php';
        static::assert(is_file($fontfile), 'err:fontfile', 'tool_tcpdffonts');

        include($fontfile);
        if (!empty($file) && file_exists(rtrim(PDF_CUSTOM_FONT_PATH, '/') . '/' . $file)) {
            unlink(rtrim(PDF_CUSTOM_FONT_PATH, '/') . '/' . $file);
        }
        if (!empty($ctg) && file_exists(rtrim(PDF_CUSTOM_FONT_PATH, '/') . '/' . $ctg)) {
            unlink(rtrim(PDF_CUSTOM_FONT_PATH, '/') . '/' . $ctg);
        }
        unlink($fontfile);

        \tool_tcpdffonts\event\font_deleted::trigger_from_font($fontidentifier);
    }

    /**
     * Are actions allowed for the given font?
     *
     * @param string $fontname
     * @param bool $iscorefont will be filled automatically!
     * @return boolean
     */
    public static function actions_allowed($fontname, &$iscorefont) {
        if (!static::is_customized()) {
            // We cannot perform font actions, this is the CORE default location.
            return false;
        }
        // Extra check which could have been done initially, but to be safe...
        $fontnameshort = strtolower($fontname);
        if (substr($fontnameshort, -1) == 'i') {
            $fontnameshort = substr($fontnameshort, 0, -1);
        }
        if (substr($fontnameshort, -1) == 'b') {
            $fontnameshort = substr($fontnameshort, 0, -1);
        }
        if (static::is_core_restricted_font($fontnameshort)) {
            // Core REQUIRES these files when it determines it's own internal magic on the custom folder.
            // Actions on these fonts is therefore forbidden (aka: no delete).
            $iscorefont = true;
            return false;
        }

        $iscorefont = false;
        // Assume everything else as true.
        return true;
    }

    /**
     * Add font mime types to core filetypes.
     *
     * This is here as a utility method so we can register the mime type for our upload needs.
     *
     * @return void
     */
    public static function add_mimetypes_for_upload() {
        $types = \core_filetypes::get_types();
        if (array_key_exists('ttf', $types)) {
            return;
        }
        \core_filetypes::add_type('ttf', 'font/ttf', 'document', ['fonts']);
        \core_filetypes::add_type('otf', 'font/otf', 'document', ['fonts']);
    }

    /**
     * Remove font mime types from core filetypes
     *
     * This is here as a utility method so we can unregister the mime type after our upload needs.
     *
     * @return void
     */
    public static function remove_mimetypes_for_upload() {
        $types = \core_filetypes::get_types();
        if (array_key_exists('ttf', $types)) {
            \core_filetypes::delete_type('ttf');
        }
        if (array_key_exists('otf', $types)) {
            \core_filetypes::delete_type('otf');
        }
    }

    /**
     * Extremely rudimentary check: TCPDF does not support OTF-OTTO (CFF) fonts.
     *
     * @param string $fontfile full path to font
     * @param string $error will be automatically set if there were errors
     * @return array|bool
     */
    public static function check_font_file($fontfile, &$error) {
        global $CFG;
        require_once($CFG->libdir . '/tcpdf/include/tcpdf_static.php');

        $font = file_get_contents($fontfile);
        if (substr($font, 0, 4) == 'OTTO') {
            $error = get_string('err:font:otf-otto', 'tool_tcpdffonts');
            return false;
        }
        return true;
    }

    /**
     * Process a font file.
     *
     * @param string $fontfile full path to font
     * @param string $fonttype Font type. Leave empty for autodetect mode.
     *              Valid values are: TrueTypeUnicode, TrueType, Type1, CID0JP = CID-0 Japanese, CID0KR = CID-0 Korean,
     *              CID0CS = CID-0 Chinese Simplified, CID0CT = CID-0 Chinese Traditional.
     * @param string $enc Name of the encoding table to use. Leave empty for default mode.
     *              Omit this parameter for TrueType Unicode and symbolic fonts like Symbol or ZapfDingBats.
     * @param int $flags Unsigned 32-bit integer containing flags specifying various characteristics of the font
     *              (PDF32000:2008 - 9.8.2 Font Descriptor Flags):
     *              +1 for fixed font; +4 for symbol or +32 for non-symbol; +64 for italic.
     *              Fixed and Italic mode are generally autodetected so you have to set it
     *              to 32 = non-symbolic font (default) or 4 = symbolic font.
     * @param int $platid Platform ID for CMAP table to extract
     *              (when building a Unicode font for Windows this value should be 3, for Macintosh should be 1).
     * @param int $encid Encoding ID for CMAP table to extract
     *              (when building a Unicode font for Windows this value should be 1, for Macintosh should be 0).
     *              When Platform ID is 3, legal values for Encoding ID are:
     *              0=Symbol, 1=Unicode, 2=ShiftJIS, 3=PRC, 4=Big5, 5=Wansung,
     *              6=Johab, 7=Reserved, 8=Reserved, 9=Reserved, 10=UCS-4.
     * @param bool $addcbbox If true includes the character bounding box information on the php font file.
     *
     * @return string|null null on error, added font name on success
     */
    public static function process_font_file($fontfile, $fonttype = '', $enc = '',
            $flags = 32, $platid = 3, $encid = 1, $addcbbox = false) {
        global $CFG;
        require_once($CFG->libdir . '/tcpdf/include/tcpdf_fonts.php');

        // Process font.
        $outpath = ''; // Represents K_FONT_PATH.
        $tcpdffont = \TCPDF_FONTS::addTTFfont($fontfile, $fonttype, $enc, $flags, $outpath, $platid, $encid, $addcbbox);
        if ($tcpdffont === false) {
            return null;
        }

        // Trigger event.
        \tool_tcpdffonts\event\font_created::trigger_from_font($tcpdffont);

        // And return the font name.
        return $tcpdffont;
    }

    /**
     * Extremely rudimentary check: Validate zip file.
     *
     * @param string $fontfile full path to font
     * @param string $error will be automatically set if there were errors
     * @return array|bool
     */
    public static function check_font_file_zipped($fontfile, &$error) {
        $font = file_get_contents($fontfile);
        if (substr($font, 0, 4) !== "\x50\x4b\x03\x04") {
            $error = get_string('err:font:zip', 'tool_tcpdffonts');
            return false;
        }
        return true;
    }

    /**
     * Process a font file.
     *
     * @param string $fontfile full path to font
     * @return string|null null on error, added font name on success
     */
    public static function process_font_file_zipped($fontfile) {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');
        // Assuming we have an actual ZIP file that passed rudimentary checking.

        $temppath = make_request_directory();

        $fp = get_file_packer('application/zip');
        $files = $fp->list_files($fontfile);

        if (empty($files)) {
            return false;
        }

        // Now load php files.
        $possiblefontfiles = [];
        foreach ($files as $file) {
            if ($file->is_directory) {
                continue;
            }
            // Parse extension; we shall only process .php.
            $extension = array_reverse(explode('.', $file->pathname))[0];
            if ($extension === 'php') {
                // We'll try to process this.
                $possiblefontfiles[] = $file;
            }
        }

        // Extract files.
        $extracted = $fp->extract_to_pathname($fontfile, $temppath);
        if (empty($extracted)) {
            return false;
        }

        // Loop through possible fonts.
        // For safety measures, first READ the PHP file and determine whether it's conforming TCPDF "specs".
        foreach ($possiblefontfiles as &$fileinfo) {
            $pathname = rtrim($temppath, '/') . '/' . $fileinfo->pathname;
            $dirname = dirname($pathname);
            if (!static::is_php_fontfile($pathname)) {
                $fileinfo->fontfileerror = true;
                continue;
            }
            // Ok see if we should include some .z files.
            $file = null;
            $ctg = null;
            // Include file: this _might_ have the potential for malicious code inclusion.
            include($pathname);
            if (!empty($file)) {
                $fileinfo->zfile = $file;
                $fileinfo->zfilepath = $dirname . '/' . $file;
            }
            if (!empty($ctg)) {
                $fileinfo->ctgfile = $ctg;
                $fileinfo->ctgfilepath = $dirname . '/' . $ctg;
            }
        }
        unset($fileinfo);

        // And now finally... we'll " extract" the correct files.
        foreach ($possiblefontfiles as &$fileinfo) {
            if (!empty($fileinfo->fontfileerror)) {
                continue;
            }
            // Check existence of all paths.
            $cancopy = true;
            $errors = [];
            if (!empty($fileinfo->zfilepath) && !file_exists($fileinfo->zfilepath)) {
                $cancopy = false;
                $errors[] = get_string('missing:z-file', 'tool_tcpdffonts', basename($fileinfo->filepath, 'php'));
            }
            if (!empty($fileinfo->ctgfilepath) && !file_exists($fileinfo->ctgfilepath)) {
                $errors[] = get_string('missing:ctg-file', 'tool_tcpdffonts', basename($fileinfo->filepath, 'php'));
                $cancopy = false;
            }
            if (!$cancopy) {
                $fileinfo->errors = $errors;
                continue;
            }
            // Copy over.
            $pathname = rtrim($temppath, '/') . '/' . $fileinfo->pathname;
            copy($pathname, static::get_font_path() . '/' . basename($fileinfo->pathname));
            if (!empty($fileinfo->zfilepath)) {
                copy($fileinfo->zfilepath, static::get_font_path() . '/' . basename($fileinfo->zfilepath));
            }
            if (!empty($fileinfo->ctgfilepath)) {
                copy($fileinfo->ctgfilepath, static::get_font_path() . '/' . basename($fileinfo->ctgfilepath));
            }
            // Trigger event that font was added.
            \tool_tcpdffonts\event\font_created::trigger_from_font(basename($fileinfo->pathname, '.php'));
        }
        unset($fileinfo);

        return $possiblefontfiles;
    }

    /**
     * Loosy check if we're handling a TCPDF font file.
     *
     * We'll do this by the most of rudimentary checks: variables $name and $type must exist.
     * We will NOT "include" the file but try and read the contents instead (prevent malicious code inclusion).
     *
     * @param string $pathname
     * @return bool
     */
    protected static function is_php_fontfile($pathname) {
        // Read bytes.
        $xctx = file_get_contents($pathname, false, null, 0, 500);
        // Check 2 variables to exist: type and name.
        $typeset = strpos($xctx, '$type') !== false;
        $nameset = strpos($xctx, '$name') !== false;
        return $typeset && $nameset;
    }

    /**
     * Get font type options (used when converting TTF/OTF uploads)
     *
     * @return array
     */
    public static function fonttype_options() {
        return [
            '' => get_string('ft:autodetect', 'tool_tcpdffonts'),
            'TrueType' => get_string('ft:truetype', 'tool_tcpdffonts'),
            'TrueTypeUnicode' => get_string('ft:truetypeunicode', 'tool_tcpdffonts'),
            'Type1' => get_string('ft:type1', 'tool_tcpdffonts'),
            'CID0JP' => get_string('ft:cid0jp', 'tool_tcpdffonts'),
            'CID0KR' => get_string('ft:cid0kr', 'tool_tcpdffonts'),
            'CID0CS' => get_string('ft:cid0cs', 'tool_tcpdffonts'),
            'CID0CT' => get_string('ft:cid0ct', 'tool_tcpdffonts'),
        ];
    }

    /**
     * Check if a font already exists.
     *
     * Mind, this check is kinda rudimentary and ripped from the TCPDF library.
     * This is merely a utility method we use to pre-detect existence and be...
     * ... able to inform the user about font existence.
     *
     * @param string $fontfile
     * @param string $fontpathfound If the font is found, this variable will be filled with the path.
     * @return bool
     */
    public static function has_font($fontfile, &$fontpathfound) {
        global $CFG;
        require_once($CFG->libdir . '/tcpdf/include/tcpdf_static.php');
        // Shamelessly ripped and modified from TCPDF.
        $pathparts = pathinfo($fontfile);
        if (!isset($pathparts['filename'])) {
            $pathparts['filename'] = substr($pathparts['basename'], 0, - (strlen($pathparts['extension']) + 1));
        }
        $fontname = strtolower($pathparts['filename']);
        $fontname = preg_replace('/[^a-z0-9_]/', '', $fontname);
        $search = array('bold', 'oblique', 'italic', 'regular');
        $replace = array('b', 'i', 'i', '');
        $fontname = str_replace($search, $replace, $fontname);
        if (empty($fontname)) {
            // Set generic name.
            $fontname = 'tcpdffont';
        }
        // Set output path.
        $outpath = self::get_font_path();
        // Check if this font already exists.
        if (@\TCPDF_STATIC::file_exists($outpath . $fontname . '.php')) {
            $fontpathfound = $outpath . $fontname . '.php';
            return true;
        }
        return false;
    }

    /**
     * Open zip file to add to.
     *
     * @param string $basename base name to give the zip.
     * @return array first item is the archive, second is the filename
     * @throws moodle_exception if archive cannot be opened.
     */
    private static function open_zip_for_export($basename) {
        global $CFG;
        // Open zip archive.
        $archive = new \ZipArchive();
        $ziprelpath = 'fontexport-' . $basename . '.zip';
        $zipname = $basepath = $CFG->dataroot . '/' . $ziprelpath;
        $rs = $archive->open($zipname, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        if ($rs !== true) {
            throw new moodle_exception('err:zipopen', 'tool_tcpdffonts');
        }
        return [$archive, $zipname];
    }

    /**
     * Download a font or font family.
     *
     * @param string $fontid either fontid or the font family name
     * @param bool $isfamily if true, this will generate an archive with all fonts in the family.
     *          When true, $fontid MUST represent the familyname.
     */
    public static function download_font($fontid, $isfamily = false) {
        // Fetch all fonts.
        $finfo = static::fetch_font_list();

        // Create filter callback.
        $cbkey = $isfamily ? 'family' : 'fontname';
        $callable = function($font) use ($cbkey, $fontid) {
            return $font[$cbkey] == $fontid;
        };

        // Create archive, loop using filter and add files.
        list($archive, $zipname) = static::open_zip_for_export($fontid);
        $iterator = new \CallbackFilterIterator(new \ArrayIterator($finfo), $callable);
        foreach ($iterator as $font) {
            // Add main file.
            $archive->addFromString($font['fontfile'], file_get_contents($font['basepath'] . $font['fontfile']));
            // Add .z file.
            if (!empty($font['file'])) {
                $archive->addFromString($font['file'], file_get_contents($font['basepath'] . $font['file']));
            }
            // Add .ctg.z file.
            if (!empty($font['ctg'])) {
                $archive->addFromString($font['ctg'], file_get_contents($font['basepath'] . $font['ctg']));
            }
        }
        $archive->close();

        // And send.
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . basename($zipname) . "\"");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . filesize($zipname));
        readfile($zipname);
        @unlink($zipname);
    }

}
