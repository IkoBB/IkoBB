<?php
namespace Iko;

use Iko\Permissions\User as PUser;

require_once 'core/core.php';
module::request("user");
//$puser = User::get("MatPlayTV");
//var_dump($puser);
/*$permuser = PUser::get($puser);
print_r($permuser);
var_dump($permuser->has_Permission("iko.*"));
echo "<br>" . Core::$basepath;
echo "<br>" . Core::$currentfile;
echo $_SESSION['user_ip'] . "<br>";
echo time() . "<br>";
var_dump($puser->is_own());
*/
set_session("user_id", 1);
$user = User::get(1);
echo time() . "<br>";
echo $user->salt("admin_password") . "<br>";
$user->update_last_login("admin_password");
print_r($user);
echo count(User::get_all());
