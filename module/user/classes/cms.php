<?php
/**
 *
 * This file is part of IkoBB Forum and belongs to the module <User>.
 *
 * @copyright (c) 2017 IkoBB <https://www.ikobb.de>
 * @license       GNU General Public License, version 3 (GPL-3.0)
 *
 * For full copyright and license information, please see
 * the LICENSE file.
 *
 */
/**
 * Created by PhpStorm.
 * User: Marcel
 * Date: 14.01.2017
 * Time: 20:07
 */
namespace iko\user;

use iko\cms\template;

class cms
{

	public function __construct ()
	{

	}

	function user_list ()
	{
		$users = User::get_all();
		$string = "";
		$template = template::get_instance();
		template::add_breadcrumb("List", "?module=user&page=user_list");
		template::add_sidebar();
		$user_string = "";
		foreach ($users as $user) {
			$user_string .= $template->entity("user-entries", array (
				"user_name" => $user->get_name(),
				"user_avatar" => $user->get_avatar(),
				"group" => "Member",
				"user_link" => "?module=user&user=" . $user->get_id()), TRUE);
		}
		$template->content = $template->entity("user-list", array ("user-entries" => $user_string), TRUE);
	}

	function std ()
	{
		$template = template::get_instance();

	}

	function user ()
	{
		if (isset($args["id"])) {
			$id = $args["id"];
			$template = template::get_instance();
			if (user::exist($id)) {

			}
			else {
				$this->user_list();
			}
		}
		else {
			$this->user_list();
		}
	}

	function init_page ($event_name, $args, $var = NULL)
	{
		template::add_breadcrumb("User", "?module=user");
		if (isset($args["page"])) {
			$func = $args["page"];
			if (is_callable(get_called_class(), $func)) {
				$this->{$func}();
			}
		}
		else {
			$this->std();
		}
		echo template::get_instance();
	}
}