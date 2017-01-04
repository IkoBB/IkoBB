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

/**
 * Class cms
 * @package iko\cms
 */
class cms
{
	/**
	 * Table name with {prefix} for the custom pages
	 */
	const table = "{prefix}pages";
	/**
	 * Column name for the colum 'id' for custom pages
	 */
	const column_id = "page_id";

	/**
	 * Checks if a custom page exists
	 *
	 * @param $site_id
	 *
	 * @return bool
	 */
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

	/**
	 * Constructs a page and sends the $_GET variable to a module which will handle the output
	 *
	 * @param array $args
	 */
	function __construct ($args = array ())
	{
		$config_iko = config::load("pdo", "iko");
		$template = template::get_instance();
		$template->title = $config_iko->site_name;

		if (array_key_exists('module', $args)) {
			if (module::exist($args['module'])) {

				/**
				 * Please add to the following function to your module:
				 * Handler::add_event(YOUR_MODULE_NAME, 'iko.cms.register.module', YOUR_CLASS_NAME, YOUR_OUTPUT_FUNCTION);
				 *
				 * Replace all uppercase text to your strings.
				 * You can find an example in this module. Take a look in the module.php and in this file at the function init_page()
				 *
				 * You have to include in your class also an output function. In this output function you will have as input the $_GET variable.
				 * Please check if the input is the correct data type.
				 */
				Handler::event_module('iko.cms.register.module', $args['module'], $args);
			}
			else {
				$this->init_page(NULL, array ('id' => 0), NULL);
			}
		}
		else {
			// Default page when no module is defined
			// ToDo: Define how the default page is set and how it will be handled
			$template->sub_title = 'Default page';
			$template->content = 'Default page';
			echo $template;
		}
		//echo $template;

	}

	/**
	 * Loads a custom page
	 *
	 * @param $site_id
	 *
	 * @throws \iko\Exception
	 */
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

		if($site != FALSE) {
			$template = template::get_instance();
			$template->sub_title = $site['page_title'];
			$template->content = $site["page_content"];
		}
		else {
			$this->load_content(0);
		}

		/*
		$template->entity("cms_site", array (
			"cms_title"   => $site['page_title'],
			"cms_content" => $site["page_content"]));*/
	}

	/**
	 * Page output function
	 * This function will handle the output for the module cms
	 *
	 * $event_name and $var are not used in this function. They are needed for the Event handler
	 *
	 * @param string $event_name
	 * @param array  $args
	 * @param array  $var
	 */
	public function init_page ($event_name, $args, $var)
	{
		if (array_key_exists('id', $args) && self::exists($args['id']) == TRUE) {
			$this->load_content((int)$args['id']);
		}
		else {
			$this->load_content(0);
		}

		$template = template::get_instance();
		echo $template;

	}
}