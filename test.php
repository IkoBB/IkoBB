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
set_session("user_id", 2);
Event\Handler::add_event("iko.viewpage", "Iko\\User", "test", NULL, TRUE);
Event\Handler::add_event("iko.send.message", "Iko\\User", "chat", NULL, TRUE);
Event\Handler::add_event("iko.send.message", "Iko\\User", "chat2", NULL, TRUE);
Event\Handler::test();
Event\Handler::event("iko.viewpage");
$matze = User::get(2);
$matze->change_user_name("hallo");
var_dump($matze->is_own());
$admin = User::get(1);
$admin->change_user_name("test");
var_dump($admin->is_own());
var_dump(module::version("user"));
