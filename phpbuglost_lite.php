<?php
/**
 * PHP Bug lost 0.5a Lite
 * One-file script for debug and monitor scripts.
 * See docs and support forum at http://www.phpbuglost.com
 *
 * PHP Version 5
 *
 * @version 0.5a
 * @author  Jordi Enguídanos <jordifreek@gmail.com>
 * @license MIT Licence
 * @link    http://phpbuglost.com
 */

error_reporting(E_ALL); // show errors...
set_error_handler("bl_error_handler"); // ... and hidden with error_handler

#################################
## - SECURITY OPTIONS

/**
 * Change with any alphanumerical string.
 * It's required if you use delete vars.
 * If anyone knows this key, may delete your session and cookie vars
 * @see _BL_DELETE_VARS
 * @type string
 */
define('_BL_SECRET_KEY', '_pbls_');


/**
 * PBL block some functions if you set the website in production mode:
 * delete vars will be off is set this true.
 * @type string
 */
define('_BL_PRODUCTION', false);

/**
 * Set the name for URL var used in ajax request when delete a
 * session/cookie var
 * @type string
 */
define('_BL_VAR_DEL', 'bl_del');


/**
 * show console only to this IP. keep empty for don't use.
 * Comma separated for multiple ip
 * @type string
 */
define('_BL_ALLOW_IP', '');


// ============================
// MONITOR OPTIONS

/** @type bool true|false for activate|deactivate monitor options */
define('_BL_MONITOR_ON', false);

/** @type string Email where to send monitor info */
define('_BL_ADMIN_MAIL', 'name@domain.com');

/** @type string Description. Appears on email title */
define('_BL_SHORT_SITE_DESCRIPTION', 'My Site');

/** @type bool Send email on sql fails */
define('_BL_MONITOR_SQL', false);

/** @type bool Send email if match "monitor times" rules (see below) */
define('_BL_MONITOR_TIMES', false);

/** @type bool Send email if match "monitor memory" rules */
define('_BL_MONITOR_MEMORY', false);

/** @type int Max allowed time for total load in seconds */
define('_BL_MAX_LOAD_TIME', 0);

/** @type int Max allowed time for any query in seconds */
define('_BL_MAX_SQL_TIME', 0);

/** @type int Max allowed time for any time mark in seconds */
define('_BL_MAX_ANY_TIME', 0);

/** @type int Max allowed total memory usage in bytes */
define('_BL_MAX_TOTAL_MEMORY', 0);

/**
 * Include log messages when send emails to admin.
 * Comma separated list: error,warn,info,user
 * Keep empty to include nothing
 * @type string
 */
define('_BL_MONITOR_MAIL_LOG', 'error,warn,info,user');


// ============================
// MESSAGES/LOG PANEL

/**
 * Type of messages to show in the console.
 * Set to "all" (default) to include all types or comma separated
 * for individual types.
 * Allowed: error|warn|info|user|all
 * @type string
 */
define('_BL_MESSAGES_TYPES', 'all');

/** @type bool show and alert when there're errors in messages */
define('_BL_ALERT_ERRORS', true);

/** @type bool show backtrace errors */
define('_BL_BACKTRACE', true);


// ============================
// VARS PANEL

/**
 * true for allow delete cookies and session vars.
 * Keep false on production sites (recommended)
 * Remember change the secret key otherwise you will get an error.
 * @see _BL_SECRET_KEY
 * @type bool
 */
define('_BL_DELETE_VARS', true);

/** @type bool true for use only sort vars like $_POST, $_GET, $_SESSION */
define('_BL_USE_SHORT_VARS', true);

/** @type bool true for use HTML Viewer (for vars on vars panel). */
define('_BL_HTML_VIEWER', false);

/**
 * true for get size of objects.
 * Some objects, like PDO instance, can't be serialized
 * and php return error. See Memory section in docs.
 * @type bool
 */
define('_BL_SERIALIZE_OBJECTS', false);

/** @type bool show methods and properties of internal php classes */
define('_BL_SHOW_INTERNAL_CLASSES', false);

/** @type bool show methods and properties of user php classes */
define('_BL_SHOW_USER_CLASSES', true);


// ============================
// SQL PANEL

/**
 * What type of DB uses. One of mysql|sqlite|pdo
 * @type string
 */
define('_BL_DB_DRIVER', 'mysql');

/** use explain for show more info on mysql querys */
define('_BL_EXPLAIN_SQL', false);

/** @type object Sqlite3 object */
$bl_sqlite = null;


// ============================
// TIMES PANEL

/** @type bool Add time marks to any event (log messages, querys...) */
define('_BL_CREATE_TIMES', true);


// ============================
// OTHER OPTIONS

/**
 * Save the console state when reloading page.
 * note: Use cookies
 * @type bool
 */
define('_BL_SAVE_STATE', false);

/**
 * Enable/disable shortcuts. Only when using internal js.
 * On external js find bl_shortcuts var and set true/false.
 * This is injected to javascript, set "true" or "false" with quotes,
 * like a string
 * @type string
 */
define('_BL_KEYBOARD_SHORTCUTS', 'true');

/** @type bool Show or hide the shortcuts numbers on menu buttons */
define('_BL_SHOW_KEYBOARD_SHORTCUTS', true);


// End Configure
// ============================

define('_BL_VERSION', 'lite');


// private. name of this file script. May be you don't need to touch this
define('_BL_FILENAME', basename(__file__));
define('_BL_PATH', str_replace(
    '//',
    '/',
    str_replace(
        $_SERVER['DOCUMENT_ROOT'],
        '/',
        str_replace(
            '\\',
            '/',
            __file__)
        )
    )
);

define('_BL_ROOT', $_SERVER['DOCUMENT_ROOT']);


/**
 * Calculate the difference between two times
 *
 * @param mixed $time_start The first time mark
 * @param mixed $microtime  The second mark. If none use actual microtime()
 *
 * @return int|double The Time
 */
function bl_get_time($time_start = null, $microtime = null)
{
    if (!$microtime) {
        $microtime = microtime();
    }

    $time = explode(' ', $microtime);
    $time = $time[1] + $time[0];

    if ($time_start) {
        $time = $time - $time_start;
    }

    return $time;
}

// check user defined time mark
if (defined('BL_TIME_START')) {
    define('_BL_TIME_START', bl_get_time(null, BL_TIME_START));
} else {
    define('_BL_TIME_START', bl_get_time());
}

// default shortcuts tags, html markup for top menu
if (_BL_KEYBOARD_SHORTCUTS == true) {
    define('_BL_KEY_LOGS', '<sup>1</sup>');
    define('_BL_KEY_SQL', '<sup>2</sup>');
    define('_BL_KEY_VARS', '<sup>3</sup>');
    define('_BL_KEY_TIME', '<sup>4</sup>');
    define('_BL_KEY_MEMORY', '<sup>5</sup>');
    define('_BL_KEY_OPACITY', ' (o)');
    define('_BL_KEY_INFO', ' (i)');
} else {
    define('_BL_KEY_LOGS', '');
    define('_BL_KEY_SQL', '');
    define('_BL_KEY_VARS', '');
    define('_BL_KEY_TIME', '');
    define('_BL_KEY_MEMORY', '');
    define('_BL_KEY_OPACITY', '');
    define('_BL_KEY_INFO', '');
}

/**
 * "Container" for global vars
 */
class _bl
{
    public static $count_msg    = 0;
    public static $count_querys = 0;
    public static $count_vars   = 0;
    public static $vars         = array();
    public static $errors       = false; // used for highlight error alert
    public static $msgs         = array(); // log messages
    public static $msgs_time    = array(); // log tiems
    public static $msg_sql      = array(); // log querys
    public static $time_start   = _BL_TIME_START; // default time start
    public static $panel_state  = 'close'; // default panel state
    public static $panel_active = array(
        "msg"     => "bl_debug_panel_active", // default panel active
        "sql"     => "",
        "vars"    => "",
        "time"    => "",
        "memory"  => ""
    );

    public static $max_var_size = array("var" => "", "size" => 0);
    public static $max_file_size = array("var" => "", "size" => 0);
}

// save/get panel state
if (_BL_SAVE_STATE == true) {
    // remember panel size
    if (isset($_COOKIE['panel_size_bl'])) {
        _bl::$panel_state = $_COOKIE['panel_size_bl'];
    }
    // remember what panel is active
    if (isset($_COOKIE['__bl_panel_active'])) {
        if (isset(_bl::$panel_active[$_COOKIE['__bl_panel_active']])) {
            _bl::$panel_active['msg'] = '';
            _bl::$panel_active[$_COOKIE['__bl_panel_active']] = 'bl_debug_panel_active';
        }
    }
}

/**
 * Send "monitor" emails to admin
 *
 * @param string $msg   Email body
 * @param string $title Email Title
 * @param array  $data  Message details.
 *
 * @return void
 */
function bl_send_mail($msg, $title, $data)
{

    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= 'To: <' . _BL_ADMIN_MAIL . '>' . "\r\n";
    //$headers .= 'From: Put any thing here <and_any@email.com>' . "\r\n";

    $th_style = 'font-weight:bold; background-color:#eee; border-bottom:1px dashed #ccc; padding:5px;';

    $data_th = $data_td = '';
    foreach ($data as $k => $v) {
        $data_th .= '<th style="'.$th_style.'">'.ucfirst($k).'</th>';
        $data_td .= '<td style="padding:5px;">' . $v . '</td>';
    }

    $error_logs = '<p>No log messages</p>';
    if (_BL_MONITOR_MAIL_LOG != '') {
        $error_logs = bl_get_msg( _BL_MONITOR_MAIL_LOG, true );
        $error_logs = '<h3>Log Messages</h3>'.$error_logs;
    }

    $msg = $msg.'
    <table>
        <thead>
            <tr>'.$data_th.'</tr>
        </thead>
        <tbody>
            <tr>'.$data_td.'</tr>
        </tbody>
    </table>'.$error_logs;

    mail(_BL_ADMIN_MAIL, _BL_SHORT_SITE_DESCRIPTION.' '.$title, $msg, $headers);
}

/**
 * Format Time, get tiem in seconds (0.0202)
 *
 * @param string $time Time to format
 * @param int    $dec  Number of decimals
 *
 * @return string Formated time
 */
function bl_format_time($time, $dec = 4)
{
    return round($time, $dec).'s';
}

/**
 * Add a message to the list (array _bl::$msgs)
 *
 * @param mixed $msg   Text of the message
 * @param string $type Tipe of message: info, error, warn or user (default)
 *
 * @return void
 */
function bl_msg($msg, $file, $line, $type = 'user')
{
    // don't fill memory if we don't need it later
    if (strpos(_BL_MESSAGES_TYPES, $type) == false and _BL_MESSAGES_TYPES != 'all') {
        return false;
    }

    $format_msg = $msg;

    if (_BL_CREATE_TIMES) {
        bl_time('Log missage '.substr($msg, 0, 30)).'...';
    }

    $count = count(_bl::$msgs);
    _bl::$msgs[$count]['msg'] = $format_msg;
    _bl::$msgs[$count]['line'] = $line;
    _bl::$msgs[$count]['file'] = $file;
    _bl::$msgs[$count]['time'] = bl_format_time(bl_get_time(_bl::$time_start));
    _bl::$msgs[$count]['type'] = $type;
}

/**
 * List of messages (_bl::$msgs) in a HTML table.
 *
 * @return string HTML Table
 */
function bl_get_msg($type = _BL_MESSAGES_TYPES, $styles = false)
{
    if ($type == 'all') {
        $types = array('error', 'warn', 'user', 'info');
    } else {
        $types = explode(',', trim($type, ','));
        $types = array_map('trim', $types);
    }

    // check styles (styles for html in emails (bl_send_mail()))
    $th_style = '';
    $td_style = '';
    $td_colors = array(
        'error' => '',
        'warn' => '',
        'info' => '',
        'user' => ''
    );
    if ($styles == true) {
        $th_style = 'font-weight:bold; background-color:#eee; border-bottom:1px dashed #ccc; padding:5px;';
        $td_style = 'padding:3px; border-bottom:1px dashed #ccc;';
        $td_colors = array(
            'error' => 'background-color:#f33;',
            'warn' => 'background-color:#f90;',
            'info' => 'background-color:#36f;',
            'user' => 'background-color:#333;'
        );
    }

    $result = '
    <table class="bl_msg_table">
        <thead>
            <tr>
                <th style="width:20px;"></th>
                <th style="'.$th_style.'">Message</th>
                <th style="'.$th_style.'">File</th>
                <th style="'.$th_style.'">Line</th>
                <th style="'.$th_style.'">Time</th>
            </tr>
        </thead>
        <tbody>';

    //die('<pre>'.print_r(_bl::$msgs, true).'</pre>');
    $count = 0;
    foreach (_bl::$msgs as $k => $v) {

        if (in_array($v['type'], $types)) {
            if ($v['type'] == 'error') {
                _bl::$errors = true;
            }

            $result .= '
                <tr id="bl_msg_num_' . $count . '" class="bl_normal_tr bl_debug_msg_' .
                $v['type'] .
                ' bl_msg_activo" onmouseover="bl_highlight_row(true, this)" onmouseout="bl_highlight_row(false, this)">
                    <td style="'.$td_colors[$v['type']].'" class="bl_msg_' . $v['type'] . '"></td>
                    <td class="bl_td">' . $v['msg'] . '</td>
                    <td class="bl_td">' . $v['file'].':'.$v['line'] . '</td>
                    <td class="bl_td">' . $v['line'] . '</td>
                    <td class="bl_td">' . $v['time'] . '</td>
                </tr>';
            $count++;
            _bl::$count_msg = $count;
        }
    }

    $result .= '
        </tbody>
    </table>';

    return $result;
}

