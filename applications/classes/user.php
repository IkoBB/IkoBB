<?php

/*
 * User class of IkoBB
 */
if (!defined('BASEPATH')) {
    die();
}

class Iko_User
{

    private $user_links = array();
    private $notice_type;

    public function __construct()
    {
        global $LANG;
        $this->notice_type = array(
            'mention',
            'reply',
            'quote',
            'pm'
        );
    }

    /**
     * Change user's usergroup
     * @param $user
     * @param $group
     * @return bool
     */
    public function changeUserGroup($user, $group)
    {
        global $MYSQL;
        $MYSQL->bind('id', $user);
        $user = $MYSQL->query("SELECT * FROM {prefix}users WHERE id = :id");
        $MYSQL->bind('id', $group);
        $group = $MYSQL->query("SELECT * FROM {prefix}usergroups WHERE id = :id");

        if (!empty($user) && !empty($group)) {

            $MYSQL->bind('user_group', $group['0']['id']);
            $MYSQL->bind('id', $user);
            $query = $MYSQL->query("INSERT INTO {prefix}users SET user_group :user_group WHERE id = :id");

            if ($query > 0) {
                return true;
            } else {
                return false;
            }


            // PDO here or shall this be like this?

            /*$data = array(
              'user_group' => $group
            );*/

            //$MYSQL->where('id', $user);

            /*try {
              $MYSQL->update('{prefix}users', $data);
              return true;
            } catch (mysqli_sql_exception $e) {
              return false;
            }*/

        } else {
            return false;
        }
    }

    /**
     * Change Username
     * @param $user
     * @param $username
     * @return bool
     */
    public function changeUsername($user, $username)
    {
        global $MYSQL;
        $MYSQL->bind('id', $user);
        $query = $MYSQL->query('SELECT * FROM {prefix}users WHERE id = :id');
        if (!empty($query)) {

            $MYSQL->bind('username', $username);
            $MYSQL->bind('id', $user);
            try {
                $MYSQL->query('UPDATE {prefix}users SET username = :username WHERE id = :id');
                return true;
            } catch (mysqli_sql_exception $e) {
                return false;
            }

        } else {
            return false;
        }
    }

