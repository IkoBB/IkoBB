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
 * Date: 02.01.2017
 * Time: 02:10
 */
namespace iko\lib\multiton;

use iko\Core;
use iko\Exception;

class cache
{
	public static function search ($args = array (), $or = FALSE, $suffix = "")
	{
		$class = get_called_class();
		$reflection = new \ReflectionClass($class);
		if ($reflection->getConstant("id") != FALSE) {
			$table_id = $reflection->getConstant("id");
		}
		else if ($reflection->getConstant("name") != FALSE) {
			$table_id = $reflection->getConstant("name");
		}
		else {
			throw new Exception("#0002 Class needs const id or name");
		}
		$sql = "SELECT " . $table_id . " FROM " . $class::table . "";
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
		$statement = Core::$PDO->query($sql);
		if ($statement !== FALSE) {
			$fetch = $statement->fetch();
			$value = $class::get($fetch[ $table_id ]);
			if ($value !== NULL) {
				return $value;
			}
			else {
				return FALSE;
			}
		}
		else {
			return FALSE;
		}
	}

	public static function searches ($args = array (), $or = FALSE, $suffix = "")
	{
		$class = get_called_class();
		$reflection = new \ReflectionClass($class);
		if ($reflection->getConstant("id") != FALSE) {
			$table_id = $reflection->getConstant("id");
		}
		else if ($reflection->getConstant("name") != FALSE) {
			$table_id = $reflection->getConstant("name");
		}
		else {
			throw new Exception("#0002 Class needs const id or name");
		}
		$sql = "SELECT " . $table_id . " FROM " . $class::table . "";
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
				array_push($ids, $fetch[ $table_id ]);
			}
			$array = $class::gets($ids);

			if (count($array) == 0) {
				return FALSE;
			}

			return $array;
		}
		else {
			return FALSE;
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
		$reflection = new \ReflectionClass($class);
		if ($reflection->getConstant("id") != FALSE) {
			$table_id = $reflection->getConstant("id");
		}
		else if ($reflection->getConstant("name") != FALSE) {
			$table_id = $reflection->getConstant("name");
		}
		else {
			throw new Exception("#0002 Class needs const id or name");
		}
		if (isset($class::$cache_exist)) {
			if ($ids !== NULL) {
				$statement = Core::$PDO->prepare("SELECT " . $table_id . " FROM " . $class::table . " WHERE " . $table_id . " = :ids");
				if (is_string($ids) || is_int($ids)) {
					if (!isset($class::$cache_exist[ $ids ]) || $reload) {
						$statement->bindParam(':ids', $ids);
						$statement->execute();
						if ($statement->rowCount() > 0) {
							$class::$cache_exist[ $ids ] = TRUE;

							return TRUE;
						}
						else {
							$class::$cache_exist[ $ids ] = FALSE;

							return FALSE;
						}
					}

					return $class::$cache_exist[ $ids ];
				}
				else {
					if (is_array($ids)) {
						foreach ($ids as $id) {
							if (!isset($class::$cache_exist[ $id ]) || $reload) {
								$statement->bindParam(':ids', $id);
								$statement->execute();
								if ($statement->rowCount() > 0) {
									$class::$cache_exist[ $id ] = TRUE;
								}
								else {
									$class::$cache_exist[ $id ] = FALSE;
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
		else {
			return TRUE;
		}
	}
}