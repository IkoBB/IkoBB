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

class log
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

	public static function add ($module, $type, $code, $msg, $extra = NULL)
	{
		$time = time();
		if ($module != NULL && (array_search($type, self::$int_to_string) || array_search($type,
					self::$string_to_int)) && is_numeric($code) && trim($msg) != ""
		) {
			echo "ja";
			if (is_string($type)) {
				$type = self::$string_to_int[ strtolower($type) ];
			}
			if ($extra !== NULL) {
				$extra = serialize($extra);
			}
			$sql = "INSERT INTO " . self::table . " (module_name, log_type, log_code, log_message, log_time, log_extra) VALUES('$module', '$type', '$code', :log_message, '$time', :log_extra )";
			$statement = Core::$PDO->prepare($sql);
			$statement->bindParam(":log_message", $msg);
			$statement->bindParam(":log_extra", $extra);
			$statement->execute();
			if ($statement !== FALSE) {
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

	protected static $cache = array ();
	protected static $cache_exist = array ();

	public static function get ($ids = 0, $reload = FALSE)
	{
		$class = get_called_class();
		if (is_numeric($ids)) {
			$ids = intval($ids);
		}
		if (is_array($ids) || is_int($ids)) {
			if (is_array($ids)) {
				self::exist($ids);
			}
			if (is_int($ids)) {
				$ids = array ($ids);
			}
			$array = array ();
			foreach ($ids as $id) {
				if (!isset(self::$cache[ $id ]) || self::$cache[ $id ] == NULL || $reload) {
					if (self::exist($id, $reload)) {
						self::$cache[ $id ] = new $class($id);
						array_push($array, self::$cache[ $id ]);
					}
				}
				else {
					array_push($array, self::$cache[ $id ]);
				}
			}
			if (count($array) == 1) {
				return $array[0];
			}
			else {
				return $array;
			}
		}

		return NULL;
	}

	public static function search ($args = array (), $or = FALSE, $suffix = "") // TODO: Complete Function for Searching after single and Mutliple user
	{
		$class = get_called_class();
		$sql = "SELECT " . self::id . " FROM " . $class::table . "";
		$equal = ($or) ? "OR" : "AND";
		if (count($args) > 0) {
			$i = count($args);
			$string = " WHERE";
			foreach ($args as $key => $var) {
				if (is_array($var)) {
					foreach ($var as $operator => $value) {
						$string .= ' ' . $key . " " . $operator . " '" . $value . "'";
					}
				}
				else {
					$string .= ' ' . $key . " = '" . $var . "'";
				}
				if ($i > 1) {
					$string .= " " . $equal;
				}
				$i--;
			}
			$sql .= $string;
		}
		$sql .= " " . $suffix;
		$ids = array ();
		$statement = Core::$PDO->query($sql);
		if ($statement !== FALSE) {
			$fetch_all = $statement->fetchAll();
			foreach ($fetch_all as $fetch) {
				array_push($ids, intval($fetch[ self::id ]));
			}
			$user_array = self::get($ids);

			return $user_array;
		}
		else {
			return NULL;
		}
	}

	/**
	 * @param int  $ids
	 * @param bool $reload
	 *
	 * @return bool|mixed
	 */
	public static function exist ($ids = 0, $reload = FALSE)
	{
		$class = get_called_class();
		if ($ids != 0 && $ids != NULL) {
			$statement = Core::$PDO->prepare("SELECT " . $class::id . " FROM " . $class::table . " WHERE " . $class::id . " = :ids");
			if (is_string($ids) || is_int($ids)) {
				if (!isset(self::$cache_exist[ $ids ]) || $reload) {
					$statement->bindParam(':ids', $ids);
					$statement->execute();
					if ($statement->rowCount() > 0) {
						self::$cache_exist[ $ids ] = TRUE;

						return TRUE;
					}
					else {
						self::$cache_exist[ $ids ] = FALSE;

						return FALSE;
					}
				}

				return self::$cache_exist[ $ids ];
			}
			else {
				if (is_array($ids)) {
					foreach ($ids as $id) {
						if (!isset(self::$cache_exist[ $id ]) || $reload) {
							$statement->bindParam(':ids', $id);
							$statement->execute();
							if ($statement->rowCount() > 0) {
								self::$cache_exist[ $id ] = TRUE;
							}
							else {
								self::$cache_exist[ $id ] = FALSE;
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
		$id = Core::$PDO->quote($id);
		$sql = "SELECT * FROM " . self::table . " WHERE " . self::id . " = " . $id;
		$statement = Core::$PDO->query($sql);
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

	public function get_module ()
	{
		return $this->module;
	}

	public function get_module_name ()
	{
		return $this->get_module()->getName();
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
		return $this->msg;
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
		return ucfirst($this->get_type(TRUE)) . " | " . $this->msg . " | " . $this->get_date() . "";
	}
}

?>