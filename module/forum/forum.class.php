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
	private function get_forums(int $forum_id = NULL, int $category_id = NULL) {
		if($forum_id === NULL && $category_id != NULL) {
			try {
				$statement = Core::$PDO->prepare("SELECT * FROM ".self::table_node ." WHERE " . self::column_category_id . " = :category_id");
				$statement->bindParam(':category_id', $category_id);
				$statement->execute();
				$result = $statement->fetchAll(PDO::FETCH_ASSOC);
			}
			catch (\PDOException $exception) {
				throw new Exception("Error #1234: " . $exception);
			}
			return $result;
		}
		elseif ($forum_id != NULL && $category_id === NULL) {
			try {
				$statement = Core::$PDO->prepare("SELECT * FROM ".self::table_node ."  WHERE " . self::column_forum_id . " = :forum_id");
				$statement->bindParam(':forum_id', $forum_id);
				$statement->execute();
				$result = $statement->fetch(PDO::FETCH_ASSOC);
			}
			catch (\PDOException $exception) {
				throw new Exception("Error #1234: " . $exception);
			}
			return $result;
		}
	}

	private function get_category(int $category_id = NULL) {
		if($category_id === NULL) {
			try {
				$statement = Core::$PDO->prepare("SELECT * FROM " . self::table_category);
				$statement->execute();
				$result = $statement->fetchAll(PDO::FETCH_ASSOC);
			}
			catch (\PDOException $exception) {
				throw new Exception("Error #1234: " . $exception);
			}
			return $result;
		}
		elseif ($category_id != NULL) {
			try {
				$statement = Core::$PDO->prepare("SELECT * FROM " . self::table_category . " WHERE " . self::column_category_id . " = :category_id");
				$statement->bindParam(':category_id', $category_id);
				$statement->execute();
				$result = $statement->fetchAll(PDO::FETCH_ASSOC);
			}
			catch (\PDOException $exception) {
				throw new Exception("Error #1234: " . $exception);
			}
			return $result;
		}

	}

	private function forum_overview ()
	{
		template::add_sidebar();
		template::add_breadcrumb("Forum", "?module=forum");
		$template = template::get_instance();
		$template->sub_title = "Forum";

		// Get all categories
		// ToDo: Add permissions support
		$categories = $this->get_category();
		foreach ($categories as $key_category => $category)
		{
			$forum_entries = "";
			// Load all forums for each category
			$forums = $this->get_forums(NULL, $category[self::column_category_id]);
			foreach ($forums as $key => $forum) {
				$forum_entries .= $template->entity("forum-entries", array(
					"forum_name" => $forum[self::column_forum_name],
					"forum_description" => $forum[self::column_forum_description]
				), TRUE);
			}

			$template->content .= $template->entity("forum-list", array(
				"forum-entries" => $forum_entries,
				"forum_category_name" => $category[self::column_category_name]), TRUE);
		}

		echo $template;
	}

	function init_page ($event_name, $args, $var = NULL)
	{
		$this->forum_overview();
	}
}