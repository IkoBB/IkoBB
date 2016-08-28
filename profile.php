<?php

define('BASEPATH', 'Forum');
require_once('applications/wrapper.php');
if (!$IKO->sess->isLogged) {
    redirect(SITE_URL . '/403.php');
}//Check if user is logged in.

$IKO->tpl->getTpl('members');

switch ($PGET->g('cmd')) {

    case "edit":
        require_once('applications/commands/profile/edit.php');
        $IKO->tpl->addParam(
            array(
                'page_title',
                'content'
            ),
            array(
                $page_title,
                $content
            )
        );
        break;

    case "avatar":
        require_once('applications/commands/profile/avatar.php');
        $IKO->tpl->addParam(
            array(
                'page_title',
                'content'
            ),
            array(
                $page_title,
                $content
            )
        );
        break;

    case "signature":
        require_once('applications/commands/profile/signature.php');
        $IKO->tpl->addParam(
            array(
                'page_title',
                'content'
            ),
            array(
                $page_title,
                $content
            )
        );
        break;

    case "password":
        require_once('applications/commands/profile/password.php');
        $IKO->tpl->addParam(
            array(
                'page_title',
                'content'
            ),
            array(
                $page_title,
                $content
            )
        );
        break;

    case "theme":
        require_once('applications/commands/profile/theme.php');
        $IKO->tpl->addParam(
            array(
                'page_title',
                'content'
            ),
            array(
                $page_title,
                $content
            )
        );
        break;

    default:
        redirect(SITE_URL . '/404.php');
        break;

}

echo $IKO->tpl->output();

?>