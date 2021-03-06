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
/**
 * @author Marcel
 *
 */
namespace iko;
error_reporting(E_ALL);

/**
 * @author Marcel
 *
 */
class Core
{
	const version = "1.0.0a";
	public static $basepath;
	public static $adminpath;
	public static $corepath;
	public static $modulepath;
	public static $currentfile;
	private static $PDO;
	public static $templatepath;
	public static $config;

	/**
	 * @param int $phase
	 */
	public static function init ($phase)
	{
		switch ($phase) {
			case 0:
				self::load_paths();
			break;
			case 1:
				self::$config = config::load("file", self::$corepath . "config.php");
				session::init(0);
				self::load_PDO();
			break;
			case 2:
				module::init();
			break;
			default:
				NULL;
			break;
		}
	}

	/**
	 * @return string // Load the prefix like: ./../../
	 */
	private static function load_path_prefix ()
	{
		$base = explode("/", self::load_path_current());
		$dir = "./";
		if (count($base) > 1) {
			for ($i = 0; $i < count($base); $i++) {
				if ($base[ $i ] != "" && $i > 1) {
					$dir .= "../";
				}
			}
		}

		return $dir;
	}

	/**
	 * @return string // Load the Current File
	 */
	private static function load_path_current ()
	{
		$current_file = dirname(__FILE__); //Load Current Directory
		$win = FALSE;
		if (strpos($current_file, "\\") > 0) { //If there is \ for a Windows based System. It will replaced with /
			$win = TRUE;
		}
		if ($win == TRUE) {
			$current_file = strtolower(str_replace("\\", "/", $current_file)); // \ -> /
		}
		$base_without_doc_root = str_replace(strtolower($_SERVER['DOCUMENT_ROOT']), "",
			$current_file); // Remove Document root
		$base_without_core = str_replace("/core", "", $base_without_doc_root);

		return str_replace($base_without_core, "", strtolower($_SERVER['PHP_SELF']));
	}

	/**
	 * @param string  $attachment
	 * @param string  $type
	 * @param boolean $get
	 *
	 * @return string // Get the Path to the Current file or it will be generate a string for such other files.
	 *                // example: Current(./my/own/website/module/test/index.php);
	 *                // Core::get_Path("my/dir/name/myfile.php");
	 *                // Return: ./../../my/dir/name/myfile.php");
	 */
	public static function get_Path ($attachment = "", $type = "", $get = FALSE)
	{
		switch ($type) {
			case "admin":
				$base_path = Core::$adminpath;
			break;
			case "module":
				$base_path = Core::$modulepath;
			break;
			case "core":
				$base_path = Core::$corepath;
			break;
			default:
				$base_path = Core::$basepath;
			break;
		}
		$base_current_file = self::load_path_current();
		if ($attachment == "") {
			if ($get) {
				$gets = "?";
				foreach ($_GET as $key => $value) {
					$gets .= $key . "=" . $value . "&";
				}
				$base_current_file .= $gets;
			}
			$path = $base_path . substr($base_current_file, 1);
		}
		else {
			$path = $base_path . "" . $attachment;
		}

		return $path;
	}

	private static function load_paths ()
	{
		self::$basepath = self::load_path_prefix();
		self::$corepath = self::$basepath . "core/";
		self::$adminpath = self::$basepath . "admin/";
		self::$modulepath = self::$basepath . "module/";
		self::$templatepath = self::$basepath . "template/";
		self::$currentfile = self::get_Path();
	}

	/**
	 * Load PDO with /Core/Database.conf.php file over Class Config
	 */
	private static function load_PDO ()
	{
		$config = config::load("file", self::$corepath . "database.conf.php");
		try {
			self::$PDO = new PDO($config->get("dns"), $config->get("username"), $config->get("password"),
				$config->get("options"));
		}
		catch (\PDOException $ex) {
			echo $ex->getMessage() . "<br>";
			exit;
		}

	}

	public static function date_format ()
	{
		$config = config::load("PDO", "iko");

		return $config->date_format;
	}

	public static function file_req ($file)
	{
		if (!defined($file)) {
			define($file, 1);

			/** @noinspection PhpIncludeInspection */
			return require($file);
		}
		else {
			return true;
		}
	}

	public static function file_incl ($file)
	{
		if (!defined($file)) {
			define($file, 1);

			/** @noinspection PhpIncludeInspection */
			return include($file);
		}
		else {
			return true;
		}
	}
	public static function PDO():\PDO {
		return self::$PDO;
	}
	public static function Config():config {
		return self::$config;
	}
}

/**
 *  Load Phase 1
 */

Core::init(0);

/**
 *  Load Config Loader
 */
//require_once Core::$corepath . "permission/module.class.php";
Core::file_req(Core::$corepath . "lib.php");
Core::file_req(Core::$corepath . "log.class.php");
Core::file_req(Core::$corepath . "exception.class.php");
Core::file_req(Core::$corepath . "pdo.class.php");
Core::file_req(Core::$corepath . "functions.php");
Core::file_req(Core::$corepath . "load_event.php");
Core::file_req(Core::$corepath . 'load_config.php');
Core::file_req(Core::$corepath . "sessions.php");



/**
 *  Load Phase 2
 */

Core::init(1);
Core::file_req(Core::$corepath . "module.class.php");
Core::file_req(Core::$corepath . "module_loader.class.php");
function my_autoloader ($class)
{
	$name = str_replace("iko\\", "", $class);
	$explode = explode("\\", $name);
	module::request($explode[0]);
}

spl_autoload_register("Iko\\my_autoloader");
Core::init(2);
