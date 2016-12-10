<?php
namespace iko;

use Iko\user\permissions\User as PUser;

require_once 'core/core.php';

/*$config = config::load("file", Core::$basepath . "meineconfig.php");
var_dump($config->set("main", array (
	"Jetzt",
	"nur",
	"Arrays","testiii",
	"Penis"), "Warum nicht!!!!"));
var_dump($config->add("hans", "lisa", "Im Glueck"));
print_r($config);*/
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
try {
	user\User::get("penis");
	$user = User::get("Marcel");
	$user->user_name = "Pennis";
	echo $user->user_name;
}
catch (\Exception $ex) {
	echo NULL;
}
