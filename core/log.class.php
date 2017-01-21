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
 * Created by PhpStorm.
 * User: Marcel
 * Date: 12.11.2016
 * Time: 21:36
 */
namespace iko;

use iko\lib\multiton\cache_int;

class log extends cache_int
{
	const table = "{prefix}log";
	const id = "log_id";
	private static $int_to_string = array (
		"info",
		"error",
		"alert",
		"warning");
	private static $string_to_int = array (
		"info"    => 0,
		"error"   => 1,
		"alert"   => 2,
		"warning" => 3);

	public static function add ($module, $type, $code, $msg, $extra = NULL): bool
	{
		$time = time();
		if ($module != NULL && (array_search($type, self::$int_to_string) || array_search($type,
					self::$string_to_int)) && is_numeric($code) && trim($msg) != ""
		) {
			if (is_string($type)) {
				$type = self::$string_to_int[ strtolower($type) ];
			}
			if ($extra !== NULL) {
				$extra = serialize($extra);
			}
			$sql = "INSERT INTO " . self::table . " (module_name, log_type, log_code, log_message, log_time, log_extra) VALUES('$module', '$type', '$code', :log_message, '$time', :log_extra )";
			$statement = Core::PDO()->prepare($sql);
			$statement->bindParam(":log_message", $msg);
			$statement->bindParam(":log_extra", $extra);
			$statement->execute();
			if ($statement !== FALSE) {
				return TRUE;
			}
		}

		return FALSE;
	}

	protected static $cache = array ();
	protected static $cache_exist = array ();

	public static function get ($id = 0, $reload = FALSE):log
	{
		return parent::get($id, $reload);
	}

	public static function get_last_log ($LIMIT = 1)
	{
		return self::search(array (), FALSE, "ORDER BY log_id DESC LIMIT 0," . $LIMIT);
	}

	private $id;
	private $module;
	private $type;
	private $code;
	private $message;
	private $time;
	private $extra;

	public function __construct ($id)
	{
		$id = Core::PDO()->quote($id);
		$sql = "SELECT * FROM " . self::table . " WHERE " . self::id . " = " . $id;
		$statement = Core::PDO()->query($sql);
		$fetch = $statement->fetch(PDO::FETCH_ASSOC);
		foreach ($fetch as $key => $value) {
			$temp_key = str_replace("log_", "", $key);
			$temp_key = str_replace("_name", "", $temp_key);
			$this->{$temp_key} = $value;
		}
	}

	public function get_id ()
	{
		return $this->id;
	}

	public function get_module ():module
	{
		if(!$this->module instanceof module) {
			$this->module = module::get($this->module);
		}
		return $this->module;
	}

	public function get_module_name ()
	{
		return $this->get_module()->get_name();
	}

	public function get_type ($string = FALSE)
	{
		if ($string) {
			return self::$int_to_string[ $this->type ];
		}
		else {
			return $this->type;
		}
	}

	public function get_message ()
	{
		return $this->message;
	}

	public function get_extra ()
	{
		try {
			$result = unserialize($this->extra);
		}
		catch (Exception $ex) {
			$result = $this->extra;
		}

		return $result;
	}

	public function get_time ()
	{
		return $this->time;
	}

	public function get_date ()
	{
		return date(Core::date_format(), $this->get_time());
	}

	public function __toString ()
	{
		return ucfirst($this->get_type(TRUE)) . " | " . $this->message . " | " . $this->get_date() . "";
	}

	/**
	 * @return mixed
	 */
	public function get_code ()
	{
		return $this->code;
	}
}

?>