<?php

/*
 * Session class of IkoBB
 */
if (!defined('BASEPATH')) {
    die();
}

class Iko_Session
{

    public $isLogged = false;
    public $data, $date;
    private $session;

    public function __construct()
    {
        global $IKO, $MYSQL, $LANG;
        $this->clear();
        if ($this->check()) {
            $this->isLogged = true;
            $this->data = $IKO->user($this->session['logged_user']);
            $d_group = ($this->data['display_group'] == 0) ? $this->data['user_group'] : $this->data['display_group'];
            $this->data['username_style'] = $IKO->usergroup($d_group, 'username_style', $this->data['username']);
            if ($this->data['additional_permissions'] !== "0") {
                $current_perms = $IKO->usergroup($this->data['user_group'], 'permissions');
                foreach ($this->data['additional_permissions'] as $ap) {
                    $current_perms[] = $ap;
                }
                $this->data['permissions'] = $current_perms;
            } else {
                $this->data['permissions'] = $IKO->usergroup($this->data['user_group'], 'permissions');
            }

            if (($this->session['session_time'] >= strtotime('24 hours ago') && $this->session['session_type'] == 1) || ($this->session['session_time'] >= strtotime('365 days ago') && $this->session['session_type'] == 2)) {
                $time = time();
                $MYSQL->bind('session_id', $this->session['session_id']);
                $MYSQL->bind('session_time', $time);
                $MYSQL->query("UPDATE {prefix}sessions SET session_time = :session_time WHERE session_id = :session_id");
            }

            //Adding links for users who are logged in and everything else in the template.
            $IKO->user->addUserLink(array(
                $LANG['bb']['profile']['profile'] => SITE_URL . '/members.php/cmd/user/',
                $LANG['bb']['profile']['personal_details'] => SITE_URL . '/profile.php/cmd/edit',
                $LANG['bb']['profile']['avatar'] => SITE_URL . '/profile.php/cmd/avatar',
                $LANG['bb']['profile']['signature'] => SITE_URL . '/profile.php/cmd/signature',
                $LANG['bb']['profile']['password'] => SITE_URL . '/profile.php/cmd/password'
            ));

            //Getting user's total post/messages.
            /*$MYSQL->bind('post_user', $this->data['id']);
            $user_post_count = $MYSQL->query("SELECT * FROM {prefix}forum_posts WHERE post_user = :post_user");
            $user_post_count = number_format(count($user_post_count));

            $mod_report_integer = modReportInteger();*/

            /*$IKO->tpl->addParam(
                array(
                    'username',
                    'username_style',
                    'user_avatar',
                    'user_post_count',
                    'mod_report_integer'
                ),
                array(
                    $this->data['username'],
                    $this->data['username_style'],
                    $this->data['user_avatar'],
                    $user_post_count,
                    $mod_report_integer
                )
            );*/

            date_default_timezone_set($this->data['set_timezone']);

            $location = location($this->data['location']);
            $this->data['location'] = $location['iso'];

            /*if ($this->data['chosen_theme'] == "0") {
                $IKO->tpl->setTheme($IKO->data['site_theme']);
            } else {
                $IKO->tpl->setTheme($this->data['chosen_theme']);
            }*/
        } else {
            $this->data['permissions'] = array();
            $this->data['user_group'] = '0';
            date_default_timezone_set('US/Central');
            //$IKO->tpl->setTheme($IKO->data['site_theme']);
        }
    }

