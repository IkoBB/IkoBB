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
namespace Iko;

class User
{
	const table = "{prefix}users";
	const id = "user_id";
	private static $cache = array ();
	private static $cache_exist = array ();

	public static function get ($user_id = 0, $reload = FALSE)
	{
		if (is_string($user_id)) {
			return self::search(array ("user_name" => $user_id));
		}
		if (is_array($user_id) || is_int($user_id)) {
			if (is_array($user_id)) {
				self::exist($user_id);
			}
			if (is_int($user_id)) {
				$user_id = array ($user_id);
			}
			$user_array = array ();
			foreach ($user_id as $id) {
				if (!isset(self::$cache[ $id ]) || self::$cache[ $id ] == NULL || $reload) {
					if (self::exist($id, $reload)) {
						$class = str_replace(__NAMESPACE__ . "/", "", __CLASS__);
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

	public static function search ($args = array (), $or = FALSE) // TODO: Complete Function for Searching after single and Mutliple user
	{
		$sql = "SELECT " . self::id . " FROM " . self::table . " WHERE";
		$equal = ($or) ? "OR" : "AND";
		if (count($args) > 0) {
			$i = count($args);
			$string = "";
			foreach ($args as $key => $var) {
				if (is_string($var)) {
					$string .= ' ' . $key . " = '" . $var . "'";
				}
				if (is_int($var)) {
					$string .= ' ' . $key . ' = ' . $var . '';
				}
				if (is_bool($var)) {
					$var = intval($var);
					$string .= ' ' . $key . ' = ' . $var . '';
				}
				if ($i > 1) {
					$string .= " " . $equal;
				}
			}
			$sql .= $string;
		}
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
	 * @param int  $user_id
	 * @param bool $reload
	 *
	 * @return bool|mixed
	 */
	public static function exist ($user_id = 0, $reload = FALSE)
	{
		if ($user_id != 0 && $user_id != NULL) {
			$statement = Core::$PDO->prepare("SELECT user_id FROM " . self::table . " WHERE " . self::id . " = :user_id");
			if (is_string($user_id) || is_int($user_id)) {
				if (!isset(self::$cache_exist[ $user_id ]) || $reload) {
					$statement->bindParam(':user_id', $user_id);
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
					foreach ($user_id as $id) {
						if (!isset(self::$cache_exist[ $id ]) || $reload) {
							$statement->bindParam(':user_id', $id);
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

	public static function session ()
	{

	}

	private $id;
	private $name;
	private $password;
	private $email;
	private $avatar_id;
	private $signature;
	private $about_user;
	private $location_id;
	private $gender;
	private $date_joined;
	private $birthday;
	private $chosen_template_id;
	private $timezone_id;
	private $permission;


	/**
	 * user constructor.
	 *
	 * @param $user_id
	 *
	 * @throws \Iko\Exception
	 */
	protected function __construct ($user_id)
	{
		if (self::exist($user_id)) {
			$statement = Core::$PDO->query("SELECT * FROM " . self::table . " WHERE " . self::id . " = $user_id");
			$fetch = $statement->fetch(PDO::FETCH_ASSOC);
			foreach ($fetch as $key => $value) {
				$temp_key = str_replace("user_", "", $key);
				$this->{$temp_key} = $value;
			}
			$this->load_groups();
			//$this->permission = Permissions::get($this);
			/*if (!$this->permission instanceof Permissions) {
				throw new Exception("Permission konnte nicht aufgerufen werden.");
			}*/
		}
		else {
			throw new Exception("User does not exist: User_ID = " . $user_id . "");
		}
	}

	/**
	 * @return integer
	 */
	public function get_Id ()
	{
		return intval($this->id);
	}

	private $groups = array ();
	private $groups_all = array ();

	/**
	 *
	 */
	private function load_groups ()
	{
		$sql = "SELECT * FROM " . Permissions::user_assignment . " WHERE " . self::id . " = " . $this->get_ID();
		$statement = Core::$PDO->query($sql);
		$fetch_all = $statement->fetchAll(PDO::FETCH_ASSOC);
		foreach ($fetch_all as $fetch) {
			$group = group::get($fetch[ Group::id ]);
			if (array_search($group, $this->groups, TRUE) === FALSE) {
				array_push($this->groups, $group);
			}
			if (array_search($group, $this->groups_all, TRUE) === FALSE) {
				array_push($this->groups_all, $group);
				$this->load_groups_recursive($group);
			}
		}
	}

	private function load_groups_recursive ($group)
	{
		$parent_list = $group->get_Parents();
		foreach ($parent_list as $item) {
			if (array_search($item, $this->groups_all, TRUE) === FALSE) {
				array_push($this->groups_all, $item);
				$this->load_groups_recursive($item);
			}
		}
	}

	/**
	 * @return mixed
	 */
	public function get_user_name ()
	{
		return $this->name;
	}

	public function get_groups ()
	{
		return $this->groups;
	}

	public function get_Permission ()
	{
		return $this->permission;
	}
}