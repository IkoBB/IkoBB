<?php

define('BASEPATH', 'Forum');
require_once('applications/wrapper.php');
$IKO->tpl->getTpl('forum');
$IKO->tpl->addParam('forum_listings', $IKO->bb->listings());

echo $IKO->tpl->output();

?>