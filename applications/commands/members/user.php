<?php

/*
 * User Profile module for IkoBB
 * Everything that you want to display MUST be in the $content variable.
 */

if (!defined('BASEPATH')) {
    die();
}
$content = '';
$page_title = '';
$query = null;

if ($PGET->g('id')) {
    $id = clean($PGET->g('id'));
    $MYSQL->bind('id', $id);
    $query = $MYSQL->query("SELECT * FROM {prefix}users WHERE id = :id");

    $MYSQL->bind('username', $id);
    $u_query = $MYSQL->query("SELECT * FROM {prefix}users WHERE username = :username");

    $query = (empty($query)) ? $u_query : $query;

    if (!empty($query)) {

        $page_title .= $LANG['bb']['members']['profile_of'] . ' ' . $query['0']['username'];
        $userg = $IKO->usergroup($query['0']['user_group']);
        $user = $IKO->user($id);

        if ($IKO->sess->isLogged && $IKO->sess->data['id'] != $user['id']) {
            // Inserting a new visitor
            $MYSQL->bindMore(array(
                'profile_owner' => $user['id'],
                'visitor' => $IKO->sess->data['id']
            ));
            $query = $MYSQL->query('SELECT * FROM  {prefix}user_visitors WHERE profile_owner = :profile_owner AND visitor = :visitor');
            if (empty($query)) {
                $MYSQL->bindMore(array(
                    'profile_owner' => $user['id'],
                    'visitor' => $IKO->sess->data['id']
                ));
                try {
                    $MYSQL->query('INSERT INTO {prefix}user_visitors (profile_owner, visitor, timestamp) VALUES (:profile_owner, :visitor, UNIX_TIMESTAMP(NOW()))');
                } catch (mysqli_sql_exception $e) {
                    throw new Exception ($LANG['global_form_process']['error_creating_thread']);
                }
            } else {
                $MYSQL->bindMore(array(
                    'profile_owner' => $user['id'],
                    'visitor' => $IKO->sess->data['id']
                ));
                try {
                    $MYSQL->query('UPDATE {prefix}user_visitors SET timestamp = UNIX_TIMESTAMP(NOW()) WHERE profile_owner = :profile_owner AND visitor = :visitor');
                } catch (mysqli_sql_exception $e) {
                    throw new Exception ($LANG['global_form_process']['error_creating_thread']);
                }

            }

        }
    }
} else {
    if ($IKO->sess->isLogged) {
        $page_title .= $LANG['bb']['members']['profile_of'] . ' ' . $IKO->sess->data['username'];
        $userg = $IKO->usergroup($IKO->sess->data['user_group']);
        $user = $IKO->user($IKO->sess->data['id']);
        $query['0'] = $user;
    } else {
        redirect(SITE_URL . '/404.php');
    }
}

if (isset($user) && isset($userg) && isset($page_title)) {

    if (isset($_POST['comment_submit'])) {
        $comment_insert = clean($_POST['comment']);
        $MYSQL->bind('comment', $comment_insert);
        $MYSQL->bind('writer', $IKO->sess->data['id']);
        $MYSQL->bind('profile_owner', $user['id']);
        $MYSQL->query("INSERT INTO {prefix}user_comments (comment, writer, profile_owner, timestamp) VALUES (:comment, :writer, :profile_owner, UNIX_TIMESTAMP(NOW()))");
    }

    //Recent activity
    $recent_activity = $IKO->user->recent_activity($query['0']['id']);

    //Moderation tools
    $mod_tools = '';
    if ($IKO->perm->check('access_moderation')) {
        if ($user['is_banned'] == "1") {
            $mod_tools .= $IKO->tpl->entity(
                'mod_tools_profile',
                array(
                    'ban_user',
                    'ban_user_url'
                ),
                array(
                    'Unban User',
                    SITE_URL . '/mod/unban.php/id/' . $id
                ),
                'buttons'
            );
        } else {
            $mod_tools .= $IKO->tpl->entity(
                'mod_tools_profile',
                array(
                    'ban_user',
                    'ban_user_url'
                ),
                array(
                    'Ban User',
                    SITE_URL . '/mod/ban.php/id/' . $id
                ),
                'buttons'
            );
        }
    }

    //profile comments
    $comments = '';
    $MYSQL->bind('profile_owner', $user['id']);
    $query = $MYSQL->query("SELECT writer,comment,timestamp FROM {prefix}user_comments WHERE profile_owner = :profile_owner ORDER BY timestamp DESC LIMIT 10");
    foreach ($query as $entry) {

        $writer = $IKO->user($entry['writer']);
        $comment = $IKO->lib_parse->parse($entry['comment']);
        $date_temp = simplify_time($entry['timestamp']);
        $date = $date_temp['time'];
        $comments .= $IKO->tpl->entity(
            'user_profile_comments',
            array(
                'writer',
                'comment',
                'date',
                'avatar',
                'profile_url'
            ),
            array(
                '<a href="' . SITE_URL . '/members.php/cmd/user/id/' . $writer['id'] . '">' . $writer['username_style'] . '</a>',
                $comment,
                $date,
                $writer['user_avatar'],
                SITE_URL . '/members.php/cmd/user/id/' . $user['id']
            )
        );
    }
    $form = '';
    if ($IKO->sess->isLogged) {
        $form = $IKO->tpl->entity('user_profile_comments_form', 'comments_action', '');
    }

    //profile visitors
    $visitors = '<div><ul class="visitors_framed">';
    $MYSQL->bind('profile_owner', $user['id']);
    $query = $MYSQL->query("SELECT visitor FROM {prefix}user_visitors WHERE profile_owner = :profile_owner ORDER BY timestamp DESC LIMIT 10");
    foreach ($query as $entry) {
        $visitor = $IKO->user($entry['visitor']);
        $visitors .= '<li><a href="' . SITE_URL . '/members.php/cmd/user/id/' . $visitor['id'] . '" title="' . $visitor['username'] . '"><img src="' . $visitor['user_avatar'] . '" class="img-thumbnail" style="width:45px;height:45px;" /></a></li>';
    }
    $visitors .= '</ul></div>';


    //Breadcrumbs
    $IKO->tpl->addBreadcrumb(
        $LANG['bb']['forum'],
        SITE_URL . '/forum.php'
    );
    $IKO->tpl->addBreadcrumb(
        $LANG['bb']['members']['home'],
        SITE_URL . '/members.php'
    );
    $IKO->tpl->addBreadcrumb(
        $LANG['bb']['members']['profile_of'] . ' ' . $user['username'],
        '#',
        true
    );
    $content .= $IKO->tpl->breadcrumbs();

    //user profile
    $content .= $IKO->tpl->entity(
        'user_profile_page',
        array(
            'username',
            'user_avatar',
            'usergroup',
            'registered_date',
            'user_signature',
            'about_user',
            'location',
            'flag',
            'gender',
            'age',
            'recent_activity',
            'mod_tools',
            'visitors',
            'comments',
            'form'
        ),
        array(
            $user['username_style'],
            $user['user_avatar'],
            $userg['group_name'],
            localized_date($user['date_joined'], @$IKO->sess->data['location']),
            $IKO->lib_parse->parse($user['user_signature']),
            $IKO->lib_parse->parse($user['about_user']),
            $LANG['location'][$user['location']],
            '<span class="flag-icon flag-icon-' . strtolower($user['location']) . '"></span>',
            gender($user['gender']),
            birthday_to_age($user['user_birthday']),
            $recent_activity,
            $mod_tools,
            $visitors,
            $comments,
            $form
        )
    );

}

?>
