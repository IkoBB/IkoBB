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

namespace iko;


class admin
{
	public function list_modules ()
	{
		$template = template::get_instance();
		$modules = '';
		try {
			$data = "SELECT * FROM " . module::table . " ORDER BY module_name";
			foreach (Core::PDO()->query($data) as $value) {
				if ($value["module_status"]) {
					$status = '<span class="label label-success">Active</span>';
				}
				else {
					$status = '<span class="label label-danger">Disabled</span>';
				}
				$modules .= $template->entity("module_list_entry", array (
					"module_displayname" => $value["module_displayname"],
					"module_name"        => $value["module_name"],
					"module_author"      => $value["module_author"],
					"module_version"     => $value["module_version"],
					"module_status"      => $status,), TRUE);
			}


			return $template->entity("module_list", array ("module_list_modules" => $modules), TRUE);
		}
		catch (\PDOException $exception) {
			echo $exception->getMessage() . "<br>";
		}
	}

	public function list_users ()
	{
		module::request("user");
		$template = template::get_instance();
		$users = User::get_all();
		$user_entities = "";

		foreach ($users as $user) {
			$user_entities .= $template->entity("user_list_entry", array (
				"user_id" => $user->get_id(),
				"user_name" => $user->get_user_name(),
				"user_email" => $user->get_email(),
				"user_date_joined" => parser::dynamic_time($user->get_joined_Time()),
				), TRUE);
		}

		$template->content = $template->entity("user_list", array ("user_list_entries" => $user_entities), TRUE);

	}
}