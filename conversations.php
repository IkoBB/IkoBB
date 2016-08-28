<?php

define('BASEPATH', 'Forum');
require_once('applications/wrapper.php');

if (!$IKO->sess->isLogged) {
    redirect(SITE_URL);
}

$IKO->tpl->getTpl('page');

switch ($PGET->g('cmd')) {

    case "view":
        require_once('applications/commands/conversations/view.php');
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

    case "new":
        require_once('applications/commands/conversations/new.php');
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

    case "reply":
        require_once('applications/commands/conversations/reply.php');
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

    case "delete":
        require_once('applications/commands/conversations/delete.php');
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
        require_once('applications/commands/conversations/home.php');
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

}

echo $IKO->tpl->output();

?>