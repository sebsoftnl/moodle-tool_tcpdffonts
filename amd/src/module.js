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

import * as Config from 'core/config';

/**
 * Preview handler.
 *
 * @param {Event} e
 */
const previewHandler = (e) => {
    e.preventDefault();
    const form = document.querySelector('#tool_tcpdffonts-preview-form');
    const fontid = form.dataset.fontid;
    let subset = document.getElementById('id_subset').checked === true;
    let bold = document.getElementById('id_bold').checked === true;
    let italic = document.getElementById('id_italic').checked === true;
    let size = document.getElementById('id_size').value;
    let txt = document.getElementById('id_txt').value;

    let style = '';
    if (bold) {
        style += 'b';
    }
    if (italic) {
        style += 'i';
    }

    let src = Config.wwwroot + '/admin/tool/tcpdffonts/preview.php?action=previewfont&id=' + fontid;
    src += '&sub=' + (subset ? 1 : 0);
    src += '&s=' + size;
    src += '&st=' + style;
    src += '&t=' + encodeURIComponent(txt);
    src += '#toolbar=0&navpanes=0&scrollbar=0';

    document.querySelector('#framepreview').src = src;
};

/**
 * Initialise module
 *
 * @returns {void}
 */
export const init = () => {
    document.querySelector('#tool_tcpdffonts-preview-form').addEventListener('submit', previewHandler);
    document.querySelectorAll('#tool_tcpdffonts-preview-form input[type="checkbox"]').forEach((el) => {
        el.addEventListener('change', previewHandler);
    });
    document.querySelector('#tool_tcpdffonts-preview-form input[type="number"]').addEventListener('change', previewHandler);
    // Change is a limited case for textareas; we could have used blur here too.
    document.querySelector('#tool_tcpdffonts-preview-form textarea').addEventListener('change', previewHandler);
};
