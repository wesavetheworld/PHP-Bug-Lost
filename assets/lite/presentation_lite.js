var bl_shortcuts = true,
    bl_key_msg = 49,
    bl_key_sql = 50,
    bl_key_vars = 51,
    bl_key_time = 52,
    bl_key_memory = 52,
    bl_key_opacity = 79,
    bl_key_info = 73,
    bl_key_plus = 77,
    bl_key_close = 88;

////////////////////////////////////
//// SOME STANDARD FUNCTIONS
var $bl = function (id) {
    return document.getElementById(id);
};
String.prototype.trim = function () {
    return this.replace(/^\s+|\s+$/g, "");
};
String.prototype.ltrim = function () {
    return this.replace(/^\s+/, "");
};
String.prototype.rtrim = function () {
    return this.replace(/\s+$/, "");
};
Element.prototype.hasClass = function (class_name) {
    this.className = this.className.replace(/^\s+|\s+$/g, "");
    this.className = " " + this.className + " ";
    if (this.className.search(" " + class_name + " ") !== -1) {
        return true;
    }
    this.className = this.className.replace(/^\s+|\s+$/g, "");
    return false;
};
Element.prototype.removeClass = function (class_name) {
    this.className = this.className.replace(class_name, '');
    this.className = this.className.replace(/^\s+|\s+$/g, "");
};
Element.prototype.addClass = function (class_name) {
    this.className = this.className + ' ' + class_name;
    this.className = this.className.replace(/^\s+|\s+$/g, "");
};

function bl_toggle(obj, mode) {
    var el = document.getElementById(obj);
    if (mode === 'more') {
        document.getElementById("bl_debug_content").style.display = 'block';
        if (el.className === 'bl_full_panel') {
            el.className = 'bl_half_panel';
        } else {
            el.className = 'bl_full_panel';
        }
    } else {
        if (el.style.display !== 'block') {
            el.style.display = 'block';
        } else {
            el.style.display = 'none';
        }
    }
}

function randomString(length) {
    var str,
        i,
        chars = 'abcdefghiklmnopqrstuvwxyz'.split('');
    if (!length) {
        length = Math.floor(Math.random() * chars.length);
    }
    for (i = 0; i < length; i += 1) {
        str += chars[Math.floor(Math.random() * chars.length)];
    }
    return str;
}

function time(ms) {
    var t = ms / 1000;
    return Math.round(t * 100) / 100;
}

// cros-browser event listener
function bl_listen(event, elem, func, id) {
    if (id) {
        elem = $bl(elem);
    } else {
        elem = document;
    }

    if (elem) {
        if (elem.addEventListener) {
            elem.addEventListener(event, func, false);
        } else if (elem.attachEvent) { // IE DOM
            var r = elem.attachEvent("on" + event, func);
            return r;
        } else {
            throw 'No es posible añadir evento';
        }
    }
}
bl_listen('keyup', 'body', bl_keydown);

function bl_keydown(e) {

    var target;

    if (!bl_shortcuts) {
        return;
    }


    if (navigator.appName === 'Microsoft Internet Explorer') {
        e = window.event;
        target = e.srcElement.nodeName.toLowerCase();
    } else {
        target = e.target.localName;
    }

    if (target === 'html' || target === 'body') {

        if (e.keyCode === bl_key_msg) {
            bl_debug_set_panel('msg');

        } else if (e.keyCode === bl_key_sql) {
            bl_debug_set_panel('sql');

        } else if (e.keyCode === bl_key_vars) {
            bl_debug_set_panel('vars');

        } else if (e.keyCode === bl_key_time) {
            bl_debug_set_panel('time');

        } else if (e.keyCode === bl_key_memory) {
            bl_debug_set_panel('memory');

        } else if (e.keyCode === bl_key_opacity) {
            bl_opacity();

        } else if (e.keyCode === bl_key_info) {
            bl_debug_set_panel('info');

        } else if (e.keyCode === bl_key_plus) { // + maximizar
            bl_setPanelSize('plus');

        } else if (e.keyCode === bl_key_close) {
            bl_setPanelSize('close');
        }
    }
}

////////////////////////////////////
//// TOGGLE VIEW HTML ON VARS PANEL