/**
 * Save new error to the message list
 *
 * @access public
 *
 * @param string $msg Message to send
 *
 * @return void
 */
function bl_error($msg, $file = '', $line = '')
{
    $debug = debug_backtrace();
    $file = ($file == '') ? $debug[0]['file'] : $file;
    $line = ($line == '') ? $debug[0]['line'] : $line;
    bl_msg('<strong>'.$msg.'</strong>', $file, $line, 'error');
}

/**
 * Save new warning to the message list
 *
 * @access public
 *
 * @param string $msg Message to send
 *
 * @return void
 */
function bl_warn($msg)
{
    $debug = debug_backtrace();
    bl_msg($msg, $debug[0]['file'], $debug[0]['line'], 'warn');
}

/**
 * Save new info to the message list
 *
 * @access public
 *
 * @param string $msg Message to send
 *
 * @return void
 */
function bl_info($msg)
{
    $debug = debug_backtrace();
    bl_msg($msg, $debug[0]['file'], $debug[0]['line'], 'info');
}

/**
 * Log new variable.
 * Get var name thanks to:
 * http://www.php.net/manual/en/language.variables.php#49997
 *
 * @access public
 *
 * @param mixed $var The var to log
 *
 * @return void
 */
function bl_var(&$var, $var_name = null)
{
    if ($var_name == null) {
        $vals = $GLOBALS;
        $old = $var;
        $var = $new = 'UNIQUE' . rand() . 'VARIABLE';
        $vname = false;

        foreach ($vals as $key => $val) {
            if ($val === $new) {
                $vname = $key;
            }
        }

        $var = $old;
    } else {
        $vname = $var_name;
    }

    $count = count(_bl::$vars);

    // using bl_var with an object class always get
    // the same state of the object.
    // Here we register the object with different states
    if (is_object($var)) {
        _bl::$vars['object_' . $vname . '|' . $count] = get_object_vars($var);
    } else {
        _bl::$vars[$vname . '|' . $count] = $var;
    }
}

/**
 * Save new mark of time
 *
 * @access public
 *
 * @param string $label Name for the mark
 * @param string $start Start reference.
 *
 * @return void
 */
function bl_time($label = null, $start = null)
{
    if ($start == null) {
        $start = _bl::$time_start;
    }

    $count = count(_bl::$msgs_time);
    if ($label == null) {
        $label = 'Time mark ' . $count;
    }
    _bl::$msgs_time[$count]['label'] = strip_tags($label);
    _bl::$msgs_time[$count]['time'] = bl_format_time(bl_get_time($start));

}

/**
 * Log a simple text. You can use html code.
 *
 * @access public
 *
 * @param string $msg Message to log
 *
 * @return void
 */
function bl_log($msg)
{
    $debug = debug_backtrace();
    bl_msg($msg, $debug[0]['file'], $debug[0]['line'], 'user');
}

/**
 * Tipical function for error_handler
 *
 * @return void
 */
function bl_error_handler($errno, $errstr, $errfile, $errline)
{
    $trace = debug_backtrace();
    $msg   = '<p><strong>'.$errstr.'</strong></p>';

    $trace = array_reverse($trace);

    if (_BL_BACKTRACE == true) {
        if (is_array($trace) and count($trace)) {
            $msg .= '
            <table class="bl_backtrace">
                <thead>
                    <tr>
                        <th>File</th>
                        <th>Line</th>
                        <th>Function</th>
                    </tr>
                </thead>
                <tbody>';

            foreach ($trace as $item) {

                if (isset($item['file'])) {

                    $function = ($item['function'] != 'bl_error_handler')
                        ? $item['function'].'()'
                        : '-';

                    if (basename($item['file']) != _BL_FILENAME) {

                        $msg .= '
                        <tr>
                            <td>'.$item['file'].'</td>
                            <td>'.$item['line'].'</td>
                            <td>'.$function.'</td>
                        </tr>';

                        $errfile = $item['file'];
                        $errline = $item['line'];
                    }
                }
            }
            $msg .= '</tbody></table>';
        }
    }

    bl_msg($msg, $errfile, $errline, 'error');
}

/**
 * An alias function for bl_mysql, bl_sqlite and bl_pdo
 *
 * @param string   $query SQL query to execute
 * @param resource $con   An sql connection (optional).
 *
 * @return void
 */
