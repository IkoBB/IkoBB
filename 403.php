<?php

define('BASEPATH', 'Forum');
require_once('applications/wrapper.php');

$IKO->tpl->getTpl('page');

$IKO->tpl->addParam('page_title', $LANG['error_pages']['403']['header']);
$IKO->tpl->addParam('content', $LANG['error_pages']['403']['message']);

echo $IKO->tpl->output();

?>