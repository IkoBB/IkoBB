<?php
/**
 *
 * This file is part of IkoBB Forum and belongs to the module <Iko>.
 *
 * @copyright (c) IkoBB <https://www.ikobb.de>
 * @license       GNU General Public License, version 3 (GPL-3.0)
 *
 * For full copyright and license information, please see
 * the LICENSE file.
 *
 */
namespace Iko;
class module
{
	/*
	 * Static Part Start
	 */
	private static $cache = array ();

	public static function get($name)
	{
		if (!isset(self::$cache[$name]) || self::$cache[$name] == null) {
			self::$cache[$name] = new module($name);
		}
	}

	public static function exist($name)
	{

	}

	public static function isUsable($name)
	{

	}

	/*
	 * Static Part End
	 */

	private $author;
	private $name;
	private $displayname;
	private $version;
	private $is_active = true;
	private $loader = null;

	public function __construct($name, $loader_name = "")
	{
		$this->name = $name;
		$this->get_loader();
		if ($loader_name != "") {
			$this->loader_class_name = $loader_name;
		}
	}

	private $is_load = false;
	private $loader_class_name = "module";

	private function get_loader()
	{
		$filename = core::$modulepath . $this->name . "/module.php";
		if (!file_exists($filename)) {
			throw new \Exception("Needed File Iko_loader to implement the Module " . $this->name . " does not exist.");
		}
		$handle = fopen($filename, "r");
		$namespace = "";
		$nextbreak = false;
		while ($read = fgets($handle)) {
			if (strpos($read, "namespace") !== false) {
				$namespace = trim(str_replace(";", "", str_replace("namespace", "", $read)));
				$nextbreak = true;
			}
			else {
				if ($nextbreak) {
					if (strpos($read, "class") !== false) {
						$words = str_word_count($read, 1, '0...9,_');
						$this->loader_class_name = trim($words[1]);
						break;
					}
				}
			}
		}
		fclose($handle);
		$include = include($filename);
		if ($include === false) {
			throw new \Exception("Need File module.php to implement the Module " . $this->name . " does not exist.");
		}
		else {
			$class = '' . $namespace . '\\' . $this->get_loader_name();
			if (class_exists($class)) {
				$this->loader = @new $class($this);
			}
			else {
				throw new \Exception("#1238 " . $class . " not found in " . $filename . "");
			}

		}
	}

	private function get_loader_name()
	{
		return $this->loader_class_name;
	}

	public function check()
	{
		if ($this->loader instanceof module_loader && $this->is_active) {
			return $this->loader->check();
		}
		else {
			return true;
		}
	}

	public function is_Checked()
	{
		if ($this->loader instanceof module_loader && $this->is_active) {
			return $this->loader->is_Checked();
		}
		else {
			return true;
		}
	}

	public function load()
	{
		if ($this->loader instanceof module_loader && $this->is_active && !$this->is_load) {
			return $this->loader->load();
		}
		else {
			return false;
		}
	}

	public function get_name()
	{
		return $this->name;
	}

	public function get_path()
	{
		return Core::$modulepath . $this->get_name() . "/";
	}

	public function install()
	{
		$this->loader->create_PDO_Tables();
	}
}