function bl_view_html(el) {
    var el1 = document.getElementById('bl_view_html_' + el),
        el2 = document.getElementById('bl_view_' + el),
        el3 = document.getElementById('bl_view_more_' + el);
    if (el1.style.display === 'block') {
        el1.style.display = 'none';
        el2.style.display = 'block';
        el3.style.display = 'none';
    } else {
        el1.style.display = 'block';
        el2.style.display = 'none';
        el3.style.display = 'none';
    }
}
////////////////////////////////////
//// TOP/RIGHT ALERT WHEN ERRORS

function bl_show_errors() {
    bl_toggle('bl_show_errors');
}

function bl_alert_errors() {
    var bl_interval = setInterval(bl_show_errors(), 500);
    setTimeout("clearInterval(" + bl_interval + ")", 3000);
}
////////////////////////////////////
//// SET OPACITY (BUTTON opacity)

function bl_opacity() {
    var el = $bl('bl_debug');
    if (el.hasClass('bl_opacity')) {
        el.removeClass('bl_opacity');
    } else {
        el.addClass('bl_opacity');
    }
}
/**
 * Change the panel size when press button M or X
 * @size string plus|close
 */
function bl_setPanelSize(size) {
    var panel_size = 'close';

    if (size === 'plus') {
        if ($bl('bl_debug_content').className === 'bl_half_panel') {
            $bl('bl_debug_content').className = 'bl_full_panel';
            panel_size = 'full';
        } else {
            $bl('bl_debug_content').className = 'bl_half_panel';
            panel_size = 'half';
        }
    } else if (size === 'close') {
        $bl('bl_debug_content').className = 'bl_close_panel';
        panel_size = 'close';
    } else {
        $bl('bl_debug_content').className = 'bl_' + size + '_panel';
        panel_size = 'half';
    }

    if (panel_size === 'close') {
        bl_setCookie('__bl_panel_active', 'none', 1);
    }

    bl_setCookie('panel_size_bl', panel_size, 1);

}
/**
 * Change the active panel
 * @panel string msg|sql|vars|time|memory|info...
 */

function bl_debug_set_panel(panel) {
    var c1 = "bl_debug_panel",
        c2 = "bl_debug_panel_active",
        c3 = "bl_debug_btn",
        c4 = "bl_debug_activo";
    if ($bl("bl_debug_" + panel).hasClass("bl_debug_panel_active")) {
        $bl("bl_debug_" + panel).className = c1;
        $bl("bl_debug_content").className = 'bl_close_panel';
        $bl(c3 + "_" + panel).className = c3;
        bl_setPanelSize('close');
    } else {
        // show panel
        $bl("bl_debug_msg").className = c1;
        $bl("bl_debug_sql").className = c1;
        $bl("bl_debug_vars").className = c1;
        $bl("bl_debug_time").className = c1;
        $bl("bl_debug_memory").className = c1;
        $bl("bl_debug_info").className = c1;
        $bl("bl_debug_" + panel).className = c1 + " " + c2;
        // set button active
        $bl("bl_debug_btn_msg").className = c3;
        $bl(c3 + "_sql").className = c3;
        $bl(c3 + "_vars").className = c3;
        $bl(c3 + "_time").className = c3;
        $bl(c3 + "_memory").className = c3;
        $bl(c3 + "_" + panel).className = c3 + " " + c4;
        if ($bl("bl_debug_content").hasClass('bl_close_panel')) {
            $bl("bl_debug_content").className = 'bl_half_panel';
            bl_setPanelSize('half');
        }
    }
    bl_setCookie('__bl_panel_active', panel, 1);
}

/**
 * Show or hidde messesages by type
 * @type string all|error|info|warn|user
 */
