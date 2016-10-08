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

abstract class module_loader
{
	private $class_module;
	public $checked = FALSE;
	public $is_load = FALSE;

	public function __construct ($module)
	{
		$this->class_module = $module;
	}

	public function check ()
	{
		if ($this->pre_check_Files() && $this->pre_check_PDO_Tables()) {
			$this->checked = TRUE;
		}

		return $this->is_Checked();
	}

	public function is_Checked ()
	{
		return (bool)$this->checked;
	}

	abstract protected function pre_check_PDO_Tables ();

	abstract protected function pre_check_Files ();

	public function check_PDO_Tables ($tables = array ())
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

	function check_files_exist ($array, $prefix)
	{
		$result = TRUE;
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				if (!$this->check_files_exist($value, $prefix . $key . "/")) {
					$result = FALSE;
				}
			}
			else {
				$filename = $prefix . $value;
				if (!file_exists($filename)) {
					$result = FALSE;
				}
			}
		}

		return $result;
	}

	public function check_Files ($files = array ())
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

	function load_file ($array, $prefix)
	{
		foreach ($array as $key => $var) {
			if (is_array($var)) {
				$this->load_file($var, $prefix . $key . "/");
			}
			else {
				$filename = $prefix . $var;
				if (!file_exists($filename)) {
					throw new \Exception("Code #1236 " . $filename);
				}
				else {
					$include = include($filename);
					if ($include === FALSE) {
						throw new \Exception("Code #1236 " . $filename);
					}
				}
			}
		}
	}

	public function load ($files = array ())
	{
		if (is_string($files)) {
			$files = array ($files);
		}
		if (is_array($files)) {
			$this->load_file($files, $this->class_module->get_path());
			$this->is_load = TRUE;
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

	public function is_load ()
	{
		return $this->is_load;
	}
}