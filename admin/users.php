<?php
namespace iko;

require_once './../core/core.php';

module::request("cms");
module::request("user");
module::request("iko");
$template = template::get_instance();
$admin = new admin();

$template->displayname = "John Wick";
$template->group_displayname = "Administrator";
$template->title = "IkoBB Admin Panel";
$template->sub_title = "Users";

$template->extra_css = $template->entity("CSS_DataTables", array (), true);
$template->extra_js = $template->entity("JS_dataTables", array (), true);
$template->extra_js .= $template->entity("JS_SlimScroll", array (), true);
$template->extra_js .= $template->entity("JS_FastClick", array (), true);
$template->extra_js .= $template->entity("Scrip_ModuleTable", array (), true);

$admin->list_users();

echo $template;