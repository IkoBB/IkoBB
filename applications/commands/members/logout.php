<?php

/*
 * Sign Out module for IkoBB
 * Everything that you want to display MUST be in the $content variable.
 */
if (!defined('BASEPATH')) {
    die();
}

if (!$IKO->sess->isLogged) {
    redirect(SITE_URL);
} //If user is not logged in.

if ($FB_USER) {
    $FACEBOOK->destroySession();
    $IKO->sess->remove();
} else {
    $IKO->sess->remove();
}
redirect(SITE_URL);

?>