<?php
namespace iko;

require './../core/core.php';

use iko\cms\template;
use iko\user\User;

$template = template::get_instance();

$template->version = Core::version;
$template->displayname = User::get_session()->get_name();
$template->user_image = User::get_session()->get_avatar();
$template->group_displayname = "Administrator";
$template->title = "IkoBB Admin Panel";
$template->sub_title = "Dashboard";
$template->basepath = Core::$basepath;
$template->adminpath = Core::$adminpath;

$template->extra_css = "";
$template->extra_js = "";

$template->content = "Test";

echo $template;