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

abstract class module_loader
{
	private $class_module;
	public $checked = FALSE;
	public $is_load = FALSE;
	protected $final_load = NULL;
	protected $depend_on = array ();
	protected $handler_file = "";
	protected $handler = array ();
	protected $handler_final = array ();

	/*
	 * array(array($this->class_module, "class", "function", "instance", "is_func_static", "can_init_over"));
	 */
	public function __construct ($module)
	{
		$this->class_module = $module;
	}

	/**
	 * @return \iko\module
	 */
	public function get_module (): module
	{
		return $this->class_module;
	}

	/**
	 * @return bool
	 */
	public function check (): bool
	{
		if ($this->pre_check_Files() && $this->pre_check_PDO_Tables()) {
			$this->checked = TRUE;
		}
		$result = TRUE;
		foreach ($this->depend_on as $item) {
			if (module::request($item) == FALSE) {
				$result = FALSE;
			}
		}

		return ($this->is_Checked() && $result) ? TRUE : FALSE;
	}

	/**
	 * @return bool
	 */
	public function is_Checked (): bool
	{
		return $this->checked;
	}

	abstract protected function pre_check_PDO_Tables ();

	abstract protected function pre_check_Files ();

	abstract protected function pre_load ();

	/**
	 * @param array $tables
	 *
	 * @return bool
	 */
	public function check_PDO_Tables (array $tables = array ()): bool
	{
		$result = TRUE;
		if (is_string($tables)) {
			$tables = array ($tables);
		}
		foreach ($tables as $var) {
			if (strpos($var, "{!prefix}") === FALSE) {
				if (strpos($var, "{prefix}") === FALSE) {
					$var = "{prefix}" . $var;
				}
			}
			else {
				$var = str_replace("{!prefix}", '', $var);
			}
			$query = "SELECT 1 FROM " . $var . " WHERE 1;";
			$sql = Core::$PDO->query($query);
			if ($sql === FALSE) {
				$result = FALSE;
			}
		}

		return $result;
	}

	function check_files_exist (array $array, string $prefix): bool
	{
		$result = TRUE;
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				if (!$this->check_files_exist($value, $prefix . $key . "/")) {
					$result = FALSE;
				}
			}
			else {
				if ($value == "*") {
					$dir = scandir($prefix);
					unset($dir[0], $dir[1]);
					$result = $this->check_files_exist($dir, $prefix);
				}
				else {
					$filename = $prefix . $value;
					if (!file_exists($filename) && !is_dir($filename)) {
						$result = FALSE;
					}
					else if (is_dir($filename)) {
						$result = $this->check_files_exist(array ("*"), $filename . "/");
					}
				}
			}
		}

		return $result;
	}

	public function check_Files ($files = array ()): bool
	{
		$result = TRUE;
		if (is_string($files)) {
			$files = array ($files);
		}
		if (is_array($files)) {
			$result = $this->check_files_exist($files, $this->class_module->get_path());
		}

		return $result;
	}

	function load_file (array $array, string $prefix)
	{
		foreach ($array as $key => $var) {
			if (is_array($var)) {
				$this->load_file($var, $prefix . $key . "/");
			}
			else {
				if ($var == "*") {
					$dir = scandir($prefix);
					unset($dir[0], $dir[1]);
					$this->load_file($dir, $prefix);
				}
				else {
					$filename = $prefix . $var;
					if (!file_exists($filename) && !is_dir($filename)) {
						throw new \Exception("Code #1236 " . $filename);
					}
					else if (is_dir($filename)) {
						$this->load_file(array ("*"), $filename . "/");
					}
					else {
						$include = include_once($filename);
						if ($include === FALSE) {
							throw new \Exception("Code #1236 " . $filename);
						}
					}
				}
			}
		}
	}

	public function load ($files = array (), bool $event_handler = FALSE)
	{
		if (!$this->is_load() && !$event_handler) {
			$this->is_load = TRUE;
			$this->pre_load();
		}
		if (is_string($files)) {
			$files = array ($files);
		}
		if (is_array($files)) {
			$this->load_file($files, $this->class_module->get_path());
		}

		return $this->is_load();
	}

	public function create_PDO_Tables ($args = array (), $file = FALSE)
	{
		$files = $args;
		if ($file) {
			$args = array ();
			$mode = "r";
			if (is_string($files)) {
				$files = array ($files);
			}
			foreach ($files as $var) {
				$filename = $this->class_module->get_path() . $var;
				if (!file_exists($filename)) {
					throw new \Exception("Code #1236 " . $filename);
				}
				$handle = fopen($filename, $mode);
				$string = "";
				while ($read = fgets($handle)) {
					$string .= $read;
				}
				fclose($handle);
				array_push($args, $string);
			}
		}
		if (is_string($args)) {
			$args = array ($args);
		}
		foreach ($args as $var) {
			$var = str_replace('`', '', $var);
			if (strpos($var, "create") !== FALSE || strpos($var, "CREATE") !== FALSE || strpos($var,
					"ALTER TABLE") !== FALSE || strpos($var, "alter table") !== FALSE
			) {
				$state = Core::$PDO->query($var);
				if ($state === FALSE) {
					throw new \Exception(Core::$PDO->errorInfo());
				}
			}
		}
	}

	protected $ajax_file = "ajax.php";

	protected function set_ajax_file ($value)
	{
		if ($this->check_Files($value)) {
			$this->ajax_file = $value;
		}
	}

	public function load_ajax_file (): bool
	{
		return $this->load($this->ajax_file);
	}

	public function is_load (): bool
	{
		return $this->is_load;
	}

	/**
	 * @param $callable
	 * @param $args
	 */
	public function final_load ()
	{
		if ($this->final_load != NULL) {
			if (is_string($this->final_load)) {
				$this->final_load = array ($this->final_load);
			}
			if (is_array($this->final_load)) {
				foreach ($this->final_load as $item) {
					if (is_callable($item)) {
						call_user_func($item);
					}
				}
			}
		}
	}

	private function add_event_handler_array ($array, $type)
	{
		if (is_array($array)) {
			foreach ($array as $item) {
				if (count($item) > 0) {
					if ($type == 1) {
						//           0         1      2       3       4                  5                       6
						//add_event ($module, $name, $class, $func, $instance = NULL, $isFuncStatic = FALSE, $canInitialOver = NULL)
						Event\Handler::add_event($item[0], $item[1], $item[2], $item[3], $item[4] ?? NULL,
							$item[5] ?? FALSE, $item[6] ?? NULL);
					}
					if ($type == 2) {
						Event\Handler::add_event_final($item[0], $item[1], $item[2], $item[3], $item[4] ?? NULL,
							$item[5] ?? FALSE, $item[6] ?? NULL);
					}
				}
			}
		}
	}

	public function event_handler_init ()
	{
		if (isset($this->handler_file) && $this->handler_file != NULL) {
			$this->load($this->handler_file, TRUE);
		}
		if (isset($this->handler) && is_array($this->handler)) {
			$this->add_event_handler_array(array ($this->handler), 1);
		}
		if (isset($this->handler) && is_array($this->handler)) {
			$this->add_event_handler_array(array ($this->handler_final), 2);
		}
	}
}