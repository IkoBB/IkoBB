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

		$data = "SELECT * FROM {prefix}modules ORDER BY module_name";
		foreach (Core::$PDO->query($data) as $value) {
			$modules .= '<tr><td>' . $value["module_displayname"] . '</td>' . '<td>' . $value["module_name"] . '</td>' . '<td>' . $value["module_author"] . '</td>' . '<td>' . $value["module_version"] . '</td>';
			if ($value["module_status"]) {
				$modules .= '<td><span class="label label-success">Active</span> </td>';
			}
			else {
				$modules .= '<td><span class="label label-danger">Disabled</span> </td>';
			}
			$modules .= '</tr>';
		}

		return $template->entity("module_list", array("module_list_modules" => $modules), true);
	}
}