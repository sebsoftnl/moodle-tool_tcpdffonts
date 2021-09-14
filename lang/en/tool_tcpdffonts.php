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
 * Plugin strings are defined here.
 *
 * File         external.php
 * Encoding     UTF-8
 *
 * @package     tool_tcpdffonts
 * @category    string
 *
 * @copyright   2021 Ing. R.J. van Dongen
 * @author      Ing. R.J. van Dongen <rogier@sebsoft.nl>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'TCPDF Fonts';

$string['action:font:delete'] = 'Delete font';
$string['action:font:add'] = 'Add font (TTF/OTF)';
$string['action:font:addzippedfont'] = 'Add font from ZIP';
$string['action:font:download'] = 'Download font';
$string['action:font:preview'] = 'Preview font';
$string['action:initcustomfonts'] = 'Initialise custom fonts folder';
$string['action:resetcorefonts'] = 'Reset core TCPDF fonts folder';

$string['tcpdffonts:viewfonts'] = 'View PDF font list';
$string['tcpdffonts:managefonts'] = 'Manage PDF fonts';
$string['settings'] = 'TCPDF Font settings';
$string['family'] = 'Font family';

$string['font:delete:header'] = 'Delete font';
$string['font:delete:body'] = 'You are about to delete custom font {$a}.<br/>
This is an irreversible process and cannot be undone. All font data will be removed.<br/>
Are you sure you wish to continue?';
$string['font:delete:confirmation'] = 'Yes, I understand and want to delete this font.';
$string['font:delete:success'] = 'Font successfully deleted.';
$string['font:delete:fail'] = 'Could not delete font.';

$string['font:family:delete:header'] = 'Delete font family {$a}';
$string['font:family:delete:body'] = 'You are about to delete custom font family {$a}.<br/>
This is an irreversible process and cannot be undone. All font data will be removed.<br/>
Are you sure you wish to continue?';
$string['font:family:delete:confirmation'] = 'Yes, I understand and want to delete this font family.';
$string['font:family:delete:success'] = 'Font family successfully deleted.';
$string['font:family:delete:fail'] = 'Could not delete font family.';

$string['font:resetcore:header'] = 'Reset TCPDF Core fonts';
$string['font:resetcore:body'] = 'You are about to reset the TCPDF Font customisation back to core.<br/>
This means we switch back to Moodle\'s internal TCPDF core with the standard fonts that came with the installation.<br/>
<b>Warning</b>: All your custom fonts will be removed; there is no way back once this action is completed.<br/>
Are you sure you wish to continue?';
$string['font:resetcore:confirmation'] = 'Yes, I understand and want to reset all font customisations.';
$string['font:resetcore:success'] = 'Successfully reset TCPDF core fonts.';
$string['font:resetcore:fail'] = 'Failed to reset TCPDF core fonts. Error: {$a}';

$string['font:initcustom:header'] = 'Initialise TCPDF Custom fonts';
$string['font:initcustom:body'] = 'You are about to initialise the TCPDF Font customisation.<br/>
This means we will:<ul>
<li>create the custom folder in your Moodle data folder</li>
<li>copy over all core fonts (which is a Moodle mandate, otherwise Moodle <i>will</i> switch back to the core folder)</li>
</ul>
You can safely do this and remove the customisation at any given time to switch back to Moodle\'s core implementation.<br/>
Are you sure you wish to continue?';
$string['font:initcustom:confirmation'] = 'Yes, I understand and want to initialise TCPDF font customisations.';
$string['font:initcustom:success'] = 'Successfully initialised TCPDF custom fonts.';
$string['font:initcustom:fail'] = 'Failed to initialise TCPDF custom fonts. Error: {$a}';

$string['uploadfontfile:header'] = 'Upload font file';
$string['uploadfontfile:description'] = 'You can upload new fonts from a TTF or OTF file and indicate the correct settings to generate the TCPDF font file(s)';
$string['uploadzipfile:header'] = 'Upload font zip file';
$string['uploadzipfile:description'] = 'You can upload a zipped file containing the TCPDF font file(s) consisting of a PHP file that defines the font, a .z file that is basically the gzcompressed font source and optionally the ctg.z file that contains any CID to GID mappings.<br/>
<div class="alert alert-danger"><strong>Warning:</strong> you should never ever just trust any files from an external source in Moodle especially when it contains PHP files, because it imposes a major security risk.<br/>
You should make absolutely sure the contents of the zip file you upload here is checked and validated! Although some rudimentary checks are performed to determine validity of the PHP based font definition file for TCPDF,
this does not guarantee anything at all. Due to the lack of a fully fledged validation mechanism (or literally any other definition file other than a PHP file; a JSON encoded definition for example would have been better) you are fully responsible for making sure the uploaded zip file doesn\'t contain any malicious code.
</div>';
$string['fontfile'] = 'Font file';
$string['err:font:otf-otto'] = 'TCPDF can not process/add OTF type fonts (OTTO)';
$string['font:addfont:success'] = 'Successfully added font {$a}';
$string['font:addfont:fail'] = 'Could not add font {$a}';
$string['err:font:zip'] = 'Uploaded file does not seem to be a valid ZIP Archive';
$string['missing:z-file'] = 'Missing compressed version of font source for {$a}';
$string['missing:ctg-file'] = 'Missing compressed version of CIDToGIDMap for {$a}';
$string['folderstate:default'] = 'TCPDF Fonts folder is not customised yet. You can not make use of the specialised actions provided by this tool (such as adding custom fonts) <i>before</i> you have initialised the font folder using the initialise action below.';
$string['folderstate:custom'] = 'TCPDF Fonts folder is customised and ready to be provided with custom fonts.';
$string['type'] = 'Type';

