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
namespace Iko;

class Group
{
	const table = "{prefix}user_groups";
	const assignment = permission::group_assignment;

	private static $cache = array ();
	private static $cache_exist = array ();

	/**
	 * @param      $group_id
	 * @param bool $reload
	 *
	 * @return array|group
	 */
	public static function get ($group_id, $reload = FALSE)
	{
		if ($group_id != 0 && $group_id != NULL) {
			if (is_array($group_id)) {
				self::exist($group_id);
			}
			if (is_string($group_id) || is_int($group_id)) {
				$group_id = array ($group_id);
			}
			$group_array = array ();
			foreach ($group_id as $id) {
				if (!isset(self::$cache[ $id ]) || self::$cache[ $id ] == NULL || $reload) {
					if (self::exist($id, $reload)) {
						$class = str_replace(__NAMESPACE__ . "/", "", __CLASS__);
						self::$cache[ $id ] = new $class($id);
						array_push($group_array, self::$cache[ $id ]);
					}
				}
				else {
					array_push($group_array, self::$cache[ $id ]);
				}
			}
			if (count($group_array) == 1) {
				return $group_array[0];
			}
			else {
				return $group_array;
			}
		}
	}

	public static function search ()
	{

	}

	/**
	 * @param      $group_id
	 * @param bool $reload
	 *
	 * @return bool
	 */
	public static function exist ($group_id, $reload = FALSE)
	{
		if ($group_id != 0 && $group_id != NULL) {
			$statement = Core::$PDO->prepare("SELECT group_id FROM " . self::table . " WHERE group_id = :group_id");
			if (is_string($group_id) || is_int($group_id)) {
				if (!isset(self::$cache_exist[ $group_id ]) || $reload) {
					$statement->bindParam(':group_id', $group_id);
					$statement->execute();
					if ($statement->rowCount() > 0) {
						self::$cache_exist[ $group_id ] = TRUE;

						return TRUE;
					}
					else {
						self::$cache_exist[ $group_id ] = FALSE;

						return FALSE;
					}
				}

				return self::$cache_exist[ $group_id ];
			}
			else {
				if (is_array($group_id)) {
					foreach ($group_id as $id) {
						if (!isset(self::$cache_exist[ $id ]) || $reload) {
							$statement->bindParam(':group_id', $id);
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


	private $id;
	private $group_name;
	private $group_style;
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
			$statement = Core::$PDO->query("SELECT * FROM " . self::table . " WHERE group_id = " . $group_id);
			$fetch = $statement->fetch();
			$this->id = $fetch["group_id"];
			$this->group_name = $fetch["group_name"];
			$this->group_style = $fetch["group_style"];
			$this->group_rang = $fetch["group_rang"];


			$this->load_parents();
		}
	}

	/**
	 *
	 */
	private function load_parents ()
	{
		$this->group_parents = array ();
		$sql = "SELECT * FROM " . self::assignment . " LEFT JOIN " . self::table . " WHERE group_id = group_parent_id AND group_child_id = :group_id ORDER BY rank";
		$statement = Core::$PDO->prepare($sql, array (PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$statement->bindParam(":group_id", $this->get_Id());
		$statement->execute();
		foreach ($statement->fetchAll() as $row) {
			$parent = self::get($row["group_parent_id"]);
			if (array_search($parent, $this->group_parents) === FALSE) {
				array_push($this->group_parents, $parent);
			}
			$this->load_parents_recursive($parent);
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
	public function get_Id ()
	{
		return $this->id;

	}

	/**
	 * @return mixed
	 */
	public function get_Rang ()
	{
		return $this->group_rang;
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

}