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

class Value
{
	private static $cache = array ();
	private static $cache_exist = array ();
	public static function get ($value = 0, $reload = FALSE)
	{
		if ($value != 0 && $value != NULL) {
			if (is_array($value)) {
				self::exist($value);
			}
			if (is_string($value) || is_int($value)) {
				$value = array ($value);
			}
			$user_array = array ();
			foreach ($value as $id) {
				if (!isset(self::$cache[ $id ]) || self::$cache[ $id ] == NULL || $reload) {
					if (self::exist($id, $reload)) {
						self::$cache[ $id ] = new __CLASS__($id);
						array_push($user_array, self::$cache[ $id ]);
					}
				}
				else {
					array_push($user_array, self::$cache[ $id ]);
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
	 * @param int  $user_id
	 * @param bool $reload
	 *
	 * @return bool|mixed
	 */
	public static function exist ($user_id = 0, $reload = FALSE)
	{
		if ($user_id != 0 && $user_id != NULL) {
			if (is_string($user_id) || is_int($user_id)) {
				if (!isset(self::$cache_exist[ $user_id ]) || $reload) {
					$statement = Core::$PDO->prepare("SELECT user_id FROM " . self::table . " WHERE user_id = :user_id");
					$statement->bindParam('user_id', $user_id);
					$statement->execute();
					if ($statement->rowCount() > 0) {
						self::$cache_exist[ $user_id ] = TRUE;

						return TRUE;
					}
					else {
						self::$cache_exist[ $user_id ] = FALSE;

						return FALSE;
					}
				}

				return self::$cache_exist[ $user_id ];
			}
			else {
				if (is_array($user_id)) {
					$statement = Core::$PDO->prepare("SELECT user_id FROM " . self::table . " WHERE user_id = :user_id");
					foreach ($user_id as $id) {
						if (!isset(self::$cache_exist[ $id ]) || $reload) {
							$statement->bindParam('user_id', $id);
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
}