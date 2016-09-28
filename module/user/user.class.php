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

class user
{
	const table = "{prefix}user";

	private static $users = array ();
	private static $users_exist = array ();

	/**
	 * @param int  $user_id
	 * @param bool $reload
	 *
	 * @return array|mixed|null
	 */
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
				if (!isset(self::$users[ $id ]) || self::$users[ $id ] == NULL || $reload) {
					if (self::exist($id, $reload)) {
						self::$users[ $id ] = new user($id);
						array_push($user_array, self::$users[ $id ]);
					}
				}
				else {
					array_push($user_array, self::$users[ $id ]);
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
				if (!isset(self::$users_exist[ $user_id ]) || $reload) {
					$statement = Core::$PDO->prepare("SELECT user_id FROM " . self::table . " WHERE user_id = :user_id");
					$statement->bindParam('user_id', $user_id);
					$statement->execute();
					if ($statement->rowCount() > 0) {
						self::$users_exist[ $user_id ] = TRUE;

						return TRUE;
					}
					else {
						self::$users_exist[ $user_id ] = FALSE;

						return FALSE;
					}
				}

				return self::$users_exist[ $user_id ];
			}
			else {
				if (is_array($user_id)) {
					$statement = Core::$PDO->prepare("SELECT user_id FROM " . self::table . " WHERE user_id = :user_id");
					foreach ($user_id as $id) {
						if (!isset(self::$users_exist[ $id ]) || $reload) {
							$statement->bindParam('user_id', $id);
							$statement->execute();
							if ($statement->rowCount() > 0) {
								self::$users_exist[ $id ] = TRUE;
							}
							else {
								self::$users_exist[ $id ] = FALSE;
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

	private $groups = array ();

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
			load_groups();
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

	/**
	 *
	 */
	private function load_groups ()
	{
		$statement = Core::$PDO->prepare("SELECT * FROM " . permission::user_assignment . " WHERE user_assignment_id_user = " . $this->get_ID(), array (PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
		$statement->execute();
		while ($fetch = $statement->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
			array_push($this->groups, group::get($fetch["user_assignment_id_group"]));
		}
	}
	public function get_groups() {
		return $this->groups;
	}

	/**
	 * @return mixed
	 */
	public function get_user_name ()
	{
		return $this->user_name;
	}
}