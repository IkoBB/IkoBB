<?php

/*
 * Standard Functions of IkoBB.
 */
if (!defined('BASEPATH')) {
    die();
}

/**
 * Conversion
 * @author N8boy
 * @param $bytes
 * @param int $precision
 * @return string
 */
function bytesToSize($bytes, $precision = 2)
{
    $kilobyte = 1024;
    $megabyte = $kilobyte * 1024;
    $gigabyte = $megabyte * 1024;
    $terabyte = $gigabyte * 1024;

    if (($bytes >= 0) && ($bytes < $kilobyte)) {
        return $bytes . ' B';
    } elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
        return round($bytes / $kilobyte, $precision) . ' KB';
    } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
        return round($bytes / $megabyte, $precision) . ' MB';
    } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
        return round($bytes / $gigabyte, $precision) . ' GB';
    } elseif ($bytes >= $terabyte) {
        return round($bytes / $terabyte, $precision) . ' TB';
    } else {
        return $bytes . ' B';
    }
}

/**
 * Counts the number of threads
 * @author
 * @return int
 */
function stat_threads()
{
    global $MYSQL;
    $query = $MYSQL->query("SELECT id FROM {prefix}forum_posts WHERE post_type = 1");
    return count($query);
}

/**
 * Counts the number of posts
 * @return int
 */
function stat_posts()
{
    global $MYSQL;
    $query = $MYSQL->query("SELECT id FROM {prefix}forum_posts WHERE post_type = 2");
    return count($query);
}

function stat_users()
{
    global $MYSQL;
    $query = $MYSQL->query("SELECT id FROM {prefix}users");
    return count($query);
}

/**
 * Who was online in the last 10 minutes?
 * @return string
 */
function users_online()
{
    global $MYSQL, $IKO;
    $time = time();
    $query = $MYSQL->query("SELECT * FROM {prefix}sessions ORDER BY session_time DESC");
    $users = array();
    foreach ($query as $u) {
        $session_time = strtotime("+10 minutes", $u['session_time']);
        if ($time <= $session_time) {
            if (!in_array($u['logged_user'], $users)) {
                $users[] = $u['logged_user'];
            }
        }
    }

    $total = array();
    foreach ($users as $u) {
        $us = $IKO->user($u);
        $total[] = '<a href="' . SITE_URL . '/members.php/cmd/user/id/' . $us['id'] . '">' . $us['username_style'] . '</a>';
    }
    if (!empty($total)) {
        return implode(', ', $total);
    } else {
        return 'None';
    }
}

/**
 * List themes for theme changer.
 * @return array
 * @array
 * - int id
 * - string change_link
 * - string theme_name
 */
function listThemes()
{
    global $MYSQL;
    $query = $MYSQL->query("SELECT * FROM {prefix}themes");
    $return = array();
    foreach ($query as $t) {
        $return[] = array(
            'id' => $t['id'],
            'change_link' => SITE_URL . '/profile.php/cmd/theme/set/' . $t['id'],
            'theme_name' => $t['theme_name']
        );
    }
    $return[] = array(
        'id' => 0,
        'change_link' => SITE_URL . '/profile.php/cmd/theme/set/default',
        'theme_name' => 'Default'
    );
    return $return;
}

/**
 * Cleans string.
 * Does not escape with MySQL because the MySQL Library already does that
 * @param $string
 * @return mixed|string
 */
function clean($string)
{
    $string = htmlentities($string);
    $string = str_replace(
        array(
            '&amp;#65279;',
            '`'
        ),
        array(
            '',
            '&#96;'
        ),
        $string
    );
    $string = str_replace('`', '\`', $string);
    return $string;
}

/**
 * Redirects to given url
 * @param $url
 */
function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

/**
 * Generate hex-encoded pseudo-random bytes.
 *
 * The function first tries to read from a secure randomness source. If neither the
 * OpenSSL extension nor the Mcrypt extension nor direct access to /dev/urandom is
 * available, it falls back to mt_rand().
 * @param $length
 * @return string
 */
function randomHexBytes($length)
{
    $raw_bytes = '';

    if (function_exists('openssl_random_pseudo_bytes')) {
        $raw_bytes = openssl_random_pseudo_bytes($length);
    } elseif (function_exists('mcrypt_create_iv')) {
        $raw_bytes = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
    } else {
        $urandom = @fopen('/dev/urandom', 'rb');

        if (is_resource($urandom)) {
            $raw_bytes = fread($urandom, $length);
            fclose($urandom);
        }
    }

    if (!is_string($raw_bytes) || strlen($raw_bytes) < $length) {
        for ($byte_index = 0; $byte_index < $length; $byte_index++) {
            $raw_bytes .= chr(mt_rand(0, 255));
        }
    }

    return bin2hex($raw_bytes);
}

