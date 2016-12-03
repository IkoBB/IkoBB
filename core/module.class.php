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
class module // TODO: Implemnt autoloading of Modules and posibility to load Modules simple over one function.
{
	const table = "{prefix}modules";
	/*
	 * Static Part Start
	 */
	private static $cache = array ();
	private static $cache_exist = array ();

	public static function get ($names = "", $reload = FALSE)
	{
		if ($names != "" && $names != NULL) {
			if (is_array($names)) {
				self::exist($names);
			}
			if (is_string($names)) {
				$names = array ($names);
			}
			$modules = array ();
			foreach ($names as $name) {
				if (!isset(self::$cache[ $name ]) || self::$cache[ $name ] == NULL || $reload) {
					if (self::exist($name, $reload)) {
						$class = str_replace(__NAMESPACE__ . "/", "", __CLASS__);
						self::$cache[ $name ] = new $class($name);
						array_push($modules, self::$cache[ $name ]);
					}
				}
				else {
					array_push($modules, self::$cache[ $name ]);
				}
			}
			if (count($modules) == 1) {
				return $modules[0];
			}
			else {
				return $modules;
			}
		}

		return NULL;
	}

	/**
	 * @param int  $names
	 * @param bool $reload
	 *
	 * @return bool|mixed
	 */
	public static function exist ($names = "", $reload = FALSE)
	{
		if ($names != "" && $names != NULL) {
			$statement = Core::$PDO->prepare("SELECT module_name FROM " . self::table . " WHERE module_name = :module_name");
			if (is_string($names) || is_int($names)) {
				if (!isset(self::$cache_exist[ $names ]) || $reload) {
					$statement->bindParam(':module_name', $names);
					$statement->execute();
					if ($statement->rowCount() > 0) {
						self::$cache_exist[ $names ] = TRUE;

						return TRUE;
					}
					else {
						self::$cache_exist[ $names ] = FALSE;

						return FALSE;
					}
				}

				return self::$cache_exist[ $names ];
			}
			else {
				if (is_array($names)) {
					foreach ($names as $name) {
						if (!isset(self::$cache_exist[ $name ]) || $reload) {
							$statement->bindParam(':module_name', $name);
							$statement->execute();
							if ($statement->rowCount() > 0) {
								self::$cache_exist[ $name ] = TRUE;
							}
							else {
								self::$cache_exist[ $name ] = FALSE;
							}
						}
					}

					return TRUE;
				}
				else {
					return FALSE;
				}
			}
		}
		else {
			return FALSE;
		}
	}

	public static function request ($name, $class = FALSE)
	{
		if (self::exist($name) && self::status($name)) {
			if (!$class) {
				return self::get($name)->load_complete();
			}
			else {
				$class = self::get($name);
				$class->load_complete();

				return $class;
			}
		}
		else {
			return FALSE;
		}
	}

	public static function status ($name)
	{
		if (self::exist($name)) {
			return self::get($name)->get_status();
		}
	}

	public static function load_status ($name)
	{
		if (self::exist($name)) {
			return self::get($name)->is_load();
		}
	}
	public static function init ()
	{
		self::request("iko");
		self::pre_check();
	}

	public static function version ($name)
	{
		return self::get($name)->get_version();
	}

	private static function pre_check ()
	{
		$statement = Core::$PDO->query("SELECT module_name FROM " . self::table . " ");
		$fetch_all = $statement->fetchAll();
		foreach ($fetch_all as $item) {
			$module = module::get($item["module_name"]);
			$filename = Core::$modulepath . $item["module_name"] . "/module.php";
			if (!file_exists($filename)) {
				throw new \Exception("Needed File module.php to implement the Module " . $item["module_name"] . " does not exist.");
			}
			$module->event_handler_init();
		}
	}

	/*
	 * Static Part End
	 */

	private $author;
	private $name;
	private $displayname;
	private $version;
	private $status = TRUE;
	private $loader = NULL;

	private function __construct ($name)
	{
		$statement = Core::$PDO->query("SELECT * FROM " . self::table . " WHERE module_name = '" . $name . "'");
		$fetch = $statement->fetch(PDO::FETCH_ASSOC);
		foreach ($fetch as $key => $value) {
			$temp_key = str_replace("module_", "", $key);
			$this->{$temp_key} = $value;
		}
		$this->status = (bool)$this->status;
		$this->load_loader();
	}

	private $is_load = FALSE;
	private $loader_class_name = "module";

	private function load_loader ()
	{
		$filename = Core::$modulepath . $this->name . "/module.php";
		if (!file_exists($filename)) {
			throw new \Exception("Needed File module.php to implement the Module " . $this->name . " does not exist.");
		}
		$handle = fopen($filename, "r");
		$namespace = "";
		$nextbreak = FALSE;
		while ($read = fgets($handle)) {
			if (strpos($read, "namespace") !== FALSE) {
				$namespace = trim(str_replace(";", "", str_replace("namespace", "", $read)));
				$nextbreak = TRUE;
			}
			else {
				if ($nextbreak) {
					if (strpos($read, "class") !== FALSE) {
						$words = str_word_count($read, 1, '0...9,_');
						$this->loader_class_name = trim($words[1]);
						break;
					}
				}
			}
		}
		fclose($handle);
		$include = include($filename);
		if ($include === FALSE) {
			throw new \Exception("Need File module.php to implement the Module " . $this->name . " does not exist.");
		}
		else {
			$class = '' . $namespace . '\\' . $this->get_loader_name();
			if (class_exists($class)) {
				$this->loader = new $class($this);
			}
			else {
				throw new \Exception("#1238 " . $class . " not found in " . $filename . "");
			}

		}
	}

	private function get_loader_name ()
	{
		return $this->loader_class_name;
	}

	private function get_loader ()
	{
		return $this->loader;
	}
	public function check ()
	{
		if ($this->get_loader() instanceof module_loader && $this->get_status()) {
			$this->get_loader()->check();

			return $this->get_loader()->is_Checked();
		}
		else {
			return TRUE;
		}
	}

	public function is_Checked ()
	{
		if ($this->get_loader() instanceof module_loader && $this->get_status()) {
			return $this->get_loader()->is_Checked();
		}
		else {
			return TRUE;
		}
	}

	public function load ()
	{
		if ($this->get_loader() instanceof module_loader && $this->get_status() && !$this->is_load()) {
			if ($this->get_loader()->load()) {
				$this->is_load = TRUE;
			}

			return $this->is_load();
		}
	}

	public function get_name ()
	{
		return $this->name;
	}

	public function get_path ()
	{
		return Core::$modulepath . $this->get_name() . "/";
	}

	public function install ()
	{
		$this->loader->create_PDO_Tables();
	}

	public function get_status ()
	{
		return $this->status;
	}

	public function is_load ()
	{
		return $this->is_load;
	}
	public function load_complete ()
	{
		if ($this->get_status()) {
			if (!$this->is_Checked()) {
				$this->check();
			}
			if ($this->is_Checked()) {
				if ($this->load()) {
					$this->get_loader()->final_load();
				}

				return $this->is_load();
			}
			else {
				return $this->is_load();
			}
		}
		else {
			return $this->is_load();
		}
	}

	public function load_ajax ()
	{
		return $this->get_loader()->load_ajax_file();
	}

	public function get_version ()
	{
		return $this->version;
	}

	public function __toString ()
	{
		return $this->get_name();
	}

	public function event_handler_init ()
	{
		if ($this->get_loader() instanceof module_loader) {
			$this->get_loader()->event_handler_init();
		}
	}
}