function bl_query($query, $con = null)
{
    if (_BL_DB_DRIVER == 'mysql') {
        return bl_mysql($query, $con, 1);

    } elseif (_BL_DB_DRIVER == 'sqlite') {
        if ($con != null) {
            return bl_sqlite($query, $con, 1);
        } else {
            bl_error('
                PHPBugLost: Require a SQlite object in bl_query()
                second parameter. See docs for use sqlite with PBL',
                $_SERVER['SCRIPT_FILENAME'],
                '0'
            );
        }

    } elseif (_BL_DB_DRIVER == 'pdo') {
        if ($con != null) {
            return bl_pdo($query, $con, 1);
        } else {
            throw new Exception('PHPBugLost: Require a PDO object. See docs
                for use PDO with PBL');
        }

    } else {
        throw new Exception('PHPBugLost: Error when executing bl_query, check
            _BL_DB_DRIVER and set one of bl_mysql, bl_sqlite or bl_pdo');
    }
}


/**
 * Execute a mysql query and send the data to the log
 *
 * @access public
 *
 * @param string $query The query to run
 * @param resource $con Optinally, connection to mysql
 *
 * @return resource MySQL resource
 */
function bl_mysql($query, $con = null, $debugnum = 0)
{
    if (_BL_CREATE_TIMES) {
        bl_time('Start Query '.substr($query, 0, 30)).'...';
    }

    $debug = debug_backtrace();

    $t_start = $error = '';

    // make query and get time
    // WTF! DRY!!
    if ($con) {
        $t_start = bl_get_time();
        $sql = mysql_query($query, $con);
        $t_stop = bl_get_time($t_start);
    } else {
        $t_start = bl_get_time();
        $sql = mysql_query($query);
        $t_stop = bl_get_time($t_start);
    }
    $time = $t_stop;

    if (mysql_error()) {
        $error = mysql_error();
    }

    // check for errros
    $insert_id = $results = '0';
    $explain_info = '';
    $query_cleared = trim(strtolower($query));
    $insert_id = $results = '0';
    if (substr($query_cleared, 0, 6) == 'insert') {
        // if is insert get the last id
        $insert_id = mysql_insert_id();
    } else {
        if (substr($query_cleared, 0, 6) == 'select') {

            // if is select get num rows

            // explain query?
            $explain_info = '';
            if (_BL_EXPLAIN_SQL and !$error and _BL_PRODUCTION == false) {
                $sql_explain = mysql_query("EXPLAIN " . $query);
                $explain = mysql_fetch_assoc($sql_explain);

                $explain_info = '
                <p class="bl_explain">
                    <strong>EXPLAIN</strong> -&gt;Table: <em>' . $explain['table'] .
                    '</em> <span class="bl_msg_separator">|</span>
                    Type: <em>' . $explain['type'] .
                    '</em> <span class="bl_msg_separator">|</span>
                    Possible Keys: <em>' . $explain['possible_keys'] .
                    '</em> <span class="bl_msg_separator">|</span>
                    Key: <em>' . $explain['key'] .
                    '</em> <span class="bl_msg_separator">|</span>
                    Key len: <em>' . $explain['key_len'] .
                    '</em> <span class="bl_msg_separator">|</span>
                    Ref: <em>' . $explain['ref'] .
                    '</em> <span class="bl_msg_separator">|</span>
                    Extra: <em>' . $explain['Extra'] . '</em>
                </p>';

                $results = $explain['rows'];
            } else {
                $results = mysql_num_rows($sql);
            }

        }
    }

    // add to the querys array
    _bl::$count_querys++;
    $count = _bl::$count_querys; // :)
    _bl::$msg_sql[$count]['query'] = $query;
    _bl::$msg_sql[$count]['time'] = $time;
    _bl::$msg_sql[$count]['insert'] = $insert_id;
    _bl::$msg_sql[$count]['result'] = $results;
    _bl::$msg_sql[$count]['explain'] = $explain_info;
    _bl::$msg_sql[$count]['error'] = (!empty($error)) ? '<span class="error">' . $error .'</span>' : '';
    _bl::$msg_sql[$count]['file'] = $debug[$debugnum]['file'];
    _bl::$msg_sql[$count]['line'] = $debug[$debugnum]['line'];

    if ($error and _BL_MONITOR_SQL and _BL_MONITOR_ON) {
        bl_send_mail(
            '<p>New MySQL Error from PHPBugLost</p>',
            'New MySQL Error from PHPBugLost',
            _bl::$msg_sql[$count]
        );
    }

    return $sql; // return resource
}


/**
 * Execute a sqlite query and send the data to the log
 *
 * @access public
 *
 * @param string $query The query to run
 * @param resource connection to sqlite db/file
 *
 * @return resource MySQL resource
 */
function bl_sqlite($query, $con, $debugnum = 0)
{
    $debug = array_reverse(debug_backtrace());
    $error = '';

    if (_BL_CREATE_TIMES) {
        bl_time('Start Query '.substr($query, 0, 30)).'...';
    }

    // make query and get time
    $t_start = bl_get_time();
    $sql = $con->query($query);
    $time = bl_get_time($t_start);


    // check for errros
    if ($sql) {

        $query_cleared = trim(strtolower($query));
        $insert_id = '0';
        if (substr($query_cleared, 0, 6) == 'insert') {
            // if is insert get the last id
            $insert_id = $con->lastInsertRowID();
        }

    } else {

        if ($con->lastErrorMsg()) {
            $error = $con->lastErrorMsg();
        } else {
            // we need this??... may be...
            $error = 'Can\'t complete the query. Unknown Error';
        }

    }

    // add to the querys array
    _bl::$count_querys++;
    $count = _bl::$count_querys; // :)
    _bl::$msg_sql[$count]['query']   = $query;
    _bl::$msg_sql[$count]['time']    = $time;
    _bl::$msg_sql[$count]['insert']  = $insert_id;
    _bl::$msg_sql[$count]['result']  = '-';
    _bl::$msg_sql[$count]['explain'] = '';
    _bl::$msg_sql[$count]['error']   = (!empty($error)) ? '<span class="error">' . $error .'</span>' : '-';
    _bl::$msg_sql[$count]['file']    = $debug[$debugnum]['file'];
    _bl::$msg_sql[$count]['line']    = $debug[$debugnum]['line'];

    return $sql; // return resource
}


/**
 * Execute a pdo query and send the data to the log
 *
 * @access public
 *
 * @param string $query The query to run
 * @param resource $con PDO connection
 *
 * @return resource MySQL resource
 */
function bl_pdo($query, $con, $debugnum = 0)
{
    if (_BL_CREATE_TIMES) {
        bl_time('Start Query '.substr($query, 0, 30)).'...';
    }

    $debug = debug_backtrace();

    $t_start = $error = '';

    // make query and get time
    $t_start = bl_get_time();
    $sql     = $con->query($query);
    $time    = bl_get_time($t_start);


    $insert_id = $num_result = '0';
    // check for errros
    if ($sql) {

        $query_cleared = trim(strtolower($query));
        if (substr($query_cleared, 0, 6) == 'insert') {
            // if is insert get the last id
            $insert_id = $con->lastInsertId();
        } elseif (substr($query_cleared, 0, 6) == 'select') {
            $num_result = $sql->rowCount();
        }

    } else {

        if ($con->errorInfo()) {
            $errorArray = $con->errorInfo();
            $error      = $errorArray[2];
            bl_error($error, $debug[$debugnum]['file'], $debug[$debugnum]['line']);
        } else {
            $error = 'Can\'t complete the query. Unknown Error'; // we need this??... may be...
        }
    }

    // add to the querys array
    _bl::$count_querys++;
    $count = _bl::$count_querys; // :)
    _bl::$msg_sql[$count]['query']   = $query;
    _bl::$msg_sql[$count]['time']    = $time;
    _bl::$msg_sql[$count]['insert']  = $insert_id;
    _bl::$msg_sql[$count]['result']  = $num_result;
    _bl::$msg_sql[$count]['explain'] = '';
    _bl::$msg_sql[$count]['error']   = (!empty($error)) ? '<span class="error">' . $error .'</span>' : '';
    _bl::$msg_sql[$count]['file']    = $debug[0]['file'];
    _bl::$msg_sql[$count]['line']    = $debug[0]['line'];

    return $sql; // return resource
}

/**
 * Convert size in bytes
 * 
 * @param mixed $size Size to messure
 *
 * @return mixed Size converted
 */
function bl_convert($size)
{
    if ($size > 0 and is_numeric($size)) {
        $unit = array(
            'b',
            'kb',
            'mb',
            'gb',
            'tb',
            'pb');
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) .
            ' <span>' . $unit[$i] . '</span>';
    }
    return '0';
}

/**
 * Because gettype() not is the way?
 *
 * @param mixed $var The var
 * @return string Type of var
 *
 * @return string The type of the var
 */
function bl_get_type($var)
{
    if (is_array($var)) {
        return 'Array';
    } elseif (is_object($var)) {
        return 'Object';
    } elseif (is_resource($var)) {
        return 'Resource';
    } elseif (is_bool($var)) {
        return 'Bool';
    } elseif (is_float($var)) {
        return 'Float';
    } elseif (is_double($var)) {
        return 'Double';
    } elseif (is_int($var)) {
        return 'Int';
    } elseif (is_numeric($var)) {
        return 'Numeric';
    } elseif (is_real($var)) {
        return 'Real';
    }

    return 'String';
}

/**
 * Add some colors to the arrays on the vars panel
 * TODO: may be the regex need some fix.
 *
 * @param array $array An array
 * @return string Highlighted print_r array
 *
 * @return string A highlighted array
 */
function bl_high_array($array)
{
    $array = preg_replace('/\[(.*?)\]/', '<strong>[$1]</strong>', print_r($array, true));
    $array = preg_replace('/\[/', '<span style="color:#f33">[</span>', $array);
    $array = preg_replace('/\]/', '<span style="color:#f33">]</span>', $array);
    $array = preg_replace('/=>/', '<span style="color:#005500">=></span>', $array);
    return $array;
}

/**
 * Highlighter class - highlights SQL with preg and some compromises
 *
 * @author dzver <dzver@abv.bg>
 * @copyright GNU v 3.0
 *
 * @param string $sql SQL query
 *
 * @return string Highlighted sql query
 */
function bl_high_sql($sql)
{
    $colors = array(
        'chars' => 'grey',
        'keywords' => 'blue',
        'joins' => 'gray',
        'functions' => 'violet',
        'constants' => 'red');
    $words = array(
        'keywords' => array('SELECT','UPDATE', 'INSERT', 'DELETE', 'REPLACE',
            'INTO', 'CREATE', 'ALTER', 'TABLE', 'DROP', 'TRUNCATE', 'FROM',
            'ADD', 'CHANGE', 'COLUMN', 'KEY', 'WHERE', 'ON', 'CASE', 'WHEN',
            'THEN', 'END', 'ELSE', 'AS', 'USING', 'USE', 'INDEX', 'CONSTRAINT',
            'REFERENCES', 'DUPLICATE', 'LIMIT', 'OFFSET', 'SET', 'SHOW',
            'STATUS', 'BETWEEN', 'AND', 'IS', 'NOT', 'OR', 'XOR', 'INTERVAL',
            'TOP', 'GROUP BY', 'ORDER BY', 'DESC', 'ASC', 'COLLATE', 'NAMES',
            'UTF8', 'DISTINCT', 'DATABASE', 'CALC_FOUND_ROWS', 'SQL_NO_CACHE',
            'MATCH', 'AGAINST', 'LIKE', 'REGEXP', 'RLIKE', 'PRIMARY',
            'AUTO_INCREMENT', 'DEFAULT', 'IDENTITY', 'VALUES', 'PROCEDURE',
            'FUNCTION', 'TRAN', 'TRANSACTION', 'COMMIT', 'ROLLBACK',
            'SAVEPOINT', 'TRIGGER', 'CASCADE', 'DECLARE', 'CURSOR','FOR',
            'DEALLOCATE'),
        'joins' => array('JOIN', 'INNER', 'OUTER', 'FULL', 'NATURAL',
            'LEFT', 'RIGHT'),
        'chars' => '/([\\.,\\(\\)<>:=`]+)/i',
        'functions' => array('MIN', 'MAX', 'SUM', 'COUNT', 'AVG', 'CAST',
            'COALESCE', 'CHAR_LENGTH', 'LENGTH', 'SUBSTRING', 'DAY',
            'MONTH', 'YEAR', 'DATE_FORMAT', 'CRC32', 'CURDATE', 'SYSDATE',
            'NOW', 'GETDATE', 'FROM_UNIXTIME', 'FROM_DAYS', 'TO_DAYS', 'HOUR',
            'IFNULL', 'ISNULL', 'NVL', 'NVL2', 'INET_ATON', 'INET_NTOA',
            'INSTR', 'FOUND_ROWS', 'LAST_INSERT_ID', 'LCASE', 'LOWER',
            'UCASE', 'UPPER', 'LPAD', 'RPAD', 'RTRIM', 'LTRIM', 'MD5',
            'MINUTE', 'ROUND', 'SECOND', 'SHA1', 'STDDEV', 'STR_TO_DATE',
            'WEEK'),
        'constants' => '/(\'[^\']*\'|[0-9]+)/i');

    $sql = str_replace('\\\'', '\\&#039;', $sql);
    foreach ($colors as $key => $color) {
        if (in_array($key, array('constants', 'chars'))) {
            $regexp = $words[$key];
        } else {
            $regexp = '/\\b(' . join("|", $words[$key]) . ')\\b/i';
        }
        $sql = preg_replace(
            $regexp,
            '<span style="color:' . $color . "\">$1</span>",
            $sql
        );
    }

    return $sql;
}

/**
 * Used for generate the HTML table of sql querys
 *
 * Developer Info: Using {@link mysql_q()} function we create an array whith
 * info for each query. This array is a global array and is
 * called _bl::$msg_sql. On bl_debug() function we call to this function
 * ({@link bl_get_querys()}) using this global array.
 * Then we iterate _bl::$msg_sql for get all the sql messages created
 * {@link mysql_q()}. Other functions {@link _bl_get_times},
 * {@link _bl_get_msg}, {@link _bl_get_memory}... are similar to this
 *
 * @param array $bl_msg_sql The global array for SQL querys info
 *
 * @return string An HTML table whith each query info
 */
function bl_get_querys($bl_msg_sql)
{
    $result = '';

    // be sure $bl_msg_sql not is empty
    if (is_array($bl_msg_sql) and count($bl_msg_sql)) {

        // HTML table header
        $result = '
        <table class="bl_table_querys">
            <thead>
                <tr>
                    <th>Query</th>
                    <th>Type</th>
                    <th>Time</th>
                    <th>Insert ID</th>
                    <th>Num Results</th>
                    <th>Error</th>
                    <th>File</th>
                    <th>Line</th>
                </tr>
            </thead>
            <tbody>';

        // add rows to the table
        foreach ($bl_msg_sql as $k => $v) {

            $sql = trim($v['query']);
            $sql_type = substr(strtolower($sql), 0, 6);
            $type = '&nbsp;';

            if ($sql_type == 'select') {
                $type = '<span style="color:purple">SELECT</span>';

            } elseif ($sql_type == 'insert') {
                $type = '<span style="color:orange">INSERT</span>';

            } elseif ($sql_type == 'update') {
                $type = '<span style="color:olive">UPDATE</span>';

            }

            $results = (empty($v['result'])) ? '&nbsp;' : $v['result'];

            $explain = ($v['explain']) ? $v['explain'] : '';
            $result .= '
                <tr onmouseover="bl_highlight_row(true, this)" onmouseout="bl_highlight_row(false, this)">
                    <td>' . bl_high_sql($v['query']) . $explain . '</td>
                    <td>'.$type.'</td>
                    <td>' . bl_format_time($v['time']) . '</td>
                    <td>' . $v['insert'] . '</td>
                    <td>' . $v['result'] . '</td>
                    <td>' . $v['error'] . '</td>
                    <td>' . $v['file'] . '</td>
                    <td>' . $v['line'] . '</td>
                </tr>';
        }

        // Close HTML table
        $result .= '
            </tbody>
        </table>';
    }

    return $result;
}

/**
 * Generate HTML table of vars (error, info, warn and log)
 *
 * @param mixed $array      Array of vars (generally with get_defined_vars())
 * @param mixed $array_name Name or type the array: user, special, post,
 *                          get, session... etc.
 *
 * @return void
 */
function bl_get_vars($array, $array_name, $id_prefix = '', $caption = '')
{
    $result = '';
    $count = $results = 0;

    if (count($array)) {

        $extra_cols = '';
        if ((_BL_DELETE_VARS == true) and ($array_name == '_SESSION' or $array_name == '_COOKIE')) {
            $extra_cols = '
                    <th></th>';
        }

        if ($caption) {
            $caption = '<caption>'.$caption.'</caption>';
        }

        $thead = '';
        if ($id_prefix != 'watch') {
            $thead = '
            <thead>
                <tr>
                    <th>Var</th>
                    <th>Value</th>
                    <th>Type</th>
                    <th>Size</th>
                    ' . $extra_cols . '
                </tr>
            </thead>';
        }

        $result = '
        <table id="bl_table' . strtolower($array_name) . '">
            '.$caption.'
            '.$thead.'
            <tbody>';

        $count = 0;
        foreach ($array as $k => $v) {

            // $k the name of the var
            // $v the value

            if (substr($k, 0, 7) == 'object_') {
                $k = str_replace('object_', '', $k);
            }

            // if is special var, the name has a simbol |
            // this simbol is for differenciate more than once the same var
            // we need the second part, after |
            $explode = explode('|', $k);
            $k = $explode[0];

            $error = false;
            $count++;

            if (substr($k, 0, 3) == 'bl_' or substr($k, 0, 4) == '_BL_') {
                $error = true;
            } else {

                // Some versions of php use this type of vars (long name) and too the short name.
                // Check if delete the long name to prevent duplicated vars.
                if (_BL_USE_SHORT_VARS) {
                    $delete_vars = array(
                        '_ENV',
                        'HTTP_ENV_VARS',
                        'HTTP_POST_VARS',
                        'HTTP_GET_VARS',
                        'HTTP_COOKIE_VARS',
                        'HTTP_SERVER_VARS',
                        'HTTP_POST_FILES',
                        '_REQUEST',
                        'HTTP_SESSION_VARS');
                    if (in_array($k, $delete_vars)) {
                        $error = true;
                    }
                }
            }

            if (!$error) {

                if ($array_name == '_USER') {
                    _bl::$count_vars++;
                }

                $var_type = bl_get_type($v);
                $toggle = $html_button = '';
                if ($var_type == 'Array') {

                    $var_type_name = ($var_type == 'Array') ? 'Array' : 'Object';

                    $valor = '
                    <a href="javascript:void(0);" onclick="view_array(\''.$id_prefix.'div_' . $array_name .
                        '_' . $count . '\')" id="'.$id_prefix.'a_' . $array_name . '_' . $count .
                        '" style="color:#008000">' . $var_type_name . '(...</a>
                    <div style="display:none;" id="'.$id_prefix.'div_' . $array_name . '_' . $count . '">
                        <p class="bl_close_array"><a href="javascript:void(0);" onclick="view_array(\''.$id_prefix.'div_' .
                        $array_name . '_' . $count . '\')">Close</a></p>
                        <pre>' . bl_high_array($v) . '</pre>
                    </div>';

                } elseif ($var_type == 'Object') {

                    // methods
                    $valor = '
                    <a href="javascript:void(0);" onclick="view_array(\''.$id_prefix.'div_' . $array_name .
                        '_' . $count . '1\')" id="'.$id_prefix.'a_' . $array_name . '_' . $count .
                        '1" style="color:#008000">Methods</a>
                    <div style="display:none; margin-bottom:10px;" id="'.$id_prefix.'div_' . $array_name .
                        '_' . $count . '1">
                        <p class="bl_close_array"><a href="javascript:void(0);" onclick="view_array(\''.$id_prefix.'div_' .
                        $array_name . '_' . $count . '1\')">Close Methods</a></p>
                        <pre>' . bl_high_array(get_class_methods($v)) . '</pre>
                    </div>';

                    $valor .= '
                    <a href="javascript:void(0);" onclick="view_array(\''.$id_prefix.'div_' . $array_name .
                        '_' . $count . '3\')" id="'.$id_prefix.'a_' . $array_name . '_' . $count .
                        '3" style="color:#008000">Object(...</a>
                    <div style="display:none; margin-bottom:10px;" id="'.$id_prefix.'div_' . $array_name .
                        '_' . $count . '3">
                        <p class="bl_close_array"><a href="javascript:void(0);" onclick="view_array(\''.$id_prefix.'div_' .
                        $array_name . '_' . $count . '3\')">Close Object</a></p>
                        <pre>' . bl_high_array($v) . '</pre>
                    </div>';

                } elseif ($var_type == 'Bool') {
                    if ($v) {
                        $valor = '<span style="color:#0000ff">True</span>';
                    } else {
                        $valor = '<span style="color:#0000ff">False</span>';
                    }

                } elseif ($var_type == 'Int' or is_numeric($v)) {
                    $valor = '<span style="color:#f33">' . $v . '</span>';

                } elseif ($var_type == 'Float') {
                    $valor = '<span style="color:#f33">' . $v . '</span>';

                } elseif ($var_type == 'Resource') {
                    $valor = '['.get_resource_type($v).']';
                } else {

                    $valor = $v;

                    if ($var_type == 'String') {
                        $valor = htmlspecialchars($v);
                        $valor_html = '';

                        if (_BL_HTML_VIEWER == true) {
                            $valor_html = $v;
                            if ($valor_html != strip_tags($valor_html)) {
                                $html_button = '<a href="javascript:bl_view_html(' . _bl::$count_vars .
                                    ')">[html]</a>';
                                $valor_html = '
                                <div id="bl_view_html_' . _bl::$count_vars .
                                    '" style="display:none;" class="bl_view_html">
                                    <div class="bl_view_html_title">HTML Viewer</div>
                                    <div class="bl_view_html_content">
                                    ' . $valor_html . '
                                    </div>
                                </div>';

                                $var_type = 'String|HTML';

                            } else {
                                $valor_html = '';
                            }
                        }

                        if (strlen($valor) > 200) {
                            $valor = '<div id="bl_view_' . _bl::$count_vars . '">' . substr($valor, 0,
                                200) . ' [...]</div>
                            <div id="bl_view_more_' . _bl::$count_vars . '" style="display:none;">
                                ' . $valor . '
                                <p><a href="javascript:bl_toggle(\'bl_view_more_' . _bl::$count_vars .
                                '\');bl_toggle(\'bl_view_' . _bl::$count_vars . '\');">Close</a></p>
                            </div>
                            ' . $valor_html;
                            $toggle = '<br /><a href="javascript:bl_toggle(\'bl_view_more_' . _bl::$count_vars .
                                '\');bl_toggle(\'bl_view_' . _bl::$count_vars . '\');">[...]</a>';
                        }

                    }

                }

                // Add content to empty vars for better render on html table
                if (empty($valor)) {
                    $valor = '&nbsp;';
                }

                // get var size (memory)
                if ($var_type == 'Object') {
                    $var_size = 0;
                    if (_BL_SERIALIZE_OBJECTS == true) {
                        $var_size = strlen(serialize($v));
                    }

                } else {
                    $var_size = strlen(serialize($v));
                }

                $prefix = '$';
                if ($array_name == '_CONSTANTS') {
                    $prefix = '';
                }
                $results++;

                $tr_id = 'bl_var' . $array_name . '_' . $count . '';

                $extra_cols = '';
                if ((_BL_DELETE_VARS == true) and
                    ($array_name == '_SESSION' or $array_name == '_COOKIE')) {
                    // bl_del_var(var_name, url, type, key)
                    $extra_cols = '
                        <td>
                            <a href="javascript:bl_del_var(\'' . $k . '\', \'' .
                        _BL_PATH . '\', \'' . $array_name . '\', \'' . _BL_SECRET_KEY . '\', \'' .
                        $tr_id . '\', \''._BL_VAR_DEL.'\');">delete</a>
                        </td>';
                }

                $result .= '
                    <tr id="' . $tr_id .
                    '" onmouseover="bl_highlight_row(true, this)" onmouseout="bl_highlight_row(false, this)">
                        <td class="bl_col_title">$' . $k . $toggle . $html_button . '</td>
                        <td>' . $valor . '</td>
                        <td>' . $var_type . '</td>
                        <td class="bl_right">' . bl_convert($var_size) . '</td>
                        ' . $extra_cols . '
                    </tr>';

                if ($var_size > _bl::$max_var_size['size']) {
                    _bl::$max_var_size['var'] = $k;
                    _bl::$max_var_size['size'] = $var_size;
                }

                $count++;
            }
        }
        $result .= '
            </tbody>
        </table>';

    }

    if ($results == 0) {
        $result = '<div class="bl_nothing"><p>Array ' . $array_name . ' is empty</p></div>';
    }

    return $result;
}

/**
 * Use reflection class for get Comments of a class, method or function
 *
 * @param object $reflection A reflection object
 *
 * @return string The comment if there're any or a text "No phpDocs" if not.
 */
function bl_get_comments($reflection)
{
    $result = '';
    $comments = $reflection->getDocComment();
    if (empty($comments)) {
        $result = 'No phpDocs';
    } else {
        $comments = htmlspecialchars($comments);
        $result = '<span class="bl_orange">' . str_replace("\n", '<br />', $comments) .
            '</span>';
    }
    return $result;
}

/**
 * Get a list of declared functions and generate an HTML table
 *
 * @return string An HTML table with a list of functions
 */
function bl_get_functions()
{
    $functions = get_defined_functions();
    $functions = $functions['user'];

    if (count($functions)) {
        $table = '
            <table id="bl_table_functions">
                <thead>
                    <tr>
                        <th>Function</th>
                        <th>File</th>
                        <th>Line</th>
                        <th>Comments</th>
                    </tr>
                </thead>
                <tbody>';
        $tr_table = '';

        foreach ($functions as $k => $function) {
            if (substr($function, 0, 3) == 'bl_') {
                unset($functions[$k]);
            } else {
                $reflection = new ReflectionFunction($function);
                $num_required_params = $reflection->getNumberOfRequiredParameters();
                $params = $reflection->getParameters();
                $function_params = '';
                $count = 1;

                foreach ($params as $param) {
                    if ($count > $num_required_params) {
                        $function_params .= '[$' . $param->name . '], ';
                    } else {
                        $function_params .= '$' . $param->name . ', ';
                    }
                    $count++;
                }

                $comments = $reflection->getDocComment();
                if (empty($comments)) {
                    $comments = 'No phpDocs';
                }

                $tr_table .= '<tr onmouseover="bl_highlight_row(true, this)" onmouseout="bl_highlight_row(false, this)">
                        <td><strong>' . $function . '</strong> ( ' . rtrim($function_params,
                    ', ') . ' )</td>
                        <td>' . $reflection->getFileName() . '</td>
                        <td>' . $reflection->getStartLine() . '</td>
                        <td>' . bl_get_comments($reflection) . '</td>
                    </tr>';

            }
        }

        $table .= $tr_table.'</tbody></table>';

        $functions = $table;
    } else {
        $functions = '<div class="bl_nothing"><p>There aren\'t user functions</p></div>';
    }

    return $functions;
}

/**
 * Get methods of a class (reflection)
 *
 * @param object $reflection A reflection object
 * @param string $count      Identifier for HTML class
 *
 * @return string
 */
function bl_get_class_methods($reflection, $count)
{
    $result = '';
    $methods = $reflection->getMethods();
    if (count($methods)) {
        foreach ($methods as $method) {
            $method_params_text = '';
            $method_params = $reflection->getMethod($method->name)->getParameters();

            $access = '';
            if ($reflection->getMethod($method->name)->isPublic()) {
                $access = '<span class="bl_grey">public</span> ';
            }
            if ($reflection->getMethod($method->name)->isPrivate()) {
                $access = '<span class="bl_grey">private</span> ';
            }
            if ($reflection->getMethod($method->name)->isProtected()) {
                $access = '<span class="bl_grey">protected</span> ';
            }
            if ($reflection->getMethod($method->name)->isStatic()) {
                $access = '<span class="bl_grey">static</span> ';
            }

            foreach ($method_params as $param) {
                $method_params_text .= '$'.$param->name.', ';
            }
            $result .= '<span class="bl_class_'.$count.'">' . $access .
                '<span class="bl_blue"><strong>'.$method->name.'</strong></span>('.
                rtrim($method_params_text, ', ').');</span> ';
        }
    }

    if (empty($result)) {
        $result = 'nothing';
    } else {
        $result = rtrim($result, ' - ');
    }

    return $result;
}

/**
 * Return properties of a class
 *
 * @param object $reflection A reflection object
 * @param string $count      Identifier for HTML class
 *
 * @return string
 */
function bl_get_class_properties($reflection, $count)
{
    $result = '';
    $properties = $reflection->getProperties();
    if (count($properties)) {
        foreach ($properties as $prop) {

            $access = '';
            if ($reflection->getProperty($prop->name)->isPublic()) {
                $access = '<span class="bl_grey">public</span> ';
            }
            if ($reflection->getProperty($prop->name)->isPrivate()) {
                $access = '<span class="bl_grey">private</span> ';
            }
            if ($reflection->getProperty($prop->name)->isProtected()) {
                $access = '<span class="bl_grey">protected</span> ';
            }
            if ($reflection->getProperty($prop->name)->isStatic()) {
                $access = '<span class="bl_grey">static</span> ';
            }

            $result .= '<span class="bl_class_' . $count . '">' . $access .
                '<span class="bl_blue"><strong>' . $prop->name . ';</strong></span></span> ';
        }
    }

    if (empty($result)) {
        $result = 'nothing';
    } else {
        $result = rtrim($result, ' - ');
    }

    return $result;
}

/**
 * Get user and internal php classes
 *
 * @return string HTML table
 */
function bl_get_classes()
{
    $classes = get_declared_classes();
    $result  = array("user" => "", "internal" => "");

    $table   = '
        <table id="bl_table_{mode}">
            <thead>
                <tr>
                    <th>Class</th>
                    <th>Methods</th>
                    <th>Properties</th>
                    <th>File</th>
                    <th>Line</th>
                    <th>Comments</th>
                </tr>
            </thead>
            <tbody>';

    // container for (u)ser and (i)nternal classes
    $utr = $itr = '';

    $count = '0';
    if (count($classes)) {
        foreach ($classes as $class) {

            if (substr($class, 0, 3) != 'bl_' and substr($class, 0, 3) != '_bl') {
                $reflection = new ReflectionClass($class);
                $methods = $properties = '';

                if ($reflection->isInternal()) {
                    if (_BL_SHOW_INTERNAL_CLASSES == true) {

                        $methods = bl_get_class_methods($reflection, $count);
                        $properties = bl_get_class_properties($reflection, $count);

                        $itr .= '
                            <tr onmouseover="bl_highlight_row(true, this)" onmouseout="bl_highlight_row(false, this)">
                                <td><strong>' . $class . '</strong><br />
                                    <a href="#" onclick="bl_expand(\'' . $count . '\')">expand</a></td>
                                <td>' . $methods . '</td>
                                <td>' . $properties . '</td>
                                <td> - </td>
                                <td>
                                    <div id="bl_method_comments_expand_'.$count.'"></div>
                                    <div id="bl_method_comments_'.$count.'" style="display:none;"></div>
                                </td>
                            </tr>';
                    }

                } else {

                    if (_BL_SHOW_USER_CLASSES == true) {
                        $comments = bl_get_comments($reflection);
                        $comments_expand = 'no phpDocs';

                        if ($comments != 'No phpDocs') {
                            $comments_expand = '<span class="bl_orange">expand for comments</span>';
                        } else {
                            $comments_expand = 'no phpDocs';
                        }

                        $methods    = bl_get_class_methods($reflection, $count);
                        $properties = bl_get_class_properties($reflection, $count);
                        $utr .= '
                            <tr onmouseover="bl_highlight_row(true, this)" onmouseout="bl_highlight_row(false, this)">
                                <td><strong>'.$class.'</strong><br />
                                    <a href="#" onclick="bl_expand(\''.$count.'\')">expand</a></td>
                                <td>'.$methods.'</td>
                                <td>'.$properties.'</td>
                                <td>'.$reflection->getFileName().'</td>
                                <td>'.$reflection->getStartLine().'</td>
                                <td>
                                    <div id="bl_method_comments_expand_'.$count.'">'.$comments_expand.'</div>
                                    <div id="bl_method_comments_'.$count.'" style="display:none;">'.$comments.'</div>
                                </td>
                            </tr>';
                    }
                }

                $count++;
            }
        }
    }

    $result['user'] = str_replace('{mode}', 'uclasses', $table) . $utr .
        '</tbody></table>';

    $result['internal'] = str_replace('{mode}', 'iclasses', $table) . $itr .
        '</tbody></table>';

    return $result;

}


/**
 * get php included files and generate a table
 *
 * @return string HTML table with included files
 */
function bl_included_files()
{
    $result = '';

    $files = get_included_files();
    asort($files);
    if (is_array($files) and count($files)) {

        $result = '<table>
            <thead>
                <tr>
                    <th>File</th>
                    <th class="bl_right">Size</th>
                </tr>
            </thead>
            <tbody>';
        foreach ($files as $file) {

            $filesize = filesize($file);

            if ($filesize > _bl::$max_file_size['size']) {
                _bl::$max_file_size['size'] = $filesize;
                $separate_path = explode(DIRECTORY_SEPARATOR, $file); // separate...
                _bl::$max_file_size['file'] = end($separate_path); // and get only the name of the file
            }

            if ($filesize > 0) {
                $filesize = bl_convert($filesize);
            }

            $result .= '
                <tr>
                    <td class="bl_col_title">' . $file .
                '</td>
                    <td class="bl_right">' . $filesize . '</td>
                </tr>';

        }
        $result .= '
            </tbody>
        </table>';
    }

    return $result;

}

/**
 * Get html table from time marks
 *
 * @return HTML table with time marks
 */
function bl_get_times($times)
{
    $result = '';

    if (is_array($times) and count($times)) {

        $result = '<table>
            <thead>
                <tr>
                    <th>Label</th>
                    <th class="bl_right">Value</th>
                </tr>
            </thead>
            <tbody>';
        foreach ($times as $time) {

            $result .= '
                <tr>
                    <td>' . $time['label'] . '</td>
                    <td class="bl_right">' . $time['time'] . '</td>
                </tr>';
        }
        $result .= '
            </tbody>
        </table>';
    }

    return $result;

}

/**
 * Return CSS code
 *
 * @return string The HTML code (<style>)
 */
function bl_css()
{
    return "<style type=\"text/css\">
    #bl_debug *{margin:0;padding:0;color:#111;z-index:100000; background-color:transparent;font-size:100%;text-align:left}#bl_debug a{text-decoration:none}#bl_debug_wrap{width:95%;margin:0 auto 0;position:fixed;bottom:0; margin-left:2%;margin-right:5px; font-family:'Lucida Sans Unicode','Lucida Grande',sans-serif;font-size:13px; z-index:999999999; position:fixed;left:0px;bottom:0px}body >div#bl_debug_wrap{position:fixed;left:0px;bottom:0px}.bl_opacity{opacity:0.1}#bl_debug span.error{color:#f00}#bl_debug_header{padding:10px;color:#fff; height:20px;overflow:hidden; background:#ea0105; background:-moz-linear-gradient(top,#ea0105 1%,#ad0008 100%); background:-webkit-gradient(linear,left top,left bottom,color-stop(1%,#ea0105),color-stop(100%,#ad0008)); background:-webkit-linear-gradient(top,#ea0105 1%,#ad0008 100%); background:-o-linear-gradient(top,#ea0105 1%,#ad0008 100%); background:-ms-linear-gradient(top,#ea0105 1%,#ad0008 100%); filter:progid:DXImageTransform.Microsoft.gradient( startColorstr='#ea0105',endColorstr='#ad0008',GradientType=0 ); background:linear-gradient(top,#ea0105 1%,#ad0008 100%)}#bl_debug_menu{float:left}#bl_debug_menu *{color:#fff !important}#bl_debug_menu ul{overflow:hidden;list-style-type:none}#bl_debug_menu li,#bl_debug_menu a{display:block;float:left}#bl_debug_menu a{padding:3px;float:left;margin:0 10px;color:#fff !important;outline:none}#bl_debug_menu a.bl_debug_activo,#bl_debug_toggle a{ background-color:#BA0106;border:1px solid #222; border-radius:5px}#bl_debug_menu sup{font-size:8px}#bl_debug_toggle{float:right;width:250px;font-size:10px}#bl_debug_toggle_buttons a{float:right;margin:0 3px;padding:2px 3px; color:#fff;text-decoration:none; outline:none}#bl_debug_toggle_buttons a{text-decoration:none}#bl_tool_box{position:absolute;bottom:40px;right:100px;width:460px; height:400px;border:3px solid #E80005;background-color:#fff; border-bottom:none;overflow:auto;overflow:hidden;opacity:0.9}#bl_debug_toggle #bl_tool_box a{background:none;border:none;text-decoration:none}#bl_debug_toggle #bl_tool_box a{color:#f33;font-size:12px} #bl_debug_toggle #bl_tool_box a:hover{color:#f00;text-decoration:underline}#bl_debug_toggle #bl_tool_box ul{margin:0 0 10px 5px}#bl_debug_toggle #bl_tool_box h3{margin-bottom:5px;font-size:14px}#bl_js_css{width:200px;float:left;word-wrap:break-word; border-left:1px solid #ccc;padding:15px}#bl_bookmarks{width:200px;float:left;padding:10px}.bl_half_panel{height:300px}.bl_close_panel{height:0;display:none}.bl_full_panel{height:600px}#bl_debug_content{border:5px solid #EA0105;border-bottom:none;background-color:#fff}#bl_debug table{font-size:11px}#bl_debug table th{text-align:left;padding:10px;background-color:#ddf1fb; border-bottom:1px dashed #ccc}#bl_debug table td{padding:5px;border-bottom:1px dashed #555; vertical-align:top;text-align:left}#bl_debug table td.bl_col_title{background-color:#f6f6f6;font-weight:bold; border-right:1px dashed #ccc}#bl_debug_panels{background-color:#fff;overflow:auto}.bl_half_panel #bl_debug_panels{height:300px}.bl_full_panel #bl_debug_panels{height:600px}#bl_debug_panels div.bl_debug_panel_active{display:block}#bl_debug .bl_panel_info a{color:#008000;text-decoration:none}.bl_panel_info{width:85%;float:left}.bl_debug_panel{display:none;overflow:auto}.bl_full_panel .bl_debug_panel{display:none; height:600px}#bl_debug_var_panels,#bl_debug_php_panels {background-color:#fff;overflow:auto}.bl_debug_var_panel,.bl_debug_php_panel {display:none;overflow:auto}#bl_debug_var_panels div.bl_debug_var_panel_activo,#bl_debug_php_panels div.bl_debug_php_panel_activo {display:block}#bl_debug #bl_debug_info h3,#bl_debug #bl_debug_info p,#bl_debug #bl_debug_info ul{margin-bottom:15px}#bl_debug #bl_debug_info ul{margin-left:15px !important}#bl_debug .bl_menu_vertical{background-color:#880400;width:auto; overflow:hidden;min-width:100px;float:left}#bl_debug .bl_menu_vertical li,.bl_menu_vertical a{display:block}#bl_debug .bl_menu_vertical a{display:block;background-color:#DA1010; padding:3px;border-bottom:1px solid #ea0105;color:#fff}#bl_debug .bl_menu_vertical a:hover{background-color:#C92929}#bl_debug_msg_menu a.bl_debug_msg_btn_activo,#bl_debug_var_menu a.bl_debug_var_btn_activo,#bl_debug_php_menu a.bl_debug_php_btn_activo{ background-color:#fff;color:#333}#bl_debug_msg_menu a.bl_debug_msg_btn_activo:hover,#bl_debug_var_menu a.bl_debug_var_btn_activo:hover,#bl_debug_php_menu a.bl_debug_php_btn_activo:hover{ background-color:#fff;color:#333}#bl_debug .in20{padding:20px}#bl_debug .bl_debug_var_content .no_top_in{padding-top:0 !important}#bl_debug .in10{padding:10px}#bl_debug .bl_border_top{border-top-left-radius:0.8ex;border-top-right-radius:0.8ex}#bl_debug .bl_right{text-align:right}#bl_debug .bl_nothing p{color:#666 !important;font-size:3em;text-align:center;padding:20px}#bl_debug .bl_opacity{opacity:0.3;filter:alpha(opacity = 30)}.bl_vars_box{width:250px;height:250px;float:left;margin:20px}.bl_vars_box_title{background-color:#222;color:#fff}#bl_debug td.bl_msg_error{background-color:#f33;color:#000}#bl_debug td.bl_msg_warn{background-color:#F90;color:#000}#bl_debug td.bl_msg_info{background-color:#36F;color:#000}#bl_debug td.bl_msg_user{background-color:#333;color:#000}#bl_debug .bl_normal_td .bl_td{background-color:transparent}#bl_debug .bl_hover_td .bl_td{background-color:#999}#bl_debug .bl_msg_info{font-weight:bold}#bl_debug .bl_msg_file{font-style:italic}#bl_debug .bl_msg_line{font-style:italic}#bl_debug .bl_msg_separator{color:#f00;margin:0 10px}#bl_debug .bl_msg_table tbody tr{display:none}#bl_debug .bl_msg_table tr.bl_highlight_row td.bl_td{background-color:#eee}#bl_debug .bl_msg_table tbody tr.bl_msg_activo{display:table-row}#bl_debug table.bl_backtrace tr{display:table-row}#bl_debug table.bl_backtrace th, #bl_debug table.bl_backtrace td {border-bottom:none;padding:2px}#bl_memory_box{width:200px}#bl_memory_box,#bl_included{padding:5px;background-color:#f6f6f6;border:1px solid #ccc; float:left;margin:0 10px; border-radius:5px; -moz-border-radius:5px; -webkit-border-radius:5px}#bl_memory_box h3,#bl_included h3{padding:5px;border-bottom:1px dashed #666;margin-bottom:10px}#bl_memory_box span{display:block}#bl_debug .bl_view_html{display:none;margin:10px}#bl_debug .bl_view_html_title{width:100px;margin-right:20px;padding:5px;background-color:#ccc}#bl_debug .bl_view_html_content{border:1px solid #ccc;padding:10px}#bl_debug_heatmap{text-align:left}#bl_debug .bl_box{width:200px;margin-bottom:15px}#bl_debug .bl_box2{width:400px}#bl_debug .bl_box,#bl_debug .bl_box2,#bl_included{padding:5px;background-color:#f6f6f6;border:1px solid #ccc; float:left;margin:0 10px 15px; border-radius:5px; -moz-border-radius:5px; -webkit-border-radius:5px}#bl_debug .bl_box h3,#bl_debug .bl_box2 h3,#bl_included h3{ padding:5px;border-bottom:1px dashed #666;margin-bottom:10px}#bl_debug .bl_box span,#bl_debug .bl_box2 span{display:block}#bl_debug .bl_view_html{display:none;margin:10px}#bl_debug .bl_view_html_title{width:100px;margin-right:20px;padding:5px;background-color:#ccc}#bl_debug .bl_view_html_content{border:1px solid #ccc;padding:10px}#bl_debug .bl_filter_box{background-color:#f3f3f3;border-radius:3px; width:200px;padding:5px;margin-bottom:5px;border:1px solid #ccc}#bl_debug .bl_filter_box input{border:1px solid #444}#bl_show_errors{padding:5px;background-color:#fff;border:2px solid #f00; position:fixed;top:10px;right:10px;display:block;font-size:14px; font-weight:bold}#bl_debug .bl_blue{color:#00F}#bl_debug .bl_blue strong{color:#00F}#bl_debug .bl_grey{color:#999}#bl_debug .bl_orange{color:#F60}#bl_debug #bl_file_container{position:fixed;width:90%;height:80%; top:50px;left:50px; border:4px solid #333;background-color:#fff; display:none;overflow:hidden; -webkit-box-shadow:0px 0px 30px rgba(15,15,15,1); -moz-box-shadow: 0px 0px 30px rgba(15,15,15,1); box-shadow: 0px 0px 30px rgba(15,15,15,1)}#bl_debug #bl_file_explorer{overflow:auto;height:95%}#bl_debug #bl_header_browser{overflow:hidden;height:5%;background-color:#333; font-size:1.2em;font-weight:bold;color:#eee !important}#bl_debug #bl_header_browser p,#bl_debug #bl_header_browser a{color:#fff !important}#bl_debug #bl_file_container .highlight_line{background-color:#C3E9FF}#bl_debug #bl_loading{display:none;padding:5px; background-color:#fff;color:#fff;font-weight:bold; position:fixed;margin-left:45%;width:100px;text-align:center; border:4px solid #ea0105;border-top:none}
    </style>";
}

/**
 * Return JS code
 *
 * @return string HTML code (<script>)
 */
function bl_js()
{
    return "
    <script type=\"text/javascript\">
    var bl_shortcuts=true,bl_key_msg=49,bl_key_sql=50,bl_key_vars=51,bl_key_time=52,bl_key_memory=52,bl_key_opacity=79,bl_key_info=73,bl_key_plus=77,bl_key_close=88;var \$bl=function(id){return document.getElementById(id)};String.prototype.trim=function(){return this.replace(/^\s+|\s+\$/g,\"\")};String.prototype.ltrim=function(){return this.replace(/^\s+/,\"\")};String.prototype.rtrim=function(){return this.replace(/\s+\$/,\"\")};Element.prototype.hasClass=function(class_name){this.className=this.className.replace(/^\s+|\s+\$/g,\"\");this.className=\" \"+this.className+\" \";if(this.className.search(\" \"+class_name+\" \")!==-1){return true}this.className=this.className.replace(/^\s+|\s+\$/g,\"\");return false};Element.prototype.removeClass=function(class_name){this.className=this.className.replace(class_name,'');this.className=this.className.replace(/^\s+|\s+\$/g,\"\")};Element.prototype.addClass=function(class_name){this.className=this.className+' '+class_name;this.className=this.className.replace(/^\s+|\s+\$/g,\"\")};function bl_toggle(obj,mode){var el=document.getElementById(obj);if(mode==='more'){document.getElementById(\"bl_debug_content\").style.display='block';if(el.className==='bl_full_panel'){el.className='bl_half_panel'}else{el.className='bl_full_panel'}}else{if(el.style.display!=='block'){el.style.display='block'}else{el.style.display='none'}}}function randomString(length){var str,i,chars='abcdefghiklmnopqrstuvwxyz'.split('');if(!length){length=Math.floor(Math.random()*chars.length)}for(i=0;i<length;i+=1){str+=chars[Math.floor(Math.random()*chars.length)]}return str}function time(ms){var t=ms/1000;return Math.round(t*100)/100}function bl_listen(event,elem,func,id){if(id){elem=\$bl(elem)}else{elem=document}if(elem){if(elem.addEventListener){elem.addEventListener(event,func,false)}else if(elem.attachEvent){var r=elem.attachEvent(\"on\"+event,func);return r}else{throw'No es posible añadir evento';}}}bl_listen('keyup','body',bl_keydown);function bl_keydown(e){var target;if(!bl_shortcuts){return}if(navigator.appName==='Microsoft Internet Explorer'){e=window.event;target=e.srcElement.nodeName.toLowerCase()}else{target=e.target.localName}if(target==='html'||target==='body'){if(e.keyCode===bl_key_msg){bl_debug_set_panel('msg')}else if(e.keyCode===bl_key_sql){bl_debug_set_panel('sql')}else if(e.keyCode===bl_key_vars){bl_debug_set_panel('vars')}else if(e.keyCode===bl_key_time){bl_debug_set_panel('time')}else if(e.keyCode===bl_key_memory){bl_debug_set_panel('memory')}else if(e.keyCode===bl_key_opacity){bl_opacity()}else if(e.keyCode===bl_key_info){bl_debug_set_panel('info')}else if(e.keyCode===bl_key_plus){bl_setPanelSize('plus')}else if(e.keyCode===bl_key_close){bl_setPanelSize('close')}}}function bl_view_html(el){var el1=document.getElementById('bl_view_html_'+el),el2=document.getElementById('bl_view_'+el),el3=document.getElementById('bl_view_more_'+el);if(el1.style.display==='block'){el1.style.display='none';el2.style.display='block';el3.style.display='none'}else{el1.style.display='block';el2.style.display='none';el3.style.display='none'}}function bl_show_errors(){bl_toggle('bl_show_errors')}function bl_alert_errors(){var bl_interval=setInterval(bl_show_errors(),500);setTimeout(\"clearInterval(\"+bl_interval+\")\",3000)}function bl_opacity(){var el=\$bl('bl_debug');if(el.hasClass('bl_opacity')){el.removeClass('bl_opacity')}else{el.addClass('bl_opacity')}}function bl_setPanelSize(size){var panel_size='close';if(size==='plus'){if(\$bl('bl_debug_content').className==='bl_half_panel'){\$bl('bl_debug_content').className='bl_full_panel';panel_size='full'}else{\$bl('bl_debug_content').className='bl_half_panel';panel_size='half'}}else if(size==='close'){\$bl('bl_debug_content').className='bl_close_panel';panel_size='close'}else{\$bl('bl_debug_content').className='bl_'+size+'_panel';panel_size='half'}if(panel_size==='close'){bl_setCookie('__bl_panel_active','none',1)}bl_setCookie('panel_size_bl',panel_size,1)}function bl_debug_set_panel(panel){var c1=\"bl_debug_panel\",c2=\"bl_debug_panel_active\",c3=\"bl_debug_btn\",c4=\"bl_debug_activo\";if(\$bl(\"bl_debug_\"+panel).hasClass(\"bl_debug_panel_active\")){\$bl(\"bl_debug_\"+panel).className=c1;\$bl(\"bl_debug_content\").className='bl_close_panel';\$bl(c3+\"_\"+panel).className=c3;bl_setPanelSize('close')}else{\$bl(\"bl_debug_msg\").className=c1;\$bl(\"bl_debug_sql\").className=c1;\$bl(\"bl_debug_vars\").className=c1;\$bl(\"bl_debug_time\").className=c1;\$bl(\"bl_debug_memory\").className=c1;\$bl(\"bl_debug_info\").className=c1;\$bl(\"bl_debug_\"+panel).className=c1+\" \"+c2;\$bl(\"bl_debug_btn_msg\").className=c3;\$bl(c3+\"_sql\").className=c3;\$bl(c3+\"_vars\").className=c3;\$bl(c3+\"_time\").className=c3;\$bl(c3+\"_memory\").className=c3;\$bl(c3+\"_\"+panel).className=c3+\" \"+c4;if(\$bl(\"bl_debug_content\").hasClass('bl_close_panel')){\$bl(\"bl_debug_content\").className='bl_half_panel';bl_setPanelSize('half')}}bl_setCookie('__bl_panel_active',panel,1)}function bl_debug_set_msg(type){var i,bl_search,bl_search2,e,allHTMLTags=document.getElementsByTagName(\"tr\");for(i=0;i<allHTMLTags.length;i+=1){if(allHTMLTags[i].className.search('bl_normal_tr')!==-1){allHTMLTags[i].className=allHTMLTags[i].className.replace('bl_msg_activo','');bl_search=allHTMLTags[i].className.search('bl_debug_msg_'+type);bl_search2=allHTMLTags[i].className.search('bl_msg_activo');if(bl_search!==-1){if(bl_search2===-1){allHTMLTags[i].className=allHTMLTags[i].className+' bl_msg_activo'}}else{if(type==='all'){if(bl_search2===-1){allHTMLTags[i].className=allHTMLTags[i].className+' bl_msg_activo'}}}}}allHTMLTags=document.getElementsByTagName(\"a\");for(i=0;i<allHTMLTags.length;i+=1){if(allHTMLTags[i].className.search('bl_debug_msg_btn')!==-1){allHTMLTags[i].className='bl_debug_msg_btn'}}e=document.getElementById('bl_debug_msg_btn_'+type);e.addClass('bl_debug_msg_btn_activo')}function bl_debug_set_var(panel){var i,e,allHTMLTags=document.getElementsByTagName(\"div\");for(i=0;i<allHTMLTags.length;i+=1){if(allHTMLTags[i].className.search('bl_debug_var_panel')!==-1){allHTMLTags[i].className='bl_debug_var_panel'}}allHTMLTags=document.getElementsByTagName(\"a\");for(i=0;i<allHTMLTags.length;i+=1){if(allHTMLTags[i].className.search('bl_debug_var_btn')!==-1){allHTMLTags[i].className='bl_debug_var_btn'}}e=document.getElementById('bl_debug_var_btn_'+panel);e.addClass('bl_debug_var_btn_activo');e=document.getElementById('bl_debug_var_'+panel);e.addClass('bl_debug_var_panel_activo')}function bl_expand(count){var i,allHTMLTags=document.getElementsByTagName(\"span\");for(i=0;i<allHTMLTags.length;i+=1){if(allHTMLTags[i].className.search('bl_class_'+count)!==-1){if(allHTMLTags[i].style.display!=='block'){allHTMLTags[i].style.display='block';\$bl('bl_method_comments_expand_'+count).style.display='none';\$bl('bl_method_comments_'+count).style.display='block'}else{allHTMLTags[i].style.display='inline';\$bl('bl_method_comments_expand_'+count).style.display='block';\$bl('bl_method_comments_'+count).style.display='none'}}}}function filter(phrase,id){var words=\$bl(phrase).value.toLowerCase().split(\" \"),table=document.getElementById(id),ele,r,i,displayStyle;for(r=1;r<table.rows.length;r+=1){ele=table.rows[r].innerHTML.replace(/<[^>]+>/g,\"\");displayStyle=\"none\";for(i=0;i<words.length;i+=1){if(ele.toLowerCase().indexOf(words[i])>=0){displayStyle=\"\"}else{displayStyle=\"none\";break}}table.rows[r].style.display=displayStyle}}function filterUser(){filter('bl_filter_user','bl_table_user')}function filterSpecial(){filter('bl_filter_special','bl_table_special')}function filterFunctions(){filter('bl_filter_functions','bl_table_functions')}function filterUclasses(){filter('bl_filter_uclasses','bl_table_uclasses')}function filterIclasses(){filter('bl_filter_iclasses','bl_table_iclasses')}function filterConstants(){filter('bl_filter_constants','bl_table_constants')}function filterGet(){filter('bl_filter_get','bl_table_get')}function filterPost(){filter('bl_filter_post','bl_table_post')}function filterSession(){filter('bl_filter_session','bl_table_session')}function filterCookie(){filter('bl_filter_cookie','bl_table_cookie')}function filterFiles(){filter('bl_filter_files','bl_table_files')}function filterServer(){filter('bl_filter_server','bl_table_server')}bl_listen('keyup','bl_filter_user',filterUser,true);bl_listen('keyup','bl_filter_special',filterSpecial,true);bl_listen('keyup','bl_filter_functions',filterFunctions,true);bl_listen('keyup','bl_filter_uclasses',filterUclasses,true);bl_listen('keyup','bl_filter_iclasses',filterIclasses,true);bl_listen('keyup','bl_filter_constants',filterConstants,true);bl_listen('keyup','bl_filter_get',filterGet,true);bl_listen('keyup','bl_filter_post',filterPost,true);bl_listen('keyup','bl_filter_session',filterSession,true);bl_listen('keyup','bl_filter_cookie',filterCookie,true);bl_listen('keyup','bl_filter_files',filterFiles,true);bl_listen('keyup','bl_filter_server',filterServer,true);function bl_ajax(){var xmlhttp=false;try{xmlhttp=new ActiveXObject(\"Msxml2.XMLHTTP\")}catch(e){try{xmlhttp=new ActiveXObject(\"Microsoft.XMLHTTP\")}catch(E){xmlhttp=false}}if(!xmlhttp&&typeof XMLHttpRequest!=='undefined'){xmlhttp=new XMLHttpRequest()}return xmlhttp}function bl_del_var(var_name,url,type,key,tr_id,url_var_name){var ajax;url=url+'?'+url_var_name+'=1&var='+var_name+'&type='+type+'&bl_key='+key;\$bl('bl_loading').style.display='block';ajax=bl_ajax();ajax.open(\"GET\",url,true);ajax.onreadystatechange=function(){if(ajax.readyState===4){\$bl('bl_loading').style.display='none';if(ajax.responseText==='ok'){var tr=\$bl(tr_id);tr.innerHTML='<td colspan=\"5\">var \$'+type+'[\"'+var_name+'\"]  deleted</td>'}else if(ajax.responseText==='error-key'){alert('There\'re a problem with your secret key')}else if(ajax.responseText==='error-cookie'){alert('Sorry, I can\t delete this cookie.')}else{alert('Error. No vars deleted!')}}};ajax.send(null)}function bl_highlight_row(highlight,el){if(highlight===true){el.addClass('bl_highlight_row')}else{el.removeClass('bl_highlight_row')}}function view_array(id){var div=document.getElementById(id),a=document.getElementById(id.replace('div_','a_'));if(div.style.display==='block'){div.style.display='none';a.style.display='block'}else{div.style.display='block';a.style.display='none'}}function htmlentities(str){return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\"/g,'&quot;')}function bl_setCookie(c_name,value,exdays){var c_value,exdate=new Date();exdate.setDate(exdate.getDate()+exdays);c_value=escape(value)+((exdays===null)?\"\":\"; expires=\"+exdate.toUTCString());document.cookie=c_name+\"=\"+c_value+'; path=/'}
    </script>";
}

/**
 * Return current page URL
 *
 * @link http://www.phpf1.com/tutorial/get-current-page-url.html
 *
 * @return string
 */
function bl_get_url()
{
    $protocol = strpos(
        strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === false
            ? 'http' : 'https';

    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME'];
    $params = $_SERVER['QUERY_STRING'];

    $currentUrl = $protocol . '://' . $host . $script . '?' . $params;

    return $currentUrl;
}


/**
 * Encoded loading image
 */
function bl_loading_image()
{
    return 'data:image/gif;base64,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%3D%3D';
}

/**
 * Monitor option for max load time.
 *
 * @param double $load_time Max load time calculated in {@link bl_debug()}
 */
function bl_check_load_time($load_time)
{
    if (_BL_MONITOR_TIMES and _BL_MAX_LOAD_TIME) {

        if ($load_time > _BL_MAX_LOAD_TIME) {
            $data = array();
            $data['Time Max'] = strip_tags(bl_format_time(_BL_MAX_LOAD_TIME));
            $data['Time Load'] = '<span style="color:#f00;">' . strip_tags(bl_format_time
                ($load_time)) . '</span>';
            $data['Exceded'] = strip_tags(bl_format_time($load_time - _BL_MAX_LOAD_TIME));
            $data['Url'] = bl_get_url();

            $msg = '<h3>Max Load time exceded</h3>';
            $title = 'Max Load time exceded';

            bl_send_mail($msg, $title, $data);
        }
    }
}

/**
 * Monitor for memory usage
 *
 * @param double $total_memory Total memory calculated in {@link bl_debug()}
 *
 * @return void
 */
function bl_check_total_memory($total_memory)
{
    if (_BL_MONITOR_MEMORY and _BL_MAX_TOTAL_MEMORY > 0) {

        if ($total_memory > _BL_MAX_TOTAL_MEMORY) {

            $exceded = $total_memory - _BL_MAX_TOTAL_MEMORY;

            $data = array();
            $data['Memory Max'] = strip_tags(bl_convert(_BL_MAX_TOTAL_MEMORY));
            $data['Memory Used'] = '<span style="color:#f00;">' . strip_tags(bl_convert($total_memory)) .
                '</span>';
            $data['Exceded'] = strip_tags(bl_convert($exceded));
            $data['Url'] = bl_get_url();

            $msg = '<h3>Total memory ammount exceded</h3>';
            $title = 'Memory exceded';

            bl_send_mail($msg, $title, $data);
        }
    }
}

/**
 * Monitor for SQL querys
 *
 * @param array $bl_msg_sql List of querys
 *
 * @return void
 */
function bl_check_querys($bl_msg_sql)
{
    if (_BL_MONITOR_SQL) {
        foreach ($bl_msg_sql as $v) {
            if (!empty($v['error'])) {
                $v['time'] = bl_format_time($v['time']);
                $v['error'] = '<span style="color:#f00;">' . $v['error'] . '</span>';
                $msg = '<h3>SQL Crash</h3> <p>Ther\'re a problem with this query:</p>';
                $title = 'SQL Fail';

                bl_send_mail($msg, $title, $v);
            }

            // monitor, max time for querys
            if (_BL_MAX_SQL_TIME > 0) {
                if ($v['time'] > _BL_MAX_SQL_TIME) {

                    $v['time'] = '<span style="color:#f00;">' . bl_format_time($v['time']) .
                        '</span>';
                    $v['Max Time'] = _BL_MAX_SQL_TIME;
                    $msg = '<h3>A SQL Query has exceded the max time for querys </h3>';
                    $title = 'SQL Query exceded max time for querys';
                    bl_send_mail($msg, $title, $v);
                }
            }
        }
    }
}

/**
 * Monitor for times
 *
 * @param array $bl_msg_time List of time marks
 *
 * @return void
 */
function bl_check_times($bl_msg_time)
{
    // monitor, check any max time
    if (_BL_MONITOR_TIMES and _BL_MAX_ANY_TIME > 0) {

        foreach ($bl_msg_time as $v) {
            $the_time = rtrim($v['time'], 's');
            if ($the_time > _BL_MAX_ANY_TIME) {
                $data = array();
                $data['Label'] = $v['label'];
                $data['Max Time'] = bl_format_time(_BL_MAX_ANY_TIME);
                $data['Time'] = bl_format_time($the_time);
                $data['Exceded'] = bl_format_time($the_time - _BL_MAX_ANY_TIME);
                $data['Url'] = bl_get_url();

                $msg = '<h3>A Time Mark has exceded the max time</h3>';
                $title = 'Time Mark Exceded';

                bl_send_mail($msg, $title, $data);
            }
        }
    }
}


/**
 * return the ip address
 *
 * @return string Current user IP addres
 */
function bl_get_ip()
{
    $ip = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];

    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    return $ip;
}


/**
 * Check if the client ip is an allowed ip
 *
 * @return bool
 */
function bl_check_ip()
{
    // get ips from _BL_ALLOW_IP
    $allow_ip = trim(_BL_ALLOW_IP);
    if (empty($allow_ip)) {
        return true;
    }

    $extract_ips = explode(',', _BL_ALLOW_IP);
    $extract_ips = array_map('trim', $extract_ips);
    if (in_array(bl_get_ip(), $extract_ips)) {
        return true;
    }

    return false;
}

/**
 * Generate the debug panel.
 * Use global vars and public functions for get the contents of the debug panel
 *
 * @param bool $active True/False for show/hide console
 *
 * @return string All HTML code for the debug panel.
 */
function bl_debug($active = false, $return = false)
{
    // get memory
    $memory = memory_get_peak_usage();
    $memory_num = strip_tags(bl_convert($memory));

    /* get load time */
    $total_time = bl_get_time(_bl::$time_start);
    $total_load_time = bl_format_time($total_time);
    $count = count(_bl::$msgs_time);
    _bl::$msgs_time[$count]['label'] = '<strong>Total Load Time</strong>';
    _bl::$msgs_time[$count]['time'] = $total_load_time;

    // defined vars
    $defined_vars = $GLOBALS;

    // check monitor
    if (_BL_MONITOR_ON) {
        bl_check_total_memory($memory); // monitor check memory
        bl_check_load_time($total_time); // monitor check load time
        bl_check_times(_bl::$msgs_time); // monitor check time marks
        bl_check_querys(_bl::$msg_sql); // monitor check querys
    }

    ///////////////////////
    // security
    
    // check secret key
    if (_BL_DELETE_VARS == true and _BL_SECRET_KEY == '_pbl_') {
        echo '<strong>ERROR FROM PHP BUG LOST:</strong> Sorry for this error!
            but you need to change your secret key
            otherwise it is not secret! Open you PHP Bug Lost file,
            search for _BL_SECRET_KEY constant and change with any word, number or
            alphanumeric string.';
        exit();
    }

    if ($active == false) {
        return '';
    }

    // check IP
    if (bl_check_ip() == false) {
        return '';
    }
    // end security
    ///////////////////////


    /* get vars */
    $post     = bl_get_vars($_POST, '_POST');
    $get      = bl_get_vars($_GET, '_GET');
    $server   = bl_get_vars($_SERVER, '_SERVER');
    $files    = bl_get_vars($_FILES, '_FILES');
    $cookie   = bl_get_vars($_COOKIE, '_COOKIE');
    $user     = '';
    $specials = bl_get_vars(_bl::$vars, '_SPECIAL');

    if (isset($_SESSION)) {
        $session = bl_get_vars($_SESSION, '_SESSION');
    } else {
        $session = '<div class="bl_nothing"><p>Array _SESSION is empty</p></div>';
    }

    // get functions and classes
    $functions = bl_get_functions();
    $classes   = bl_get_classes();
    $uclasses  = $classes['user'];
    $uclasses  = (_BL_SHOW_USER_CLASSES == true)
        ? $classes['user']
        : '<div class="bl_nothing"><p>To view PHP user classes set
            <em>_BL_SHOW_USER_CLASSES</em> to true</p></div>';
    // internal classes
    $iclasses  = (_BL_SHOW_INTERNAL_CLASSES == true)
        ? $classes['internal']
        : '<div class="bl_nothing"><p>To view PHP internal classes set
            <em>_BL_SHOW_INTERNAL_CLASSES</em> to true</p></div>';

    // user constants
    $user_constant = get_defined_constants(true);
    $constants = '<div class="bl_nothing"><p>Array _CONSTANTS is empty</p></div>';
    if (isset($user_constant['user']) and count($user_constant['user'])) {
        $constants = bl_get_vars($user_constant['user'], '_CONSTANTS');
    }

    /* GET USER VARS */
    if (is_array($defined_vars)) {

        // temp var, only for check array keys on $defined_vars
        // TODO: check if any php version or config have more predefined vars
        $array_vars_names = array(
            '_SERVER',
            '_POST',
            '_GET',
            '_GLOBALS',
            '_SESSION',
            '_FILES',
            '_COOKIE',
            'GLOBALS',
            'REQUEST',
            'ENV');
        foreach ($array_vars_names as $v) {
            if (isset($defined_vars[$v])) {
                unset($defined_vars[$v]); // delete global var
            }
        }
        unset($array_vars_names); // don't need any more

        // by deleting all global vars, we get the user defined vars
        $user = bl_get_vars($defined_vars, '_USER');
        unset($defined_vars); // don't need any more
    }

    /* Memory usage */
    $memory_usage = '
    <div id="bl_included">
        <h3>PHP Included Files</h3>
        ' . bl_included_files() . '
    </div>';
    $memory_usage .= '
    <div class="bl_box">
        <h3>PHP Memory</h3>
        <span class="bl_memory_total">Available: ' . ini_get('memory_limit') . '</span>
        <span class="bl_memory_usage">Using: <strong>' . $memory_num .
        '</strong></span>
    </div>
    <div class="bl_box">
        <h3>Max File Size</h3>
        <span class="bl_memory_usage"><strong>' . str_ireplace(_BL_ROOT, '', _bl::$max_file_size['file']) .
        ' : ' . strip_tags(bl_convert(_bl::$max_file_size['size'])) .
        '</strong></span>
    </div>
    <div class="bl_box">
        <h3>Max Var Size</h3>
        <span class="bl_memory_usage"><strong>$' . _bl::$max_var_size['var'] . ' : ' .
        strip_tags(bl_convert(_bl::$max_var_size['size'])) . '</strong></span>
    </div>';

    /* sql querys */
    $querys = '<div class="bl_nothing"><p>There aren\'t querys</p></div>';
    if (is_array(_bl::$msg_sql) and count(_bl::$msg_sql)) {
        $querys = bl_get_querys(_bl::$msg_sql);
    }

    /* List of Messages */
    $message_list = bl_get_msg();
    if (!_bl::$count_msg) {
        $message_list = '<div class="bl_nothing"><p>There aren\'t messages</p></div>';
    }

    // Finally, generate and return the HTML code for the debug panel.
    // add js / css
    $result = bl_css() . bl_js();

    $result .= '
    <div id="bl_debug_wrap">
        <div id="bl_debug" class="bl_border_top bl_half bl_resize">
            <div id="bl_debug_content" class="bl_'._bl::$panel_state . '_panel">
                <span id="bl_loading"><img src="'.bl_loading_image().'" alt="Loading" /> loading...</span>
                <div id="bl_debug_panels">
                    <div id="bl_debug_msg" class="bl_debug_panel ' . _bl::$panel_active['msg'] . '">

                        <div id="bl_debug_msg_menu" class="bl_menu_vertical">
                            <ul>
                                <li><a href="javascript:bl_debug_set_msg(\'all\');" id="bl_debug_msg_btn_all" class="bl_debug_msg_btn bl_debug_msg_btn_activo">all</a></li>
                                <li><a href="javascript:bl_debug_set_msg(\'info\');" id="bl_debug_msg_btn_info" class="bl_debug_msg_btn">info</a></li>
                                <li><a href="javascript:bl_debug_set_msg(\'warn\');" id="bl_debug_msg_btn_warn" class="bl_debug_msg_btn">warn</a></li>
                                <li><a href="javascript:bl_debug_set_msg(\'error\');" id="bl_debug_msg_btn_error" class="bl_debug_msg_btn">error</a></li>
                                <li><a href="javascript:bl_debug_set_msg(\'user\');" id="bl_debug_msg_btn_user" class="bl_debug_msg_btn">user</a></li>
                            </ul>
                        </div>

                        <div class="in20 bl_panel_info">
                            ' . $message_list . '
                        </div>
                    </div>

                    <div id="bl_debug_sql" class="bl_debug_panel ' . _bl::$panel_active['sql'] . '">
                        <div class="in20 bl_panel_info">
                            ' . $querys . '
                        </div>
                    </div>

                    <div id="bl_debug_time" class="bl_debug_panel ' . _bl::$panel_active['time'] . '">
                        <div class="in20 bl_panel_info">
                            ' . bl_get_times(_bl::$msgs_time) . '
                        </div>
                    </div>

                    <div id="bl_debug_memory" class="bl_debug_panel ' . _bl::$panel_active['memory'] . '">
                        <div class="in20 bl_panel_info">
                            <div id="bl_debug_memory_box">
                                ' . $memory_usage . '
                            </div>
                        </div>
                    </div>

                    <div id="bl_debug_info" class="bl_debug_panel ' . _bl::$panel_active['info'] . '">
                        <div class="in20 bl_panel_info">
                            <h3>About...</h3>
                            <p>v@0.5a Lite</p>
                            <p><strong>PHP Bug Lost</strong> is Open Source. Original idea from
                                <a href="http://particletree.com/features/php-quick-profiler/">Php Quick Profiler</a>.</p>
                            <h3>Thanks To:</h3>
                            <ul>
                                <li>Ryan Campbell from <a href="http://particletree.com">particletree.com</a> for his <strong>Php Quick Profiler</strong>,
                                    the first inspiration for <strong>PHP Bug Lost</strong>.</li>
                                <li><a href="http://www.vonloesch.de">vonloesch.de</a> for his javascript table filter function</li>
                            </ul>

                            <h3>Thirt Party Bookmarklets</h3>
                            <p>
                            <a href="http://westciv.com/mri/">MRI (Test CSS Selector)</a> from
                                <a href="http://westciv.com">westciv.com</a>
                            <br />
                            <a href="http://westciv.com/xray">XRay</a> from
                                <a href="http://westciv.com/xray">westciv.com</a>
                            <a href="http://slayeroffice.com/?c=/content/tools/modi.html">Dom Inspector</a> from
                                    <a href="http://slayeroffice.com">slayeroffice.com</a><br />
                            <a href="http://slayeroffice.com/?c=/content/tools/suite.html">Favelet Suite</a> from
                                    <a href="http://slayeroffice.com">slayeroffice.com</a><br />
                            View Selected Source, View all JS, View al vars/functions, View all CSS, View Classes and Check Img Alt from
                                    <a href="https://www.squarefree.com/bookmarklets/">squarefree.com</a><br />
                            </p>


                            <p><strong>PHP Bug Lost</strong> by Jordi Enguídanos <small>(at gmail.com)</small>.
                                See docs and support at <a href="http://phpbuglost.com">phpbuglost.com</a></p>
                        </div>
                    </div>

                    <div id="bl_debug_vars" class="bl_debug_panel ' . _bl::$panel_active['vars'] . '">

                        <div id="bl_debug_var_content">
                            <div id="bl_debug_var_menu" class="bl_menu_vertical">
                                <ul>
                                    <li><a href="javascript:bl_debug_set_var(\'user\');" id="bl_debug_var_btn_user" class="bl_debug_var_btn bl_debug_var_btn_activo">user</a></li>
                                    <li><a href="javascript:bl_debug_set_var(\'special\');" id="bl_debug_var_btn_special" class="bl_debug_var_btn">special</a></li>
                                    <li><a href="javascript:bl_debug_set_var(\'functions\');" id="bl_debug_var_btn_functions" class="bl_debug_var_btn">functions</a></li>
                                    <li><a href="javascript:bl_debug_set_var(\'uclasses\');" id="bl_debug_var_btn_uclasses" class="bl_debug_var_btn">classes(user)</a></li>
                                    <li><a href="javascript:bl_debug_set_var(\'iclasses\');" id="bl_debug_var_btn_iclasses" class="bl_debug_var_btn">classes(internal)</a></li>
                                    <li><a href="javascript:bl_debug_set_var(\'constants\');" id="bl_debug_var_btn_constants" class="bl_debug_var_btn">constants</a></li>
                                    <li><a href="javascript:bl_debug_set_var(\'get\');" id="bl_debug_var_btn_get" class="bl_debug_var_btn">get</a></li>
                                    <li><a href="javascript:bl_debug_set_var(\'post\');" id="bl_debug_var_btn_post" class="bl_debug_var_btn">post</a></li>
                                    <li><a href="javascript:bl_debug_set_var(\'session\');" id="bl_debug_var_btn_session" class="bl_debug_var_btn">session</a></li>
                                    <li><a href="javascript:bl_debug_set_var(\'cookie\');" id="bl_debug_var_btn_cookie" class="bl_debug_var_btn">cookie</a></li>
                                    <li><a href="javascript:bl_debug_set_var(\'files\');" id="bl_debug_var_btn_files" class="bl_debug_var_btn">files</a></li>
                                    <li><a href="javascript:bl_debug_set_var(\'server\');" id="bl_debug_var_btn_server" class="bl_debug_var_btn">server</a></li>
                                </ul>
                            </div>

                            <div class="in10 no_top_in  bl_panel_info">
                                <div id="bl_debug_var_panels" class="bl_panel">
                                    <div class="in10 bl_debug_var_panel  bl_debug_var_panel_activo"  id="bl_debug_var_user">
                                        <div class="bl_filter_box">
                                            Filter <input id="bl_filter_user" name="filter" type="text" />
                                        </div>
                                        ' . $user . '
                                    </div>

                                    <div class="in10 bl_debug_var_panel" id="bl_debug_var_special">
                                        <div class="bl_filter_box">
                                            Filter <input id="bl_filter_special" name="filter" type="text" />
                                        </div>
                                        ' . $specials . '
                                    </div>

                                    <div class="in10 bl_debug_var_panel" id="bl_debug_var_functions">
                                        <div class="bl_filter_box">
                                            Filter <input id="bl_filter_functions" name="filter" type="text" />
                                        </div>
                                        ' . $functions . '
                                    </div>

                                    <div class="in10 bl_debug_var_panel" id="bl_debug_var_uclasses">
                                        <div class="bl_filter_box">
                                            Filter <input id="bl_filter_uclasses" name="filter" type="text" />
                                        </div>
                                        ' . $uclasses . '
                                    </div>
                                    <div class="in10 bl_debug_var_panel" id="bl_debug_var_iclasses">
                                        <div class="bl_filter_box">
                                            Filter <input id="bl_filter_iclasses" name="filter" type="text" />
                                        </div>
                                        ' . $iclasses . '
                                    </div>

                                    <div class="in10 bl_debug_var_panel" id="bl_debug_var_constants">
                                        <div class="bl_filter_box">
                                            Filter <input id="bl_filter_constants" name="filter" type="text" />
                                        </div>
                                        ' . $constants . '
                                    </div>

                                    <div class="in10 bl_debug_var_panel" id="bl_debug_var_get">
                                        <div class="bl_filter_box">
                                            Filter <input id="bl_filter_get" name="filter" type="text" />
                                        </div>
                                        ' . $get . '
                                    </div>

                                    <div class="in10 bl_debug_var_panel" id="bl_debug_var_post">
                                        <div class="bl_filter_box">
                                            Filter <input id="bl_filter_post" name="filter" type="text" />
                                        </div>
                                        ' . $post . '
                                    </div>

                                    <div class="in10 bl_debug_var_panel" id="bl_debug_var_session">
                                        <div class="bl_filter_box">
                                            Filter <input id="bl_filter_session" name="filter" type="text" />
                                        </div>
                                        ' . $session . '
                                    </div>

                                    <div class="in10 bl_debug_var_panel" id="bl_debug_var_cookie">
                                        <div class="bl_filter_box">
                                            Filter <input id="bl_filter_cookie" name="filter" type="text" />
                                        </div>
                                        ' . $cookie . '
                                    </div>

                                    <div class="in10 bl_debug_var_panel" id="bl_debug_var_files">
                                        <div class="bl_filter_box">
                                            Filter <input id="bl_filter_files" name="filter" type="text" />
                                        </div>
                                        ' . $files . '
                                    </div>

                                    <div class="in10 bl_debug_var_panel" id="bl_debug_var_server">
                                        <div class="bl_filter_box">
                                            Filter <input id="bl_filter_server" name="filter" type="text" />
                                        </div>
                                        ' . $server . '
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="bl_debug_header">
                <div id="bl_debug_menu">
                    <ul>
                        <li><a href="javascript:bl_debug_set_panel(\'msg\');" id="bl_debug_btn_msg" class="bl_debug_btn">
                            <span>' . _bl::$count_msg . ' logs ' . _BL_KEY_LOGS . '</span></a></li>
                        <li><a href="javascript:bl_debug_set_panel(\'sql\');" id="bl_debug_btn_sql" class="bl_debug_btn">
                            <span>' . _bl::$count_querys . ' Sql</span> ' . _BL_KEY_SQL . '</a></li>
                        <li><a href="javascript:bl_debug_set_panel(\'vars\');" id="bl_debug_btn_vars" class="bl_debug_btn">
                            <span>Vars</span> ' . _BL_KEY_VARS . '</a></li>
                        <li><a href="javascript:bl_debug_set_panel(\'time\');" id="bl_debug_btn_time" class="bl_debug_btn">
                            <span>' . $total_load_time . '</span> ' . _BL_KEY_TIME . '</a></li>
                        <li><a href="javascript:bl_debug_set_panel(\'memory\');" id="bl_debug_btn_memory" class="bl_debug_btn">
                            <span>' . $memory_num . '</span> ' . _BL_KEY_MEMORY . '</a></li>
                    </ul>
                </div>

                <div id="bl_debug_toggle">
                    <div id="bl_debug_toggle_buttons">
                        <a href="javascript:bl_setPanelSize(\'close\')" title="Close console">X</a>
                        <a href="javascript:bl_setPanelSize(\'plus\')" title="Open or maximize console">M</a>
                        <a href="javascript:bl_debug_set_panel(\'info\');" id="bl_debug_btn_info" title="Info Panel">about' . _BL_KEY_INFO . '</a>
                        <a href="javascript:bl_opacity();" title="Opacity">opacity' . _BL_KEY_OPACITY . '</a>
                    </div>
                </div>
            </div>

        </div>
    </div>'; // the end...

    // alert for errors
    if (_bl::$errors == true and _BL_ALERT_ERRORS == true) {
        $result .= '<div id="bl_show_errors">PHP Bug Lost: There are errors.</div>';
        $result .= '
            <script type="text/javascript">
                bl_alert_errors();
            </script>';
    }

    if ($return == true) {
        echo $result;
        return true;
    } else {
        return $result;
    }

}

// ============================
// AJAX FUNCTIONS
// ============================

if (_BL_PRODUCTION == false) {

    // ============================
    // AJAX DELETE VARS
    // ============================
    if (isset($_GET[_BL_VAR_DEL]) and _BL_DELETE_VARS == true) {

        // check secret key and IP
        if (!isset($_GET['bl_key']) or $_GET['bl_key'] != _BL_SECRET_KEY) {
            die('error-key');
        } elseif (bl_check_ip() == false) {
            die('error');
        }

        if ($_GET['type'] == '_COOKIE') {
            if (isset($_COOKIE[$_GET['var']])) {
                // delete cookie
                if (setcookie($_GET['var'], '', time() - 3600, '/')) {
                    // check if cookie is deleted.
                    if (isset($_COOKIE[$_GET['var']])) {
                        die('ok');
                    }
                    die('error-cookie');
                }
            }
        } elseif ($_GET['type'] == '_SESSION') {

            $session_id = session_id();
            if (empty($session_id)) {
                session_start();
            }

            if (isset($_SESSION[$_GET['var']])) {
                unset($_SESSION[$_GET['var']]);
                if (isset($_SESSION[$_GET['var']])) {
                    die('ok');
                }
                die('error');
            }
        }

        // problem with $_GET['type']? or $_GET['var']?
        die('error');
    }
}

