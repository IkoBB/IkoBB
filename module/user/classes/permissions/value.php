<?php
/**
 *
 * This file is part of IkoBB Forum and belongs to the module <User>.
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
 * Date: 30.09.2016
 * Time: 22:09
 */
namespace Iko\user\permissions;

use Iko\Permissions;
use Iko\Core as Core;
use Iko\PDO as PDO;

class Value
{
	const table = Permissions::table;
	const name = Permissions::name;

	private static $cache = array ();
	private static $cache_exist = array ();

	public static function get ($ids = 0, $reload = FALSE)
	{
		$class = get_called_class();
		if (is_array($ids) || is_string($ids)) {
			if (is_array($ids)) {
				self::exist($ids);
			}
			if (is_string($ids)) {
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
		$sql = "SELECT " . self::name . " FROM " . self::table . "";
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
			}
			$i--;
			$sql .= $string;
		}
		$sql .= " " . $suffix;
		$ids = array ();
		$statement = Core::$PDO->query($sql);
		if ($statement !== FALSE) {
			$fetch_all = $statement->fetchAll();
			foreach ($fetch_all as $fetch) {
				array_push($ids, $fetch[ self::name ]);
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
		if ($ids !== NULL) {
			$statement = Core::$PDO->prepare("SELECT " . self::name . " FROM " . self::table . " WHERE " . self::name . " = :ids");
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


	private $name;
	private $module;
	private $comment;

	protected function __construct ($name)
	{
		if (is_string($name) && self::exist($name)) {
			$sql = "SELECT * FROM " . self::table . " WHERE " . self::name . " = '" . $name . "'";
			$statement = Core::$PDO->query($sql);
			$fetch = $statement->fetch(PDO::FETCH_ASSOC);
			foreach ($fetch as $key => $value) {
				$temp_key = str_replace("permission_", "", $key);
				$temp_key = str_replace("_name", "", $temp_key);
				$this->{$temp_key} = $value;
			}
		}
	}

	public function get_name ()
	{
		return $this->name;
	}

	public function get_module () {
		return $this->module;
	}

	public function get_comment ()
	{
		return $this->comment;
	}

	public function __toString ()
	{
		return $this->get_name();
	}
}