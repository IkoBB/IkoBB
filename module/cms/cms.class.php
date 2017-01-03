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
use iko\Event\Handler;
use iko\module;
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

	function __construct ($args = array ())
	{
		$config_iko = config::load("pdo", "iko");
		$template = template::get_instance();
		$template->title = $config_iko->site_name;

		if (array_key_exists('module',$args)) {
			if (module::exist($args['module'])) {

				/**
				 * Please add to the following function to your module:
				 * Handler::add_event(YOUR_MODULE_NAME, 'iko.cms.register.module', YOUR_CLASS_NAME, YOUR_OUTPUT_FUNCTION);
				 *
				 * Replace all uppercase text to your strings.
				 *
				 * You have to include in your class also an output function. In this output function you will have as input the $_GET variable.
				 * Please check if the input is the correct data type.
				 */
				Handler::event_module('iko.cms.register.module', $args['module'], $args);
			}
			else {
				$this->load_content(0);
			}
		}
		else {

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