<?php
/**
 * This file is part of IkoBB Forum and belongs to the module <Forum>.
 *
 * @copyright (c) 2017 IkoBB <https://www.ikobb.de>
 * @license       GNU General Public License, version 3 (GPL-3.0)
 *
 * For full copyright and license information, please see
 * the LICENSE file.
 *
 */

namespace iko\forum;


use iko\cms\entity;
use iko\cms\template;

class cms
{
	function __construct ()
	{
	}

	private function list_forums (int $id = NULL)
	{
		// ToDo: Add permissions support
		template::add_sidebar();
		template::add_breadcrumb("Forum", "?module=forum");
		$template = template::get_instance();

		if ($id !== NULL) {
			$categories[0] = new category($id);
			$template->sub_title = $categories[0]->get_name();
			template::add_breadcrumb($categories[0]->get_name(), "?module=forum&page=category&id=".$categories[0]->get_id());
		}
		else {
			// Get all categories
			$categories = category::get_all();
			$template->sub_title = "Forum";
		}

		foreach ($categories as $key_category => $category) {
			if($category instanceof category) {
				$board_entries = "";
				$boards = $category->get_child_boards();
				foreach ($boards as $board) {
					if ($board instanceof board) {

						$board_entries .= entity::return_entity("forum-entries", array (
							"forum_name"        => $board->get_name(),
							"forum_description" => $board->get_description()), 'forum');
					}
				}
				$template->content .= entity::return_entity("forum-list", array (
					"forum-entries"       => $board_entries,
					"forum_category_name" => $category->get_name(),
					"forum_category_id" => $category->get_id()), 'forum');
			}
		}

		echo $template;
	}

	private function list_threads($args) {

	}


	function init_page ($event_name, $args, $var = NULL)
	{
		if ($args['page'] == "category" && array_key_exists('id', $args)) {
			$this->list_forums($args['id']);
		}
		elseif ($args['page'] == "board" && array_key_exists('id', $args)) {
			$this->list_threads($args['id']);
		}
		else {
			$this->list_forums();
		}

	}
}