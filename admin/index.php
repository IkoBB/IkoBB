<?php
namespace Iko;

require_once './../core/core.php';

module::request("template");
module::request("user");
$template = template::get_instance();

$template->version = Core::version;
$template->displayname = "John Wick";
$template->group_displayname = "Administrator";
$template->title = "IkoBB Admin Panel";
$template->sub_title = "Dashboard";
$template->basepath = Core::$basepath;
$template->adminpath = Core::$adminpath;

$template->extra_css = "";
$template->extra_js = "";

$template->content = "Test";

echo $template;