$string['addfont:explain'] = 'The settings below allow you to customize how the font will be generated.<br/>
If you have no idea what these settings are about, please keep the defaults in place (this covers most standard cases).<br/>
Only when you know exactly what the settings mean or you\'ve been instructed with which settings your true type/open type font should be converted should you adjust these settings.
';
$string['fonttype'] = 'Font type';
$string['fonttype_help'] = 'Select the type of font here or leave on auto-detect.';
$string['fontflags'] = 'Flags';
$string['fontflags_help'] = 'Unsigned 32-bit integer containing flags specifying various characteristics of the font (PDF32000:2008 - 9.8.2 Font Descriptor Flags): +1 for fixed font; +4 for symbol or +32 for non-symbol; +64 for italic. Fixed and Italic mode are generally autodetected so you have to set it to 32 = non-symbolic font (default) or 4 = symbolic font.';
$string['fontplatid'] = 'Platform ID';
$string['fontplatid_help'] = 'Platform ID for CMAP table to extract (when building a Unicode font for Windows this value should be 3, for Macintosh should be 1).';
$string['fontenc'] = 'Encoding Table';
$string['fontenc_help'] = 'Name of the encoding table to use. Leave empty for default mode. Omit this parameter for TrueType Unicode and symbolic fonts like Symbol or ZapfDingBats.';
$string['fontencid'] = 'Encoding ID';
$string['fontencid_help'] = 'Encoding ID for CMAP table to extract (when building a Unicode font for Windows this value should be 1, for Macintosh should be 0). When Platform ID is 3, legal values for Encoding ID are: 0=Symbol, 1=Unicode, 2=ShiftJIS, 3=PRC, 4=Big5, 5=Wansung, 6=Johab, 7=Reserved, 8=Reserved, 9=Reserved, 10=UCS-4.';
$string['fontaddcbbox'] = 'Add bounding box information?';
$string['fontaddcbbox_help'] = 'When selected, this will add bounding box information to the generated font files.';
$string['fontforceoverwrite'] = 'Overwrite font if it already exists (as opposed to early exit).';
$string['upload:font:exists'] = 'Font {$a} already has been processed and will not be overwritten. If you wish to overwrite the font please delete the existing font first or check the option to force overwriting the font.';

$string['ft:autodetect'] = 'Auto-detect';
$string['ft:truetypeunicode'] = 'True Type Unicode';
$string['ft:truetype'] = 'True Type';
$string['ft:type1'] = 'Type-1';
$string['ft:cid0jp'] = 'CID-0 Japanese';
$string['ft:cid0kr'] = 'CID-0 Korean';
$string['ft:cid0cs'] = 'CID-0 Chinese Simplified';
$string['ft:cid0ct'] = 'CID-0 Chinese Traditional';
$string['nonsymbolfont'] = 'Non-Symbol Font';
$string['symbolfont'] = 'Symbol Font';
$string['backtolist'] = 'Back to font overview';
$string['font:add'] = 'Add font';
$string['font:delete'] = 'Delete font';

$string['toggle:subset'] = 'Apply font subsetting';
$string['styles'] = 'Styles';
$string['bold'] = 'Apply font weight bold';
$string['italic'] = 'Apply font style italic';
$string['size'] = 'Font size';
$string['previewtext'] = 'Preview text';
$string['defaultpreviewtext'] = 'The quick brown fox jumps over the lazy hog and what a jump it was.';
$string['updatepreview'] = 'Update preview';
$string['previewfor'] = 'Font preview for {$a}';
$string['err:zipopen'] = 'An error occured trying to create the downloadable archive.<br/>
You may try to refresh this page. If the error is persistent, please contact the system administrator.';
$string['toggle:familyview:off'] = 'Group by family: OFF';
$string['toggle:familyview:on'] = 'Group by family: ON';
