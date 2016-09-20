<?php
namespace Iko;

class user
{
	private static $users = array ();
	private static $users_exist = array ();

	public static function get($user_id = 0)
	{
		if ($user_id != 0 && $user_id != null) {
			if (is_array($user_id)) {
				self::exist($user_id);
			}
			if (is_string($user_id) || is_int($user_id)) {
				$user_id = array ($user_id);
			}
			$user_array = array ();
			foreach ($user_id as $id) {
				if (!isset(self::$users[$id]) || self::$users[$id] == null) {
					if (self::exist($id)) {
						self::$users[$id] = new user($id);
						array_push($user_array, self::$users[$id]);
					}
				}
				else {
					array_push($user_array, self::$users[$id]);
				}
			}
			if (count($user_array) == 1) {
				return $user_array[0];
			}
			else {
				return $user_array;
			}
		}

		return null;
	}

	public static function search($args = array (), $or = false) // TODO: Complete Function for Searching after single and Mutliple user
	{
		$sql = "SELECT user_id FROM {prefix}user WHERE";
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

	public static function exist($user_id = 0, $reload = false)
	{
		if ($user_id != 0) {
			if (is_string($user_id) || is_int($user_id)) {
				if (!isset(self::$users_exist[$user_id]) || $reload) {
					$statement = Core::$PDO->prepare("SELECT user_id FROM {prefix}user WHERE user_id = :user_id");
					$statement->bindParam('user_id', $user_id);
					$statement->execute();
					if ($statement->rowCount() > 0) {
						self::$users_exist[$user_id] = true;

						return true;
					}
					else {
						self::$users_exist[$user_id] = false;

						return false;
					}
				}

				return self::$users_exist[$user_id];
			}
			else {
				if (is_array($user_id)) {
					$statement = Core::$PDO->prepare("SELECT user_id FROM {prefix}user WHERE user_id = :user_id");
					foreach ($user_id as $id) {
						if (!isset(self::$users_exist[$user_id]) || $reload) {
							$statement->bindParam('user_id', $id);
							$statement->execute();
							if ($statement->rowCount() > 0) {
								self::$users_exist[$id] = true;
							}
							else {
								self::$users_exist[$id] = false;
							}
						}
					}

					return true;
				}
				else {
					return self::$users_exist[$user_id];
				}
			}
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

	protected function __construct($user_id)
	{
		if (self::exist($user_id)) {
			$statement = Core::$PDO->query("SELECT * FROM {prefix}user WHERE user_id = $user_id");
			$fetch = $statement->fetch();
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
		}
		else {
			throw new Exception("User does not exist: User_ID = " . $user_id . "");
		}
	}

	public function get_user_name()
	{
		return $this->user_name;
	}
}