function bl_debug_set_msg(type) {
    var i,
        bl_search,
        bl_search2,
        e,
        allHTMLTags = document.getElementsByTagName("tr");

    for (i = 0; i < allHTMLTags.length; i += 1) {
        if (allHTMLTags[i].className.search('bl_normal_tr') !== -1) {
            allHTMLTags[i].className = allHTMLTags[i].className.replace('bl_msg_activo', '');
            bl_search = allHTMLTags[i].className.search('bl_debug_msg_' + type);
            bl_search2 = allHTMLTags[i].className.search('bl_msg_activo');
            if (bl_search !== -1) {
                if (bl_search2 === -1) {
                    allHTMLTags[i].className = allHTMLTags[i].className + ' bl_msg_activo';
                }
            } else {
                if (type === 'all') {
                    if (bl_search2 === -1) {
                        allHTMLTags[i].className = allHTMLTags[i].className + ' bl_msg_activo';
                    }
                }
            }
        }
    }
    allHTMLTags = document.getElementsByTagName("a");
    for (i = 0; i < allHTMLTags.length; i += 1) {
        if (allHTMLTags[i].className.search('bl_debug_msg_btn') !== -1) {
            allHTMLTags[i].className = 'bl_debug_msg_btn';
        }
    }
    // añadir la clase al elemento actual
    e = document.getElementById('bl_debug_msg_btn_' + type);
    e.addClass('bl_debug_msg_btn_activo');
}

/**
 * Change the active vars panel
 * @size string A vars panel: vars|special|get|post... etc.
 */
function bl_debug_set_var(panel) {
    // obtener todos los links del menu y eliminar la clase activo
    var i,
        e,
        allHTMLTags = document.getElementsByTagName("div");

    for (i = 0; i < allHTMLTags.length; i += 1) {
        if (allHTMLTags[i].className.search('bl_debug_var_panel') !== -1) {
            allHTMLTags[i].className = 'bl_debug_var_panel';
        }
    }

    allHTMLTags = document.getElementsByTagName("a");
    for (i = 0; i < allHTMLTags.length; i += 1) {
        if (allHTMLTags[i].className.search('bl_debug_var_btn') !== -1) {
            allHTMLTags[i].className = 'bl_debug_var_btn';
        }
    }

    // añadir la clase al elemento actual
    e = document.getElementById('bl_debug_var_btn_' + panel);
    e.addClass('bl_debug_var_btn_activo');
    // activar el panel de elemento
    e = document.getElementById('bl_debug_var_' + panel);
    e.addClass('bl_debug_var_panel_activo');
}


/**
 * Expand methods and properties on classes panel
 */
function bl_expand(count) {
    // obtener todos los links del menu y eliminar la clase activo
    var i,
        allHTMLTags = document.getElementsByTagName("span");
    for (i = 0; i < allHTMLTags.length; i += 1) {
        if (allHTMLTags[i].className.search('bl_class_' + count) !== -1) {
            if (allHTMLTags[i].style.display !== 'block') {
                allHTMLTags[i].style.display = 'block';
                $bl('bl_method_comments_expand_' + count).style.display = 'none';
                $bl('bl_method_comments_' + count).style.display = 'block';
            } else {
                allHTMLTags[i].style.display = 'inline';
                $bl('bl_method_comments_expand_' + count).style.display = 'block';
                $bl('bl_method_comments_' + count).style.display = 'none';
            }

        }
    }
}

////////////////////////////////////
//// ACTIONS FOR INPUT FILTER ON VARS PANEL

// filter function by vonloesch.de
function filter(phrase, id) {

    var words = $bl(phrase).value.toLowerCase().split(" "),
        table = document.getElementById(id),
        ele,
        r,
        i,
        displayStyle;

    for (r = 1; r < table.rows.length; r += 1) {
        ele = table.rows[r].innerHTML.replace(/<[^>]+>/g, "");
        displayStyle = "none";
        for (i = 0; i < words.length; i += 1) {
            if (ele.toLowerCase().indexOf(words[i]) >= 0) {
                displayStyle = "";
            } else {
                displayStyle = "none";
                break;
            }
        }
        table.rows[r].style.display = displayStyle;
    }
}

function filterUser() {
    filter('bl_filter_user', 'bl_table_user');
}

function filterSpecial() {
    filter('bl_filter_special', 'bl_table_special');
}

function filterFunctions() {
    filter('bl_filter_functions', 'bl_table_functions');
}

function filterUclasses() {
    filter('bl_filter_uclasses', 'bl_table_uclasses');
}

function filterIclasses() {
    filter('bl_filter_iclasses', 'bl_table_iclasses');
}

