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
namespace iko;
use iko\lib\multiton\cache_string;

class module extends cache_string
{
	const table = "{prefix}modules";
	const name = "module_name";
	/*
	 * Static Part Start
	 */
	protected static $cache = array ();
	protected static $cache_exist = array ();
	public static function get ($id = 0, $reload = FALSE):module
	{
		return parent::get($id, $reload);
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

	public static function status ($name): bool
	{
		if (self::exist($name)) {
			return self::get($name)->get_status();
		}
		return false;
	}

	public static function load_status ($name): bool
	{
		if (self::exist($name)) {
			return self::get($name)->is_load();
		}
		return false;
	}
	public static function init ()
	{
		self::request("iko");
		self::pre_check();
	}

	public static function version ($name): string
	{
		return self::get($name)->get_version();
	}

	private static function pre_check ()
	{
		$statement = Core::PDO()->query("SELECT module_name FROM " . self::table . " ");
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

	protected function __construct ($name)
	{
		$statement = Core::PDO()->query("SELECT * FROM " . self::table . " WHERE module_name = '" . $name . "'");
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
			throw new Exception("Iko | error | 1002 | Need File module.php to implement the Module " . $this->name . " does not exist.");
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
		$include = Core::file_incl($filename);
		if ($include === FALSE) {
			throw new Exception("Iko | error | 1002 | Need File module.php to implement the Module " . $this->name . " does not exist.");
		}
		else {
			$class = '' . $namespace . '\\' . $this->get_loader_name();
			if (class_exists($class)) {
				$this->loader = new $class($this);
			}
			else {
				throw new Exception("#1238 " . $class . " not found in " . $filename . "");
			}

		}
	}

	private function get_loader_name (): string
	{
		return $this->loader_class_name;
	}

	private function get_loader (): module_loader
	{
		return $this->loader;
	}

	public function check (): bool
	{
		if ($this->get_loader() instanceof module_loader && $this->get_status()) {
			$this->get_loader()->check();

			return $this->get_loader()->is_Checked();
		}
		else {
			return TRUE;
		}
	}

	public function is_Checked (): bool
	{
		if ($this->get_loader() instanceof module_loader && $this->get_status()) {
			return $this->get_loader()->is_Checked();
		}
		else {
			return TRUE;
		}
	}

	public function load (): bool
	{
		if ($this->get_loader() instanceof module_loader && $this->get_status() && !$this->is_load()) {
			if ($this->get_loader()->load()) {
				$this->is_load = TRUE;
			}
			return $this->is_load();
		}
		else {
			return FALSE;
		}
	}

	public function get_name (): string
	{
		return $this->name;
	}

	public function get_path (): string
	{
		return Core::$modulepath . $this->get_name() . "/";
	}

	public function install ()
	{
		$this->get_loader()->create_PDO_Tables();
	}

	public function get_status (): bool
	{
		return $this->status;
	}

	public function is_load (): bool
	{
		return $this->is_load;
	}

	public function load_complete (): bool
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

	public function load_ajax (): bool
	{
		return $this->get_loader()->load_ajax_file();
	}

	public function get_version (): string
	{
		return $this->version;
	}
	public function get_entity_file():string {
		return $this->get_loader()->get_entity_file();
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

	/**
	 * @return string
	 */
	public function get_author ():string
	{
		return $this->author;
	}

	/**
	 * @return mixed
	 */
	public function get_display_name ()
	{
		return $this->displayname;
	}
}