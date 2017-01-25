<?php
/**
 *
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
use iko\Core;
use iko\Exception;
use iko\PDO;

class forum
{

	const table_category = "{prefix}forum_categories";
	const column_category_id = "forum_category_id";
	const column_category_name = "forum_category_name";
	const table_node = "{prefix}forum_node";
	const column_forum_id = "forum_id";
	const column_forum_name = "forum_name";
	const column_forum_description = "forum_description";

	function __construct ()
	{
	}

	private function get_structure (string $table, string $column = NULL, int $id = NULL, string $mode = "fetchAll")
	{
		$sql = "SELECT * FROM " . $table;
		if ($column != NULL && $id != NULL) {
			$sql .= " WHERE " . $column . " = :id";
		}

		try {
			$statement = Core::PDO()->prepare($sql);
			$statement->bindParam(':id', $id);
			$statement->execute();
			if ($mode == "fetchAll") {
				$result = $statement->fetchAll(PDO::FETCH_ASSOC);
			}
			else {
				$result = $statement->fetch(PDO::FETCH_ASSOC);
			}

		}
		catch (\PDOException $exception) {
			throw new Exception("Error #1234: " . $exception);
		}

		return $result;

	}


	private function list_forums (int $id = NULL)
	{
		// ToDo: Add permissions support
		template::add_sidebar();
		template::add_breadcrumb("Forum", "?module=forum");
		$template = template::get_instance();

		if ($id !== NULL) {
			// get a specific category
			$categories = $this->get_structure(self::table_category, self::column_category_id, $id);
			template::add_breadcrumb($categories[0][ self::column_category_name ],
				"?module=forum&page=category&id=" . $categories[0][ self::column_category_id ]);
			$template->sub_title = $categories[0][ self::column_category_name ];
		}
		else {
			// Get all categories
			$categories = $this->get_structure(self::table_category);
			$template->sub_title = "Forum";
		}
		$entity = new entity();

		foreach ($categories as $key_category => $category) {
			$forum_entries = "";
			// Load all forums for each category
			$forums = $this->get_structure(self::table_node, self::column_category_id,
				$category[ self::column_category_id ]);
			foreach ($forums as $key => $forum) {


				$forum_entries .= $entity->return_entity("forum-entries", array (
					"forum_name"        => $forum[ self::column_forum_name ],
					"forum_description" => $forum[ self::column_forum_description ]), 'forum');

			}

			$template->content .= $entity->return_entity("forum-list", array (
				"forum-entries"       => $forum_entries,
				"forum_category_name" => $category[ self::column_category_name ],
				"forum_category_id" => $category[ self::column_category_id ]), 'forum');
		}

		echo $template;
	}

	private function list_threads($args) {

	}


	function init_page ($event_name, $args, $var = NULL)
	{
		if ($args['page'] == "category" && array_key_exists('id', $args)) {
			$this->list_forums($args ['id']);
		}
		elseif ($args['page'] == "forum" && array_key_exists('id', $args)) {
			$this->list_threads($args['id']);
		}
		else {
			$this->list_forums();
		}

	}
}