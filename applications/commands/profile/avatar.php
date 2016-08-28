<?php

/*
 * Profile edit module for IkoBB.
 */
if (!defined('BASEPATH')) {
    die();
}
if (!$IKO->sess->isLogged) {
    redirect(SITE_URL . '/404.php');
}//Check if user is logged in.

$page_title = $LANG['bb']['profile']['avatar'];
$content = '';
$notice = '';

if (isset($_POST['edit'])) {

    try {

        $mime = array(
            'image/png',
            'image/jpeg',
            'image/jpg',
            'image/gif'
        );

        $avatar = (isset($_POST['avatar'])) ? $_POST['avatar'] : '';

        if ($avatar == 1) { // Using Gravatar
            $MYSQL->bind('id', $IKO->sess->data['id']);
            if ($MYSQL->query("UPDATE {prefix}users SET avatar_type = 1 WHERE id = :id") > 0) {
                $notice .= $IKO->tpl->entity(
                    'success_notice',
                    'content',
                    $LANG['bb']['profile']['successful_adding_gravatar']
                );
            } else {
                throw new Exception ($LANG['bb']['profile']['error_adding_gravatar']);
            }

        } elseif ($avatar == 2) { // Using identicons
            require_once('applications/libraries/Identicon/Generator/BaseGenerator.php');
            require_once('applications/libraries/Identicon/Generator/GeneratorInterface.php');
            require_once('applications/libraries/Identicon/Generator/GdGenerator.php');
            require_once('applications/libraries/Identicon/Identicon.php');

            $avatar_dir = 'public/img/avatars/' . $IKO->sess->data['id'] . '.png';
            $identicon = new \Identicon\Identicon();
            $imageData = $identicon->getImageData($IKO->sess->data['username'], 500);
            $image = imagecreatefromstring($imageData);
            if ($image !== false) {
                imagepng($image, $avatar_dir);
                imagedestroy($image);
                $MYSQL->bind('user_avatar', $IKO->sess->data['id'] . '.png');
                $MYSQL->bind('id', $IKO->sess->data['id']);
                $MYSQL->query("UPDATE {prefix}users SET user_avatar = :user_avatar, avatar_type = 2 WHERE id = :id");
                $notice .= $IKO->tpl->entity(
                    'success_notice',
                    'content',
                    $LANG['bb']['profile']['successful_identicon']
                );
            } else {
                throw new Exception($LANG['errors']['generate_identicon']);
            }

        } elseif ($avatar == 0) { // Uploading regular avatar
            if (!$_FILES['avatar']) {
                throw new Exception ($LANG['global_form_process']['all_fields_required']);
            } elseif (!in_array($_FILES['avatar']['type'], $mime)) {
                throw new Exception ($LANG['global_form_process']['invalid_file_format']);
            } else {

                $image = $_FILES['avatar'];
                $bin_dir = 'public/img/bin/' . $IKO->sess->data['id'] . '.png';
                copy($image['tmp_name'], $bin_dir);
                list($width, $height, $type, $attr) = getimagesize($bin_dir);

                if ($width > 500 && $height > 500) {
                    throw new Exception ($LANG['global_form_process']['img_dimension_limit']);
                } else {

                    unlink($bin_dir);
                    $avatar_dir = 'public/img/avatars/' . $IKO->sess->data['id'] . '.png';
                    if (copy($image['tmp_name'], $avatar_dir)) {
                        $MYSQL->bind('user_avatar', $IKO->sess->data['id'] . '.png');
                        $MYSQL->bind('id', $IKO->sess->data['id']);
                        $MYSQL->query("UPDATE {prefix}users SET user_avatar = :user_avatar WHERE id = :id");
                        $MYSQL->bind('id', $IKO->sess->data['id']);
                        $MYSQL->query("UPDATE {prefix}users SET avatar_type = 0 WHERE id = :id");
                        $notice .= $IKO->tpl->entity(
                            'success_notice',
                            'content',
                            $LANG['bb']['profile']['successful_upload_avatar']
                        );

                    } else {
                        throw new Exception ($LANG['bb']['profile']['error_upload_avatar']);
                    }

                }

            }
        } else {
            throw new Exception ('Test');
        }

    } catch (Exception $e) {
        $notice .= $IKO->tpl->entity(
            'danger_notice',
            'content',
            $e->getMessage()
        );
    }

}

//Breadcrumbs
$IKO->tpl->addBreadcrumb(
    $LANG['bb']['forum'],
    SITE_URL . '/forum.php'
);
$IKO->tpl->addBreadcrumb(
    $LANG['bb']['members']['home'],
    SITE_URL . '/conversations.php'
);
$IKO->tpl->addBreadcrumb(
    $LANG['bb']['profile']['avatar'],
    '#',
    true
);
$breadcrumbs = $IKO->tpl->breadcrumbs();

$avatar_checked = ($IKO->sess->data['avatar_type'] == "0") ? ' checked' : '';
$gravatar_checked = ($IKO->sess->data['avatar_type'] == "1") ? ' checked' : '';
$identicon_checked = ($IKO->sess->data['avatar_type'] == "2") ? ' checked' : '';

$content .= '<form id="tango_form" action="" method="POST" enctype="multipart/form-data">
                 <fieldset>
                 <input type="radio" id="avatar" name="avatar" class="avatar_activator" value="0"' . $avatar_checked . ' /> <label for="avatar">' . $LANG['bb']['profile']['use_avatar'] . '</label>
                 <div class="iko avatar_uploader">
                   <label for="avatar">' . $LANG['bb']['profile']['change_avatar'] . '</label>
                   <input type="file" name="avatar" id="avatar" />
                 </div>
                 <br />
                 <input type="radio" id="gravatar" name="avatar" value="1"' . $gravatar_checked . ' /> <label for="gravatar">' . $LANG['bb']['profile']['use_gravatar'] . '</label>
                 <br />
                 <input type="radio" id="identicon" name="avatar" value="2"' . $identicon_checked . ' /> <label for="identicon">' . $LANG['bb']['profile']['use_identicon'] . '</label>
                 </fieldset>
                 <br /><br />
                 <input type="submit" name="edit" value="' . $LANG['bb']['profile']['form_save'] . '" />
               </form>';

$content = $breadcrumbs . $notice . $content;

?>
