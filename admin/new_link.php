<?php

define('BASEPATH', 'Staff');
require_once('../applications/wrapper.php');

if (!$IKO->perm->check('access_administration')) {
    redirect(SITE_URL);
}//Checks if user has permission to create a thread.
//require_once('template/top.php');
echo $ADMIN->template('top');
$notice = '';

function list_category()
{
    global $MYSQL;
    $query = $MYSQL->query('SELECT * FROM {prefix}forum_category');
    $return = '';
    foreach ($query as $s) {
        $MYSQL->bind('in_category', $s['id']);
        $return .= '<option value="' . $s['id'] . '">' . $s['category_title'] . '</option>';
    }
    return $return;
}

function allowed_usergroups()
{
    global $IKO, $MYSQL;
    $query = $MYSQL->query('SELECT * FROM {prefix}usergroups');
    $return = '<input type="checkbox" name="allowed_ug[]" value="0" CHECKED id="ug_0" /> <label style="font-weight: normal;" for="ug_0">Guest</label><br />';
    foreach ($query as $u) {
        $check = (BAN_ID != $u['id']) ? ('CHECKED') : ('');
        $return .= '<input type="checkbox" name="allowed_ug[]" value="' . $u['id'] . '" id="ug_' . $u['id'] . '" ' . $check . ' /> <label style="font-weight: normal;" for="ug_' . $u['id'] . '">' . $u['group_name'] . '</label><br />';
    }
    return $return;
}

if (isset($_POST['create'])) {
    try {
        NoCSRF::check('csrf_token', $_POST);

        $title = clean($_POST['node_title']);
        $link = $_POST['link'];
        foreach ($_POST['allowed_ug'] as $ug) {
            $_POST['allowed_ug'][] = clean($ug);
        }
        $all_u = (isset($_POST['allowed_ug'])) ? implode(',', $_POST['allowed_ug']) : '0';

        if (!$title) {
            throw new Exception ('Title is required!');
        } else {
            $in_category = clean($_POST['node_parent']);
            $node_type = 3;
            $parent_node = 0;
        }
        $data = array(
            'node_name' => $title,
            'link' => $link,
            'name_friendly' => title_friendly($title),
            'in_category' => $in_category,
            'node_type' => $node_type,
            'parent_node' => $parent_node,
            'allowed_usergroups' => $all_u
        );

        try {
            $MYSQL->query('INSERT INTO {prefix}forum_node (node_name, node_desc, name_friendly, in_category, node_type, parent_node, allowed_usergroups) VALUES (:node_name, :link, :name_friendly, :in_category, :node_type, :parent_node, :allowed_usergroups)', $data);
            if (!empty($labels) && $labels[0] != "") {
                $query = $MYSQL->query("SELECT LAST_INSERT_ID(id) AS LAST_ID FROM {prefix}forum_node ORDER BY id DESC LIMIT 1");
                $node_id = $query['0']['LAST_ID'];
                foreach ($labels as $label) {
                    $MYSQL->bind('node_id', $node_id);
                    $MYSQL->bind('label', $label);
                    $MYSQL->query("INSERT INTO {prefix}labels (node_id, label) VALUES (:node_id, :label)");
                }
            }
            redirect(SITE_URL . '/admin/manage_node.php/notice/create_success');
        } catch (mysqli_sql_exception $e) {
            throw new Exception ('Error creating new link.');
        }


    } catch (Exception $e) {
        $notice .= $ADMIN->alert(
            $e->getMessage(),
            'danger'
        );
    }
}

$token = NoCSRF::generate('csrf_token');

echo $ADMIN->box(
    'Create new Link <p class="pull-right"><a href="' . SITE_URL . '/admin/manage_node.php" class="btn btn-default btn-xs">Back</a></p>',
    $notice .
    '<form action="" method="POST">
         <input type="hidden" name="csrf_token" value="' . $token . '">
         <label for="node_title">Title</label>
         <input type="text" name="node_title" id="node_title" class="form-control" />
         <label for="link">Link</label>
         <input type="text" name="link" id="link" class="form-control" />
         <label for="parent">Parent</label><br />
         <select name="node_parent" id="parent">
           ' . list_category() . '
         </select>
         <br />
         <label for="allowed_usergroups">Allowed Usergroups</label>
         <br />
         ' . allowed_usergroups() . '
         <input type="submit" name="create" value="Create Link" class="btn btn-default" />
       </form>',
    '',
    '12'
);

//require_once('template/bot.php');
echo $ADMIN->template('bot');