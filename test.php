<?php
namespace Iko;

use Iko\Permissions\User as PUser;


require_once 'core/core.php';
set_session("user_id", 1);
$config = config::load("file", Core::$basepath . "meineconfig.php");
$config->set("test", array (
	"Jetzt",
	"nur",
	"Arrays"), "Warum nicht!!!!");
print_r($config);
/*
$test = (new class(user::get_session()->get_id(), "Pascal") extends User {
	public function __construct ($user_id, $kuerzel)
	{
		parent::__construct($user_id);
		$this->kuerzel = $kuerzel;
	}
	public function get_user_name ()
	{
		return $this->kuerzel;
	}
});
print_r($test);
echo $test->get_user_name();
if($test instanceof User) {
	echo "erfolg";
}
else
	echo "fehler";
*/
$lang = module::get("language");
$user = module::get("user");
print_r($user);
print_r($lang);
Event\Handler::event("iko.user.registration", "Ich halt");
print_r($lang);