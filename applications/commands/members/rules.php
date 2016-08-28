<?php

/*
 * Account Activation Module for IkoBB.
 * Everything that you want to display MUST be in the $content variable.
 */
if (!defined('BASEPATH')) {
    die();
}

$page_title = $LANG['bb']['members']['rules'];
$content = '';

//Breadcrumb
$IKO->tpl->addBreadcrumb(
    $LANG['bb']['forum'],
    SITE_URL . '/forum.php'
);
$IKO->tpl->addBreadcrumb(
    $LANG['bb']['members']['home'],
    SITE_URL . '/members.php'
);
$IKO->tpl->addBreadcrumb(
    $LANG['bb']['members']['rules'],
    '#',
    true
);
$content .= $IKO->tpl->breadcrumbs();

$content .= str_replace('%rules%', nl2br($IKO->data['site_rules']), nl2br($LANG['bb']['members']['rules_message']));

?>