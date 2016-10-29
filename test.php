<?php
namespace Iko;

use Iko\Permissions\User as PUser;
require_once 'core/core.php';
module::request("user");
$puser = User::get("MatPlayTV");
$permuser = PUser::get($puser);
print_r($permuser);
var_dump($permuser->has_Permission("iko.*"));
echo "<br>" . Core::$basepath;
echo "<br>" . Core::$currentfile;
echo $_SESSION['user_ip'];
