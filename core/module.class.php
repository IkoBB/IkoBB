<?php
/**
 *
 * This file is part of IkoBB Forum and belongs to the module <Iko>.
 *
 * @copyright (c) IkoBB <https://www.ikobb.de>
 * @license GNU General Public License, version 3 (GPL-3.0)
 *
 * For full copyright and license information, please see
 * the LICENSE file.
 *
 */
namespace Iko;
class modul {
	/*
	 * Static Part Start
	 */
	private static $cache = array();
	public static function get($name) {
		if( !isset(self::$cache[$name]) || self::$cache[$name] == null ) {
			self::$cache[$name] = new modul($name);
		}
	}
	public static function exist($name) {
		
	}
	public static function isUsable($name) {
		
	}
	/*
	 * Static Part End
	 */
	
	private $author;
	private $name;
	private $displayname;
	private $version;
	private $is_active;
	private $loader = null;
	
	public function __construct($name) {
		$this->name = $name;
		$this->get_loader();
	}
	private $is_load = false;
	private $loader_class = "modul";
	private function get_loader() {
		$filename = core::$modulepath . $this->name . "/modul.php";
		if(!file_exists($filename)) {
			throw new \Exception("Needed File Iko_loader to implement the Module " . $this->name . " does not exist.");
		}
		$handle = fopen($filename, "r");
		$namespace = "";
		while($read = fgets($handle)) {
			if(strpos($read, "namespace") !== false) {
				$namespace = trim(str_replace(";", "", str_replace("namespace", "", $read)));
				break;
			}
		}
		fclose($handle);
		$include = @include($filename);
		if($include === false) {
			throw new \Exception("Need File module.php to implement the Module " . $this->name . " does not exist.");
		}
		else if($include == true) {
			$class = '' . $namespace . '\\modul';
			if(class_exists($class))
				$this->loader = @new $class($this);
			else 
				throw new \Exception("#1238 " . $class . " not found in " . $filename . "");
		}
	}
	public function load() {
		if($this->loader != null && $this->is_active && !$this->is_load)
			$this->loader->load();
	}
	public function get_name() {
		return $this->name;
	}
	public function get_path() {
		return Core::$modulepath . $this->get_name() . "/";
	}
}