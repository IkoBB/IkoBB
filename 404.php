<?php

define('BASEPATH', 'Forum');
require_once('applications/wrapper.php');

$IKO->tpl->getTpl('page');

$IKO->tpl->addParam('page_title', $LANG['error_pages']['404']['header']);
$IKO->tpl->addParam('content', $LANG['error_pages']['404']['message']);

echo $IKO->tpl->output();

?>