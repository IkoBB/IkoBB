<?php
namespace iko;

use iko\Event\Handler;
use iko\language\key;
use iko\user\Group;
use iko\user\profile\Field;
use iko\user\profile\Content;
use iko\user\User;

$start = microtime(TRUE);

require 'core/core.php';

/*$user = User::get_session();
// 1 2 3 46 47
$user_1 = User::get(1);
$user_2 = User::get(2);
$user_3 = User::get(3);
$user_46 = User::get(46);
$user_47 = User::get(47);
$admin = Group::get(1);
$mod = Group::get(2);
$member = Group::get(3);
$sup_member = Group::get(4);
$user_46->add_group($sup_member);
$user_47->add_group($member);
$user_3->add_group($mod);
$user_1->add_group($admin);

var_dump(Handler::isset_event("iko.cms.register.module"));
var_dump(Handler::isset_event_module("iko.cms.register.module", "cms"));*/
Handler::test();
$user = User::get_session();
var_dump(User::get_session()->has_permission("iko.language.keys.set.lang"));
print_r($user->get_permission_class()->get_permissions());
$key = key::get("user_name");
var_dump($key->set_lang("english", "usermituser"));
$key->get_lang("english");
print_r($key);

/*$all_user = User::get_all();
foreach($all_user as $item) {
	//$item = (User) $item;
	echo "User ID: " . $item->get_id() . " | ";
	var_dump($item->has_permission("iko.user.set.user_name"));
	echo "<br>";
}*/
//print_r($all_user);
/*
 * Handler::add_event(module::get("user"),$item->get_name(), get_called_class(), "has_permission", NULL, FALSE, "get");
 * module::request("user");
$user = User::get(2);
print_r($user);
$group = Group::get(2);
print_r($group);
$user = User::get(1);
print_r($user);
$field = Field::get(1);
print_r($field);
echo "<br>";
$main = new Content($user, Field::get(2));
echo "<br>";
print_r($main);
$array = array(0 => array("prefix" => "https://www.youtube.com/c/"),
               1 => array("prefix" => "https://www.youtube.com/channel/"),
               2 => array("prefix" => "https://www.youtube.com/user/"));
echo serialize($array);
echo "<br>";
echo $main->get();*/
//O:21:"iko\user\profile\main":3:{s:5:"*id";i:1;s:8:"*value";s:4:"test";s:11:"*property";N;}
//O:21:"iko\user\profile\main":4:{s:5:"*id";i:1;s:8:"*value";s:4:"test";s:11:"*property";N;s:10:"*user_id";i:2;}
//$avatar = new avatar(User::get_session());
//User::get_session()->change_email("marcel.nowocyn@googlemail.com");
//echo "<img src='" . $avatar->get() . "' >";
$end = microtime(TRUE);
echo "<br>" . ($end - $start) . "<br>" . $start . "<br>" . $end;
