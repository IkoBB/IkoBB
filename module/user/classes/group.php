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
 * Date: 28.09.2016
 * Time: 19:47
 */
namespace iko\user;

use iko\Core;
use iko\PDO;

class Group extends operators implements iGroup //Todo: Complete
{
	const table = "{prefix}usergroups";
	const assignment = Permissions::group_assignment;
	const id = "usergroup_id";
	const name = "usergroup_name";

	public static $cache = array ();
	public static $cache_exist = array ();

	/*public static function get ($ids = 0, $reload = FALSE)
	{
		$class = get_called_class();
		if (is_string($ids) && !is_numeric($ids)) {
			return self::search(array (self::name => $ids));
		}
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
			$user_array = array ();
			foreach ($ids as $id) {
				if (!isset(self::$cache[ $id ]) || self::$cache[ $id ] == NULL || $reload) {
					if (self::exist($id, $reload)) {
						self::$cache[ $id ] = new $class($id);
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
			return FALSE;
		}
	}*/

	/**
	 * @param int  $ids
	 * @param bool $reload
	 *
	 * @return bool|mixed
	 */
	/*
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
	}*/

	private $id;
	private $name;
	private $style;
	private $group_rang;
	private $group_parents = array ();

	/**
	 * group constructor.
	 *
	 * @param $group_id
	 */
	protected function __construct ($group_id)
	{
		if (self::exist($group_id)) {
			$statement = Core::$PDO->query("SELECT * FROM " . self::table . " WHERE " . self::id . " = " . $group_id);
			$fetch = $statement->fetch(PDO::FETCH_ASSOC);
			foreach ($fetch as $key => $value) {
				$temp_key = str_replace("usergroup_", "", $key);
				$this->{$temp_key} = $value;
			}
			$this->load_parents();

		}
	}

	/**
	 *
	 */
	private function load_parents ()
	{
		$this->group_parents = array ();
		$sql = "SELECT * FROM " . self::assignment . " WHERE child_group_id = " . $this->get_id();
		$statement = Core::$PDO->query($sql);
		if ($statement !== FALSE) {
			foreach ($statement->fetchAll() as $row) {
				$parent = self::get($row["parent_group_id"]);
				if (array_search($parent, $this->group_parents, TRUE) === FALSE) {
					array_push($this->group_parents, $parent);
				}
				$this->load_parents_recursive($parent);
			}
		}
	}

	private function load_parents_recursive ($group)
	{
		$parent_list = $group->get_Parents();
		foreach ($parent_list as $item) {
			if (array_search($item, $this->group_parents) === FALSE) {
				array_push($this->group_parents, $item);
				$this->load_parents_recursive($item);
			}
		}
	}
	/*
	 * Parent | Child
	 * Gast   | Member
	 * Member | VIP
	 * Member | Moderator
	 * Test   | Moderator
	 *
	 */
	/**
	 * @return mixed
	 */
	public function get_id ()
	{
		return intval($this->id);
	}

	/**
	 * @return mixed
	 */
	public function get_Rang ()
	{
		return $this->rang;
	}

	/**
	 * @param mixed $group_rang
	 */
	public function set_Rang ($group_rang)
	{
		$this->group_rang = $group_rang;
	}

	public function get_Parents ()
	{
		return $this->group_parents;
	}

	public function get_Style ()
	{
		return $this->style;
	}


	/*public function get_Displayname ()
	{
		return $this->style;
	}*/
}