/**
 * @param int $length
 * @return string
 */
function randomString($length = 16)
{
    trigger_error('The function randomString() is deprecated. Use randomHexBytes() instead.', E_USER_WARNING);

    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

/**
 * @param $string
 * @return string
 */
function title_friendly($string)
{
    return strtolower(preg_replace("![^a-z0-9]+!i", "_", $string));
}

/**
 * Password Encryption
 * @param $password
 * @return bool|false|string
 */
function encrypt($password)
{
    return password_hash($password, PASSWORD_BCRYPT, array('cost' => USER_PASSWORD_HASH_COST));
}

/**
 * Moderator Functions
 * @return int
 */
function modReportInteger()
{
    global $MYSQL;
    $query = $MYSQL->query("SELECT id FROM {prefix}reports");

    return count($query);
}

/**
 * Check if username exists
 * @param $username
 * @return bool
 */
function usernameExists($username)
{
    global $MYSQL;
    $MYSQL->bind('username', $username);
    $query = $MYSQL->query('SELECT * FROM {prefix}users WHERE username = :username');

    if (!empty($query)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Check if email exists
 * @param $email
 * @return bool
 */
function emailTaken($email)
{
    global $MYSQL;
    $MYSQL->bind('user_email', $email);
    $query = $MYSQL->query('SELECT * FROM {prefix}users WHERE user_email = :user_email');

    if (!empty($query)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Check if email is valid
 * @param $email
 * @return bool
 */
function validEmail($email)
{
    if (preg_match('/^[a-z0-9_\.-]+@([a-z0-9]+([\-]+[a-z0-9]+)*\.)+[a-z]{2,7}$/i', $email)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Check if user is banned
 * @param $email
 * @return bool
 */
function userBanned($email)
{
    global $MYSQL;
    $MYSQL->bind('user_email', $email);
    $a = $MYSQL->query("SELECT unban_time, ban_reason, is_banned FROM {prefix}users WHERE user_email = :user_email");
    $MYSQL->bind('username', $email);
    $b = $MYSQL->query("SELECT unban_time, ban_reason, is_banned FROM {prefix}users WHERE username = :username");

    $a = ($a) ? $a : $b;

    $return['unban_time'] = $a['0']['unban_time'];
    $return['ban_reason'] = $a['0']['ban_reason'];

    if ($a['0']['is_banned'] == "1") {
        return $return;
    } else {
        return false;
    }
}

/**
 * Check if user is activated
 * @param $email
 * @return bool
 * ToDo: Why is this here? Should be in user class or not?
 */
function userActivated($email)
{
    global $MYSQL;
    $MYSQL->bind('user_email', $email);
    $a = $MYSQL->query("SELECT user_disabled FROM {prefix}users WHERE user_email = :user_email");
    $MYSQL->bind('username', $email);
    $b = $MYSQL->query("SELECT user_disabled FROM {prefix}users WHERE username = :username");

    $a = ($a) ? $a : $b;

    if ($a['0']['user_disabled'] == "0") {
        return true;
    } else {
        return false;
    }
}

/**
 * Check if user exists / Login
 * @param $email
 * @param $password
 * @param bool $rehash_if_necessary
 * @return bool
 */
function userExists($email, $password, $rehash_if_necessary = true)
{
    global $MYSQL;

    $login_successful = false;
    $MYSQL->bind('user_email', $email);
    $a = $MYSQL->query("SELECT * FROM {prefix}users WHERE user_email = :user_email");

    $MYSQL->bind('username', $email);
    $b = $MYSQL->query("SELECT * FROM {prefix}users WHERE username = :username");
    if ($a or $b) {
        $user_data = ($a) ? $a[0] : $b[0];
        $hash = $user_data['user_password'];

        /*
         * There are two types of hashes: insecure legacy hashes based on SHA-256
         * and the new bcrypt hashes.
         *
         * The legacy hashes need to be replaced with bcrypt hashes after the
         * password has been verified. The bcrypt hashes need to be refreshed
         * in case the cost factor has changed.
         */
        $obsolete_hash = false;

        if (substr($hash, 0, 4) === '$SHA') {
            list(, , $salt) = explode('$', $hash);

            $hash_to_test =
                '$SHA$' . $salt . '$' . hash('sha256', hash('sha256', $password) . hash('sha256', $salt));

            $login_successful = strcasecmp($hash_to_test, $hash) === 0;

            $obsolete_hash = $rehash_if_necessary;
        } else {
            $login_successful = password_verify($password, $hash);

            $obsolete_hash = $rehash_if_necessary
                && password_needs_rehash($hash, PASSWORD_BCRYPT, array('cost' => USER_PASSWORD_HASH_COST));
        }

        if ($login_successful && $obsolete_hash) {
            $MYSQL->bind('id', $user_data['id']);
            $MYSQL->bind('user_password', encrypt($password));
            $MYSQL->query('UPDATE {prefix}users SET user_password = :user_password');

        }
    }

    return $login_successful;
}

/**
 * Check if usergroupExists
 * @param $name
 * @return bool
 */
function usergroupExists($name)
{
    global $MYSQL;
    $MYSQL->bind('group_name', $name);
    $query = $MYSQL->query('SELECT * FROM {prefix}usergroups WHERE group_name = :group_name');
    if (!empty($query)) {
        return $query['0'];
    } else {
        return false;
    }
}

/**
 * Get details for a thread
 * @param $id
 * @param null $callback
 * @return mixed
 */
function thread($id, $callback = null)
{
    global $MYSQL;
    $MYSQL->bind('id', $id);
    $query = $MYSQL->query("SELECT * FROM {prefix}forum_posts WHERE id = :id");

    if (is_callable($callback)) {
        call_user_func($callback, $query['0']);
    } else {
        return $query['0'];
    }
}

/**
 * @param $id
 * @param null $callback
 * @return mixed
 */
function node($id, $callback = null)
{
    global $MYSQL;
    $MYSQL->bind('id', $id);
    $query = $MYSQL->query("SELECT * FROM {prefix}forum_node WHERE id = :id");

    if (is_callable($callback)) {
        call_user_func($callback, $query['0']);
    } else {
        return $query['0'];
    }
}

/**
 * @param $id
 * @return mixed
 */
function category($id)
{
    global $MYSQL;
    $MYSQL->bind('id', $id);
    $query = $MYSQL->query("SELECT * FROM {prefix}forum_category WHERE id = :id");
    return $query['0'];
}

/**
 * Delete a folder with contents in it
 * @param $dir
 * @return bool
 */
function rrmdir($dir)
{
    foreach (glob($dir . '/*') as $file) {
        if (is_dir($file)) rrmdir($file); else unlink($file);
    }
    if (rmdir($dir)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Include all installed extensions
 */
function include_extensions()
{
    global $MYSQL;
    $query = $MYSQL->query("SELECT * FROM {prefix}extensions");
    foreach ($query as $extension) {
        require_once('extensions/' . $extension['extension_folder'] . '/manifest.php');
    }
}

/**
 * Forum Listings
 * @return array
 */
function list_forums()
{
    global $MYSQL;
    $return = array();
    $query = $MYSQL->query("SELECT * FROM {prefix}forum_node");
    foreach ($query as $node) {
        $return[] = array(
            'id' => $node['id'],
            'name' => $node['node_name'],
            'parent_node' => $node['parent_node']
        );
    }
    return $return;
}

/**
 * Get location data from location ID
 * @author N8boy
 * @return array;
 * @array
 *  - iso
 *  - language
 */

function location($location_id = 233)
{
    global $MYSQL;

    if (is_int($location_id)) {
        try {
            $MYSQL->bind('id', $location_id);
            $query = $MYSQL->query("SELECT iso, language FROM {prefix}countries WHERE id = :id");
            return $query['0'];
        } catch (PDOException $e) {
            $this->ExceptionLog($e->getMessage());
            return false;
        }
    } else {
        return false;
    }


}

/**
 * Features to tell time.
 * @author N8boy
 * @return array;
 * @array
 *  - tooltip
 *  - time
 */
function simplify_time($timestamp, $location = 'EN')
{
    global $LANG;
    if ((time() - $timestamp) >= 86400 && (time() - $timestamp) < 604800) {
        $post_time = date('l h:i A', $timestamp); // Sunday 12:00 AM
        $post_time_tooltip = localized_date($timestamp, $location); // January 1st, 2014
    } elseif ((time() - $timestamp) >= 7200 && (time() - $timestamp) < 86400) {
        $post_time = str_replace(
            '%time%',
            round((time() - $timestamp) / 3600),
            $LANG['time']['hours_ago']
        );
        $post_time_tooltip = localized_date($timestamp, $location);
    } elseif ((time() - $timestamp) > 3600 && (time() - $timestamp) < 7200) {
        $post_time = str_replace(
            '%time%',
            round((time() - $timestamp) / 3600),
            $LANG['time']['hour_ago']
        );
        $post_time_tooltip = localized_date($timestamp, $location);
    } elseif ((time() - $timestamp) >= 120 && (time() - $timestamp) < 3600) {
        $time = round((time() - $timestamp) / 60);
        $post_time = str_replace(
            '%time%',
            $time,
            $LANG['time']['minutes_ago']
        );
        $post_time_tooltip = localized_date($timestamp, $location);
    } elseif ((time() - $timestamp) >= 60 && (time() - $timestamp) < 120) {
        $time = round((time() - $timestamp) / 60);
        $post_time = str_replace(
            '%time%',
            $time,
            $LANG['time']['minute_ago']
        );
        $post_time_tooltip = localized_date($timestamp, $location);
    } elseif ((time() - $timestamp) < 60) {
        $post_time = $LANG['time']['just_now'];
        $post_time_tooltip = localized_date($timestamp, $location);
    } else {
        $post_time = localized_date($timestamp, $location);
        $post_time_tooltip = date('l h:i A', $timestamp);
    }

    $return = array(
        'tooltip' => $post_time_tooltip,
        'time' => $post_time
    );
    return $return;
}

/**
 * Function which counts the replies of a conversation
 * @author N8boy
 * @param $origin_massage_id
 * @return string
 */
function amount_replies($origin_massage_id)
{
    global $MYSQL;
    if (is_numeric($origin_massage_id)) {
        $MYSQL->bind('origin_message', $origin_massage_id);
        $query = $MYSQL->query("SELECT * FROM {prefix}messages WHERE origin_message = :origin_message");
        return number_format(count($query));
    }
}

/**
 * Transforms an input int (1 or 2) to male and female symbols
 * @param $in
 * @return string
 */
function gender($in)
{
    if ($in == 1) {
        $out = '&#9792;';
    } elseif ($in == 2) {
        $out = '&#9794;';
    } else {
        $out = '';
    }
    return $out;
}

/**
 * Transforms a date in a age
 * @param $date
 * @return false|string
 */
function birthday_to_age($date)
{
    $year = substr($date, 0, 4);
    $month = substr($date, 5, 2);
    $day = substr($date, 8, 2);

    $cur_year = date("Y");
    $cur_month = date("m");
    $cur_day = date("d");

    $calc_year = $cur_year - $year;
    if ($month > $cur_month)
        return $calc_year - 1;
    elseif ($month == $cur_month && $day > $cur_day)
        return $calc_year - 1;
    else
        return $calc_year;
}

/**
 * Shows the date according location
 * @param $date
 * @param string $location
 * @return string
 */
function localized_date($date, $location = 'EN')
{

    global $LANG;
    if (is_numeric($date) === false)
        $date = strtotime($date);

    $day = date("j", $date);
    $month = date("n", $date);
    $year = date("Y", $date);
    $location = strtoupper($location);
    if ($location == 'DE' || 'AT')
        return $day . '. ' . $LANG['date']['month_' . $month] . ' ' . $year;
    else
        return $LANG['date']['month_' . $month] . ' ' . $day . ', ' . $year;
}

/**
 * @param $input
 * @return mixed
 */
function nl2brPre($input)
{
    $input = preg_replace('%\n%i', '<br/>', $input);
    preg_match_all('%<pre\s*[^>]*>.+?</pre>%i', $input, $a);
    for ($i = 0; $i < sizeof($a); $i++) {
        $input = str_replace($a[$i], str_replace("<br/>", "\n", $a[$i]), $input);
    }
    return $input;
}

/**
 * Transforms a emoji to a text
 * @param $input
 * @return mixed|string
 */
function emoji_to_text($input)
{
    global $ICONS;
    $clean = mb_convert_encoding($input, 'HTML-ENTITIES', 'UTF-8');
    foreach ($ICONS as $var1 => $var2) {
        foreach ($var2 as $code => $translation) {
            $clean = str_replace($translation, $code, $clean);
        }
    }
    return $clean;
}