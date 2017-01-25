<?php
/**
 * This file is part of IkoBB Forum and belongs to the module <CMS>.
 *
 * @copyright (c) 2017 IkoBB <https://www.ikobb.de>
 * @license       GNU General Public License, version 3 (GPL-3.0)
 *
 * For full copyright and license information, please see
 * the LICENSE file.
 *
 */


namespace iko\cms;


use iko\cms\template;
use iko\Core;
use iko\module;

/**
 * Class entity
 * @package cms\classes
 */
class entity
{
	function __construct ()
	{
	}

	/**
	 * @param string      $entity
	 * @param string|NULL $module
	 *
	 * @return bool
	 */
	private function exist_entity (string $entity, string $module = NULL): bool
	{
		$return = FALSE;
		$entity_file = $this->get_entity_file($module);
		if (file_exists($entity_file)) {
			$file_content = file_get_contents($entity_file);
			if (strpos($file_content, "<!-- start:" . $entity . " -->") !== FALSE && strpos($file_content,
					"<!-- end:" . $entity . " -->") !== FALSE
			) {
				$return = TRUE;
			}
		}

		return $return;
	}

	/**
	 * @param string|NULL $module
	 *
	 * @return string
	 */
	private function get_entity_file (string $module = NULL): string
	{
		if ($module != NULL) {
			$entity_file = module::get($module)->get_entity_file();
		}
		else {
			$template = template::get_instance();
			$entity_file = Core::$basepath . 'template/' . $template->get_directory() . "/entities.html";
		}

		return $entity_file;

	}


	/**
	 * @param string        $entity
	 * @param string | NULL $module
	 *
	 * @return string
	 */
	private function get_entity (string $entity, string $module = NULL): string
	{
		$entity_content = "";
		$file = $this->get_entity_file($module);
		if ($this->exist_entity($entity, $module)) {
			$file_content = file_get_contents($file);
			preg_match("/<!-- start:" . $entity . " -->(.*)<!-- end:" . $entity . " -->/is", $file_content,
				$entity_content);

			return $entity_content[1];
		}
		else {
			$template = template::get_instance();

			return FALSE;
		}
	}

	/**
	 * @param string      $entity
	 * @param string|NULL $module
	 */
	public function get_template_entity (string $entity, string $module = NULL)
	{
		$unparsed_entity = $this->get_entity($entity, $module);
		$this->send_to_template($entity, $unparsed_entity);

	}

	/**
	 * @param string      $entity
	 * @param array       $params
	 * @param string|NULL $module
	 *
	 * @return string
	 */
	public function return_entity (string $entity, array $params = array (), string $module = NULL): string
	{
		$unparsed_entity = $this->get_entity($entity, $module);
		$template = template::get_instance();
		$parsed_entity = $template->bladeSyntax($unparsed_entity, $params);

		return $parsed_entity;
	}


	/**
	 * @param string $entity
	 * @param string $content
	 */
	private function send_to_template (string $entity, string $content = "")
	{
		$template = template::get_instance();
		$template->add_entity($entity, $content);
	}

}