    /*
     * Check if session or cookie exists.
     */
    public function check()
    {
        global $MYSQL;

        if (isset($_SESSION['ikobb_sess']) or isset($_COOKIE['ikobb_sess'])) {
            $id = (isset($_SESSION['ikobb_sess'])) ? $_SESSION['ikobb_sess'] : $_COOKIE['ikobb_sess'];
            $MYSQL->bind('session_id', $id);
            $query = $MYSQL->query("SELECT * FROM {prefix}sessions WHERE session_id = :session_id");

            $MYSQL->bind('id', $query['0']['logged_user']);
            $user = $MYSQL->query("SELECT * FROM {prefix}users WHERE id = :id");

            if (!empty($query) && !empty($user)) {
                $this->session = $query['0'];
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /*
     * Clear expired sessions.
     */
    public function clear()
    {
        global $MYSQL;
        $time_session = strtotime(session_cache_expire() . ' minutes ago');
        $time_cookie = strtotime(IKO_SESSION_TIMEOUT . ' seconds ago');
        $query = $MYSQL->query("SELECT * FROM {prefix}sessions");
        $time_now = time();
        foreach ($query as $s) {
            if (($s['session_time'] < $time_session && (int)$s['session_type'] == 1) || ((int)$s['session_type'] !== 1 && (int)$s['session_type'] !== 2) || ($s['session_time'] < $time_cookie && (int)$s['session_type'] == 2)) {
                $MYSQL->bind('id', $s['id']);
                $MYSQL->query("DELETE FROM {prefix}sessions WHERE id = :id");
            }


        }
    }

    /*
     * Assign session to user.
     * Session Type
     *  - 1 ($_SESSION)
     *  - 2 ($_COOKIE)
     */
    public function assign($email, $remember = false, $facebook = false)
    {
        global $MYSQL;

        $MYSQL->bind('user_email', $email);
        $a = $MYSQL->query("SELECT * FROM {prefix}users WHERE user_email = :user_email");
        $MYSQL->bind('username', $email);
        $b = $MYSQL->query("SELECT * FROM {prefix}users WHERE username = :username");

        $query = ($a) ? $a : $b;

        $session_id = randomHexBytes(16);
        $time = time();

        if ($facebook) {
            setcookie('ikobb_facebook', true, time() + IKO_SESSION_TIMEOUT, '/', NULL, isset($_SERVER['HTTPS']), true);
        }

        if ($remember) {
            $MYSQL->bind('session_id', $session_id);
            $MYSQL->bind('logged_user', $query['0']['id']);
            $MYSQL->bind('session_type', 2);
            $MYSQL->bind('session_time', $time);
            $insert = $MYSQL->query("INSERT INTO {prefix}sessions (session_id, logged_user, session_type, session_time) VALUES (:session_id, :logged_user, :session_type, :session_time)");
            if ($insert > 0) {
                return setcookie('ikobb_sess', $session_id, time() + IKO_SESSION_TIMEOUT, '/', NULL, isset($_SERVER['HTTPS']), true);
            } else {
                return false;
            }

        } else {

            $MYSQL->bind('session_id', $session_id);
            $MYSQL->bind('logged_user', $query['0']['id']);
            $MYSQL->bind('session_type', 1);
            $MYSQL->bind('session_time', $time);
            $insert = $MYSQL->query("INSERT INTO {prefix}sessions (session_id, logged_user, session_type, session_time) VALUES (:session_id, :logged_user, :session_type, :session_time)");
            if ($insert > 0) {
                $_SESSION['ikobb_sess'] = $session_id;
                return true;
            } else {
                return false;
            }

        }
    }

    /*
     * Remove session to user.
     */
    public function remove()
    {
        global $MYSQL;

        if (isset($_SESSION['ikobb_sess'])) {
            $MYSQL->bind('session_id', $_SESSION['ikobb_sess']);
            $MYSQL->query("DELETE FROM {prefix}sessions WHERE session_id = :session_id");
            session_destroy();
        } else {
            $MYSQL->bind('session_id', $_COOKIE['ikobb_sess']);
            $MYSQL->query("DELETE FROM {prefix}sessions WHERE session_id = :session_id");
            return setcookie('ikobb_sess', '', time() - 3600, '/', NULL, isset($_SERVER['HTTPS']), true);
        }
    }

}

?>
