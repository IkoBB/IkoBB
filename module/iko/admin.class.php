<?php
/**
 *
 * This file is part of IkoBB Forum and belongs to the module <Admin>.
 *
 * @copyright (c) IkoBB <https://www.ikobb.de>
 * @license       GNU General Public License, version 3 (GPL-3.0)
 *
 * For full copyright and license information, please see
 * the LICENSE file.
 *
 */

namespace Iko;


class admin
{
	public function list_modules() {
		$template = template::get_instance();
		$modules = '';

		$data = "SELECT * FROM " . module::table . " ORDER BY module_name";
		foreach (Core::$PDO->query($data) as $value) {

			if ($value["module_status"]) {
				$status = '<span class="label label-success">Active</span>';
			}
			else {
				$status = '<span class="label label-danger">Disabled</span>';
			}
			$modules .= $template->entity("module_list_entry", array (
				"module_displayname" => $value["module_displayname"],
				"module_name" => $value["module_name"],
				"module_author" => $value["module_author"],
				"module_version" => $value["module_version"],
				"module_status" => $status,), true);
		}

		return $template->entity("module_list", array("module_list_modules" => $modules), true);
	}
}