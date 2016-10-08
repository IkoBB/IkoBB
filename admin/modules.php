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
$template->sub_title = "Modules";
$template->basepath = Core::$basepath;
$template->adminpath = Core::$adminpath;

$template->extra_css = $template->entity("CSS_DataTables", array (), true);
$template->extra_js = $template->entity("JS_dataTables", array (), true);
$template->extra_js .= $template->entity("JS_SlimScroll", array (), true);
$template->extra_js .= $template->entity("JS_FastClick", array (), true);
$template->extra_js .= $template->entity("Scrip_ModuleTable", array (), true);

$template->content = '<div class="row">
  <div class="col-xs-12">
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Modules Table</h3>
      </div>
      <div class="box-body">
        <table id="modules-table" class="table table-bordered table-striped table-hover">
          <thead>
          <tr>
            <th>Displayname</th>
            <th>Name</th>
            <th>Author</th>
            <th>Version</th>
            <th>Status</th>
          </tr>
          </thead>
          <tbody>';

$data = "SELECT * FROM {prefix}modules ORDER BY module_name";
foreach (Core::$PDO->query($data) as $value) {
	$template->content .= '<tr><td>' . $value["module_displayname"] . '</td>' . '<td>' . $value["module_name"] . '</td>' . '<td>' . $value["module_author"] . '</td>' . '<td>' . $value["module_version"] . '</td>';
	if ($value["module_status"]) {
		$template->content .= '<td><span class="label label-success">Active</span> </td>';
	}
	else {
		$template->content .= '<td><span class="label label-danger">Disabled</span> </td>';
	}
	$template->content .= '</tr>';
}
$template->content .= '</tbody>
        </table>
      </div>
    </div>
  </div>
</div>';

echo $template;