    /**
     * Add permission to a user
     * @param $user
     * @param $permission
     * @return bool
     */
    public function givePermission($user, $permission)
    {
        global $MYSQL, $IKO;
        $user = $IKO->user($user);
        if (!empty($user)) {
            $perm = $IKO->perm->perm($permission);
            if ($user['additional_permissions'] == "0") {
                $MYSQL->bind('additional_permissions', $perm['permission_name']);
            } else {
                $ap_array = array();
                foreach ($user['additional_permissions'] as $ap) {
                    $MYSQL->bind('permission_name', $ap);
                    $ap_query = $MYSQL->query('SELECT * FROM {prefix}permissions WHERE permission_name = :permission_name');
                    if ($ap_query) {
                        $ap_array[] = $ap_query['0']['id'];
                    }
                }
                $additional_permissions = implode(',', $ap_array);
                $MYSQL->bind('additional_permissions', $additional_permissions . ',' . $perm['permission_name']);
            }
            $MYSQL->bind('id', $user['id']);
            if ($MYSQL->query('UPDATE {prefix}users SET additional_permissions = :additional_permissions WHERE id = :id')) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Remove additional permission from a user
     * @param $user
     * @param $permission
     * @return bool
     */
    public function removeAddPermission($user, $permission)
    {
        global $MYSQL, $IKO;
        $user = $IKO->user($user);
        if (!empty($user)) {
            $current_perms = array();
            foreach ($user['additional_permissions'] as $ap) {
                $current_perms[$ap] = $ap;
            }
            unset($current_perms[$permission]);
            if (!empty($current_perms)) {
                $new_perms = array();
                foreach ($current_perms as $parent => $child) {
                    $MYSQL->bind('permission_name', $child); //$id_perms not defined?
                    $p_query = $MYSQL->query('SELECT * FROM {prefix}permissions WHERE permission_name = :permission_name');
                    $new_perms[] = $p_query['id'];
                }
                $new_perms = implode(',', $new_perms);
                $MYSQL->bind('additional_permissions', $new_perms);
            } else {
                $MYSQL->bind('additional_permissions', '0');
            }

            $MYSQL->bind('id', $user['id']);
            if ($MYSQL->query('UPDATE {prefix}users SET additional_permissions = :additional_permissions WHERE id = :id')) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Return user links as an array
     * For template use
     * @return array
     */
    function userLinks()
    {
        return $this->user_links;
    }

    /**
     * Add link to the user links
     * @param array $link
     */
    public function addUserLink($link = array())
    {
        foreach ($link as $name => $href) {
            $this->user_links[$name] = $href;
        }
    }

    /**
     * User messages
     * @return array
     */
    public function userMessages()
    {
        global $MYSQL, $IKO;
        $return = array();
        $MYSQL->bind('message_receiver', $IKO->sess->data['id']);
        $MYSQL->bind('receiver_viewed', 0);
        $query = $MYSQL->query("SELECT * FROM {prefix}messages WHERE message_receiver = :message_receiver AND receiver_viewed = :receiver_viewed");
        foreach ($query as $msg) {
            if ($msg['message_type'] == 1) {
                $receiver = $IKO->user($msg['message_receiver']);
                $sender = $IKO->user($msg['message_sender']);
                $msg['message_receiver'] = $receiver['username'];
                $msg['message_sender'] = $sender['username'];
                $msg['view_url'] = SITE_URL . '/conversations.php/cmd/view/v/' . $msg['id'];
                $return[] = $msg;
            } else {
                $MYSQL->bind('id', $msg['origin_message']);
                $origin = $MYSQL->query('SELECT * FROM {prefix}messages WHERE id = :id');
                $receiver = $IKO->user($msg['message_receiver']);
                $sender = $IKO->user($msg['message_sender']);
                $msg['message_receiver'] = $receiver['username'];
                $msg['message_sender'] = $sender['username'];
                $msg['view_url'] = SITE_URL . '/conversations.php/cmd/view/v/' . $origin['0']['id'];
                $return[] = $msg;
            }
        }
        return $return;
    }

    /**
     * Notification
     * @return array
     */
    public function notifications()
    {
        global $MYSQL, $IKO;
        $return = array();
        if ($IKO->sess->isLogged) {
            $query = $MYSQL->query("SELECT * FROM {prefix}notifications WHERE user = {$IKO->sess->data['id']} AND viewed = 0 ORDER BY time_received ASC");
            foreach ($query as $note) {
                $note['notice_link'] = ($query['0']['notice_link'] == "0") ? '#' : $query['0']['notice_link'];
                $return[] = $note;
            }
        } else {
            unset($return);
        }
        return $return;
    }

    /**
     * Delete all notifications of a user
     */
    public function clearNotification()
    {
        global $MYSQL, $IKO;
        $MYSQL->bind('user', $IKO->sess->data['id']);
        $MYSQL->query("UPDATE {prefix}notifications SET viewed = 1 WHERE user = :user");
    }

    /**
     * Notify a user
     * @param $type
     * @param $user
     * @param bool $email
     * @param array $extra
     * @return bool
     * @throws Exception
     */
    public function notifyUser($type, $user, $email = false, $extra = array())
    {
        global $MYSQL, $IKO, $LANG, $MAIL;
        $time = time();
        $notice = '';
        if (in_array($type, $this->notice_type)) {
            switch ($type) {
                //Mention notification.
                case "mention":
                    $notice = str_replace(
                        '%username%',
                        $extra['username'],
                        $LANG['notification']['mention']
                    );

                    $MYSQL->bindMore(
                        array(
                            'notice_content' => $notice,
                            'notice_link' => $extra['link'],
                            'user' => $user,
                            'time_received' => $time
                        )
                    );
                    break;

                //Reply notification
                case "reply":
                    $notice = str_replace(
                        array(
                            '%username%',
                            '%thread_title%'
                        ),
                        array(
                            $extra['username'],
                            $extra['thread_title']
                        ),
                        $LANG['notification']['reply']
                    );

                    $MYSQL->bindMore(
                        array(
                            'notice_content' => $notice,
                            'notice_link' => $extra['link'],
                            'user' => $user,
                            'time_received' => $time
                        )
                    );
                    break;

                //Quote notification.
                case "quote":
                    $notice = str_replace(
                        array(
                            '%username%',
                            '%thread_title%'
                        ),
                        array(
                            $extra['username'],
                            $extra['thread_title']
                        ),
                        $LANG['notification']['quoted']
                    );

                    $MYSQL->bindMore(
                        array(
                            'notice_content' => $notice,
                            'notice_link' => $extra['link'],
                            'user' => $user,
                            'time_received' => $time
                        )
                    );
                    break;


                // New private message
                case "pm":
                    $notice = str_replace(
                        array(
                            '%username%',
                            '%message_title%'
                        ),
                        array(
                            $extra['username'],
                            $extra['message_title']
                        ),
                        $LANG['notification']['pm']
                    );

                    $MYSQL->bindMore(
                        array(
                            'notice_content' => $notice,
                            'notice_link' => $extra['link'],
                            'user' => $user,
                            'time_received' => $time
                        )
                    );
                    break;
            }
        } else {
            //Uncategorized notification.
            $link = (isset($extra['link'])) ? $extra['link'] : '';
            $extra['link'] = $link;
            $notice .= $type;

            $MYSQL->bindMore(
                array(
                    'notice_content' => $notice,
                    'notice_link' => $link,
                    'user' => $user,
                    'time_received' => $time
                )
            );
        }

        try {
            $MYSQL->query("INSERT INTO {prefix}notifications (notice_content, notice_link, user, time_received) VALUES (:notice_content, :notice_link, :user, :time_received)");
            $info = str_replace(
                '%url%',
                $extra['link'],
                $LANG['email']['notify']['more_info']
            );
            if ($email) {
                $user = $IKO->user($user);
                //Setting up email
                $MAIL->to($user['user_email']);
                $MAIL->from($IKO->data['site_email']);
                $MAIL->subject($notice);
                $MAIL->body($notice . $info);
                if ($MAIL->send()) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        } catch (mysqli_sql_exception $e) {
            throw new Exception ('FAIL: ' . $e);
        }
        $notice .= $IKO->tpl->entity(
            'danger_notice',
            'content',
            $e->getMessage()
        );
    }

    function recent_activity($user)
    {
        global $MYSQL, $IKO, $LANG;
        $return = '';
        if (isset($user)) {
            $MYSQL->bind('post_user', $user);
            $query = $MYSQL->query("SELECT origin_node, post_type, title_friendly, post_title, post_time, origin_thread FROM {prefix}forum_posts WHERE post_user = :post_user ORDER BY post_time DESC LIMIT 15");
            foreach ($query as $activity) {
                $MYSQL->bind('id', $activity['origin_node']);
                $parent_node = $MYSQL->query("SELECT allowed_usergroups FROM {prefix}forum_node WHERE id = :id");
                $allowed = explode(',', $parent_node['0']['allowed_usergroups']);
                if (in_array($IKO->sess->data['user_group'], $allowed)) {
                    //User created thread
                    if ($activity['post_type'] == "1") {

                        $return .= str_replace(
                            array(
                                '%url%',
                                '%title%',
                                '%date%'
                            ),
                            array(
                                SITE_URL . '/thread.php/' . $activity['title_friendly'] . '.' . $activity['id'],
                                $activity['post_title'],
                                date('F j, Y', $activity['post_time'])
                            ),
                            $LANG['bb']['members']['posted_thread']
                        );
                    } else {
                        //User replied to thread
                        $thread = thread($activity['origin_thread']);
                        $return .= str_replace(
                            array(
                                '%url%',
                                '%title%',
                                '%date%'
                            ),
                            array(
                                SITE_URL . '/thread.php/' . $thread['title_friendly'] . '.' . $thread['id'] . '#post-' . $thread['id'],
                                $thread['post_title'],
                                date('F j, Y', $activity['post_time'])
                            ),
                            $LANG['bb']['members']['replied_to']
                        );
                    }
                }
            }
        }
        return $return;
    }


}

?>
