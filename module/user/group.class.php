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

class group
{
	const table = "{prefix}user_groups";

	private static $groups = array ();
	private static $groups_exist = array ();

	/**
	 * @param      $group_id
	 * @param bool $reload
	 *
	 * @return array|mixed
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
				if (!isset(self::$groups[ $id ]) || self::$groups[ $id ] == NULL || $reload) {
					if (self::exist($id, $reload)) {
						self::$groups[ $id ] = new group($id);
						array_push($group_array, self::$groups[ $id ]);
					}
				}
				else {
					array_push($group_array, self::$groups[ $id ]);
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
	 * @return bool|mixed
	 */
	public static function exist ($group_id, $reload = FALSE)
	{
		if ($group_id != 0 && $group_id != NULL) {
			if (is_string($group_id) || is_int($group_id)) {
				if (!isset(self::$groups_exist[ $group_id ]) || $reload) {
					$statement = Core::$PDO->prepare("SELECT group_id FROM " . self::table . " WHERE group_id = :group_id");
					$statement->bindParam('group_id', $group_id);
					$statement->execute();
					if ($statement->rowCount() > 0) {
						self::$groups_exist[ $group_id ] = TRUE;

						return TRUE;
					}
					else {
						self::$groups_exist[ $group_id ] = FALSE;

						return FALSE;
					}
				}

				return self::$groups_exist[ $group_id ];
			}
			else {
				if (is_array($group_id)) {
					$statement = Core::$PDO->prepare("SELECT group_id FROM " . self::table . " WHERE group_id = :group_id");
					foreach ($group_id as $id) {
						if (!isset(self::$groups_exist[ $id ]) || $reload) {
							$statement->bindParam('group_id', $id);
							$statement->execute();
							if ($statement->rowCount() > 0) {
								self::$groups_exist[ $id ] = TRUE;
							}
							else {
								self::$groups_exist[ $id ] = FALSE;
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
	private $group_parents;

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
		}
	}

	/**
	 *
	 */
	private function load_parent() {
		$statement = Core::$PDO->query("SELECT * FROM ");
	}
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


}