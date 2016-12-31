<?php
namespace iko;

use iko\user\profile\avatar;
use iko\user\User;

$start = microtime(TRUE);

require 'core/core.php';

$_SESSION["user_id"] = 2;
module::request("user");
//$avatar = new avatar(User::get_session());
//User::get_session()->change_email("marcel.nowocyn@googlemail.com");
//echo "<img src='" . $avatar->get() . "' >";
$end = microtime(TRUE);
echo "<br>" . ($end - $start) . "<br>" . $start . "<br>" . $end;
