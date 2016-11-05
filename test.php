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
Event\Handler::add_event("iko.viewpage", "Iko\\User", "test", NULL, TRUE);
Event\Handler::add_event("iko.send.message", "Iko\\User", "chat", NULL, TRUE);
Event\Handler::add_event("iko.send.message", "Iko\\User", "chat2", NULL, TRUE);
Event\Handler::test();
Event\Handler::event("iko.viewpage");
echo Event\Handler::event("iko.send.message",
	"Mein TextPenis Penismacht mich so was vonPenis an. Jannik ist so gemein.");
echo Event\Handler::event("iko.send.message", "Mein Text macht mich so was von an. Jannik ist so gemein.");
