<?php
/**
 *
 * This file is part of IkoBB Forum and belongs to the module <CMS>.
 *
 * @copyright (c) 2016 IkoBB <https://www.ikobb.de>
 * @license       GNU General Public License, version 3 (GPL-3.0)
 *
 * For full copyright and license information, please see
 * the LICENSE file.
 *
 */

namespace iko\cms;

use iko\Core;
use iko\PDO;
use iko\config;
use iko\Exception;

class cms
{
	const table = "{prefix}pages";
	const column_id = "page_id";

	public static function exists ($site_id): bool
	{
		if (is_numeric($site_id) && $site_id != 0 && $site_id != NULL) {
			$statement = Core::$PDO->prepare("SELECT " . self::column_id . " FROM " . self::table . " WHERE " . self::column_id . " = :id");
			$statement->bindParam(':id', $site_id);
			$statement->execute();
			if ($statement->rowCount() > 0) {
				return TRUE;
			}
			else {
				return FALSE;
			}
		}
		else {
			return FALSE;
		}
	}

	function __construct ($page = "index", $id = NULL)
	{
		$config_iko = config::load("pdo", "iko");
		$template = template::get_instance();

		$template->title = $config_iko->site_name;

		// Check if id is set and if it is an integer or not
		if ($id != NULL && is_numeric($id)) {
			$id = (int)$id;
		}
		elseif ($id != NULL && !is_numeric($id)) {
			$id = NULL;
		}

		// Loads the default page
		if (strcasecmp($page, 'index') == 0) {
			// load default page
			$config_cms = config::load("pdo", "cms");
			$page = $config_cms->default_page;
		}

		if (strcasecmp($page, 'forum') == 0 && $id === NULL) {
			// load forum list

		}
		elseif (strcasecmp($page, 'forum') == 0 && $id != NULL) {
			// load content for forum

		}
		elseif (strcasecmp($page, 'thread') == 0 && $id != NULL) {
			// load thread list

		}
		elseif (strcasecmp($page, 'page') == 0 && $id != NULL) {
			// load content for custom pages
			if (self::exists($id)) {
				$this->load_content($id);
			}
			else {
				$this->load_content(0);
			}
		}
		elseif ((strcasecmp($page, 'members') == 0 || strcasecmp($page, 'member') == 0) && $id === NULL) {
			// load member list

		}
		elseif ((strcasecmp($page, 'members') == 0 || strcasecmp($page, 'member') == 0) && $id != NULL) {
			// load member profile

		}
		elseif (strcasecmp($page, 'imprint') == 0 || strcasecmp($page, 'impressum') == 0) {
			// Imprint is needed in some countries

		}
		elseif (strcasecmp($page, 'debug') == 0) {
			// Testing and debug page
			$parser = new parser();
			$template->sub_title = "Demo & Testing page";
			$template->content = $parser->parse("[b]Welcome to the IkoBB demo and testing page[/b]");
			$template->content .= $template->entity("TEST", array (
				"output"      => $parser->parse(\iko\define_post("text", "")),
				"code_output" => $parser->parse('[code]' . \iko\define_post("text", "") . '[/code]')), TRUE);
		}
		else {
			// 404 page
			$this->load_content(0);
		}

		echo $template;

	}

	private function load_content ($site_id)
	{

		try {
			$statement = Core::$PDO->prepare("SELECT * FROM " . self::table . " WHERE " . self::column_id . " = :id");
			$statement->bindParam(':id', $site_id);
			$statement->execute();
			$site = $statement->fetch(PDO::FETCH_ASSOC);
		}
		catch (\PDOException $exception) {
			throw new Exception("Error #1234: " . $exception);
		}

		$template = template::get_instance();
		$template->sub_title = $site['page_title'];
		$template->content = $site["page_content"];
		/*
		$template->entity("cms_site", array (
			"cms_title"   => $site['page_title'],
			"cms_content" => $site["page_content"]));*/
	}
}