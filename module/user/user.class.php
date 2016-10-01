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
	const table = "{prefix}user";

	private static $cache = array ();
	private static $cache_exist = array ();
	public static function get ($user_id = 0, $reload = FALSE)
	{
		if ($user_id != 0 && $user_id != NULL) {
			if (is_array($user_id)) {
				self::exist($user_id);
			}
			if (is_string($user_id) || is_int($user_id)) {
				$user_id = array ($user_id);
			}
			$user_array = array ();
			foreach ($user_id as $id) {
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

	private $id;
	private $user_name;
	private $user_password;
	private $user_email;
	private $user_avatar_id;
	private $user_signature;
	private $user_about_user;
	private $user_location_id;
	private $user_gender;
	private $user_date_joined;
	private $user_birthday;
	private $user_chosen_template_id;
	private $user_timezone_id;
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
			$statement = Core::$PDO->query("SELECT * FROM {prefix}user WHERE user_id = $user_id");
			$fetch = $statement->fetch(PDO::FETCH_ASSOC);
			$this->id = $fetch["user_id"];
			$this->user_name = $fetch["user_name"];
			$this->user_password = $fetch["user_password"];
			$this->user_email = $fetch["user_email"];
			$this->user_avatar_id = $fetch["user_avatar_id"];
			$this->user_signature = $fetch["user_signature"];
			$this->user_about_user = $fetch["user_about_user"];
			$this->user_location_id = $fetch["user_location_id"];
			$this->user_gender = $fetch["user_gender"];
			$this->user_date_joined = $fetch["user_date_joined"];
			$this->user_birthday = $fetch["user_birthday"];
			$this->user_chosen_template_id = $fetch["user_chosen_template_id"];
			$this->user_timezone_id = $fetch["user_timezone_id"];
			$this->load_groups();
			$this->permission = Permissions::get($this);
		}
		else {
			throw new Exception("User does not exist: User_ID = " . $user_id . "");
		}
	}

	/**
	 * @return mixed
	 */
	public function get_Id ()
	{
		return $this->id;
	}

	private $groups = array ();
	private $groups_all = array();
	/**
	 *
	 */
	private function load_groups ()
	{
		$statement = Core::$PDO->prepare("SELECT * FROM " . Permissions::user_assignment . " WHERE user_assignment_id_user = " . $this->get_ID(), array (PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$statement->execute();
		while ($fetch = $statement->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
			$group = group::get($fetch["user_assignment_id_group"]);
			if(array_search($group, $this->groups)) {
				array_push($this->groups, $group);
			}
			if(array_search($group, $this->groups_all)) {
				array_push($this->groups_all, $group);
				$this->load_groups_recursive($group);
			}
		}
	}
	private function load_groups_recursive($group) {
		$parent_list = $group->get_Parents();
		foreach ($parent_list as $item) {
			if(array_search($item, $this->groups_all) === false) {
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
		return $this->user_name;
	}
}