<?php

define('BASEPATH', 'Forum');
require_once('applications/wrapper.php');

$IKO->tpl->getTpl('members');
switch ($PGET->g('cmd')) {

    case "register":
        require_once('applications/commands/members/register.php');
        $IKO->tpl->addParam(
            array(
                'page_title',
                'content'
            ),
            array(
                $LANG['bb']['members']['register'],
                $content
            )
        );
        break;

    case "signin":
        require_once('applications/commands/members/signin.php');
        $IKO->tpl->addParam(
            array(
                'page_title',
                'content'
            ),
            array(
                $LANG['bb']['members']['log_in'],
                $content
            )
        );
        break;

    case "logout":
        require_once('applications/commands/members/logout.php');
        break;

    case "user":
        require_once('applications/commands/members/user.php');
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

    case "activate":
        require_once('applications/commands/members/activate.php');
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

    case "forgotpassword":
        require_once('applications/commands/members/forgotpassword.php');
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

    case "resetpassword":
        require_once('applications/commands/members/resetpassword.php');
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

    case "rules":
        require_once('applications/commands/members/rules.php');
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
        require_once('applications/commands/members/home.php');
        $IKO->tpl->addParam(
            array(
                'page_title',
                'content'
            ),
            array(
                $LANG['bb']['members']['home'],
                $content
            )
        );
        break;

}

echo $IKO->tpl->output();

?>