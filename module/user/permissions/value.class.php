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
namespace Iko\Permissions;

use Iko\Permissions;

class Value
{
	const table = Permissions::table;
	private static $cache = array ();
	private static $cache_exist = array ();
	public static function get ($value = 0, $reload = FALSE)
	{
		if ($value != 0 && $value != NULL) {
			if (is_array($value)) {
				self::exist($value);
			}
			if (is_string($value)) {
				$value = array ($value);
			}
			$user_array = array ();
			foreach ($value as $name) {
				if (!isset(self::$cache[ $name ]) || self::$cache[ $name ] == NULL || $reload) {
					if (self::exist($name, $reload)) {
						$class = str_replace(__NAMESPACE__ . "/", "", __CLASS__);
						self::$cache[ $name ] = new $class($name);
						array_push($user_array, self::$cache[ $name ]);
					}
				}
				else {
					array_push($user_array, self::$cache[ $name ]);
				}
			}
			if (count($user_array) == 1) {
				return $user_array[0];
			}
			else {
				return $user_array;
			}
		}

		return NULL;
	}

	public static function search ($args = array (), $or = FALSE) // TODO: Complete Function for Searching after single and Mutliple user
	{
		$sql = "SELECT user_id FROM " . self::table . " WHERE";
		$equal = ($or) ? "OR" : "AND";
		if (count($args) > 0) {
			$string = "";
			foreach ($args as $key => $var) {
				if (is_string($var)) {
					$string .= ' ' . $key . ' LIKE "' . $var . '"';
				}
				if (is_int($var)) {
					$string .= ' ' . $key . ' = ' . $var . '"';
				}
				if (is_bool($var)) {
					$var = intval($var);
					$string .= ' ' . $key . ' = ' . $var . '"';
				}
			}
		}
	}

	/**
	 * @param int  $names
	 * @param bool $reload
	 *
	 * @return bool|mixed
	 */
	public static function exist ($names = 0, $reload = FALSE)
	{
		if ($names != 0 && $names != NULL) {
			$statement = Core::$PDO->prepare("SELECT * FROM " . self::table . " WHERE permission_name = :perm_name");
			if (is_string($names) || is_int($names)) {
				if (!isset(self::$cache_exist[ $names ]) || $reload) {
					$statement->bindParam(':perm_name', $names);
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
							$statement->bindParam(':perm_name', $name);
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


	private $name;
	private $module;

	protected function __construct ($name)
	{
		if (is_string($name) && self::exist($name)) {

		}
	}

	public function get_name ()
	{
		return $this->name;
	}
}