function filterConstants() {
    filter('bl_filter_constants', 'bl_table_constants');
}

function filterGet() {
    filter('bl_filter_get', 'bl_table_get');
}

function filterPost() {
    filter('bl_filter_post', 'bl_table_post');
}

function filterSession() {
    filter('bl_filter_session', 'bl_table_session');
}

function filterCookie() {
    filter('bl_filter_cookie', 'bl_table_cookie');
}

function filterFiles() {
    filter('bl_filter_files', 'bl_table_files');
}

function filterServer() {
    filter('bl_filter_server', 'bl_table_server');
}

bl_listen('keyup', 'bl_filter_user', filterUser, true);
bl_listen('keyup', 'bl_filter_special', filterSpecial, true);
bl_listen('keyup', 'bl_filter_functions', filterFunctions, true);
bl_listen('keyup', 'bl_filter_uclasses', filterUclasses, true);
bl_listen('keyup', 'bl_filter_iclasses', filterIclasses, true);
bl_listen('keyup', 'bl_filter_constants', filterConstants, true);
bl_listen('keyup', 'bl_filter_get', filterGet, true);
bl_listen('keyup', 'bl_filter_post', filterPost, true);
bl_listen('keyup', 'bl_filter_session', filterSession, true);
bl_listen('keyup', 'bl_filter_cookie', filterCookie, true);
bl_listen('keyup', 'bl_filter_files', filterFiles, true);
bl_listen('keyup', 'bl_filter_server', filterServer, true);

////////////////////////////////////
//// AJAX FOR DELETE SESSIONS AND COOKIES

function bl_ajax() {
    var xmlhttp = false;
    try {
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
        try {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (E) {
            xmlhttp = false;
        }
    }

    if (!xmlhttp && typeof XMLHttpRequest !== 'undefined') {
        xmlhttp = new XMLHttpRequest();
    }
    return xmlhttp;
}
function bl_del_var(var_name, url, type, key, tr_id, url_var_name) {

    var ajax;
    url = url + '?' + url_var_name + '=1&var=' + var_name + '&type=' + type + '&bl_key=' + key;

    $bl('bl_loading').style.display = 'block';

    ajax = bl_ajax();
    ajax.open("GET", url, true);
    ajax.onreadystatechange = function () {
        if (ajax.readyState === 4) {

            $bl('bl_loading').style.display = 'none';

            if (ajax.responseText === 'ok') {
                // delete table row
                var tr = $bl(tr_id);
                tr.innerHTML = '<td colspan="5">var $' + type + '["' + var_name + '"]  deleted</td>';

            } else if (ajax.responseText === 'error-key') {
                alert('There\'re a problem with your secret key');

            } else if (ajax.responseText === 'error-cookie') {
                alert('Sorry, I can\t delete this cookie.');

            } else {
                alert('Error. No vars deleted!');
            }
        }
    };
    ajax.send(null);
}


////////////////////////////////////
//// HIGHLIGHT A TABLE ROW. FIRE WHEN MOUSEOVER (inline code on <tr>)

function bl_highlight_row(highlight, el) {
    if (highlight === true) {
        el.addClass('bl_highlight_row');
    } else {
        el.removeClass('bl_highlight_row');
    }
}
////////////////////////////////////
//// TOGGLE FOR VIEW ARRAY OPTION ON VARS PANEL

function view_array(id) {
    var div = document.getElementById(id),
        a = document.getElementById(id.replace('div_', 'a_'));
    if (div.style.display === 'block') {
        div.style.display = 'none';
        a.style.display = 'block';
    } else {
        div.style.display = 'block';
        a.style.display = 'none';
    }
}
////////////////////////////////////
//// LIKE PHP HTMLENTITES. USED FOR SHOW HTML ON VARS PANEL

function htmlentities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

////////////////////////////////////
//// SET COOKIE | SAVE CONSOLE STATE

function bl_setCookie(c_name, value, exdays) {
    var c_value,
        exdate = new Date();
    exdate.setDate(exdate.getDate() + exdays);
    c_value = escape(value) + ((exdays === null) ? "" : "; expires=" + exdate.toUTCString());
    document.cookie = c_name + "=" + c_value + '; path=/';
}

