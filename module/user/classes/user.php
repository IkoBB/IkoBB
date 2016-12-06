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

class User extends operators implements iUser //TODO: Complete
{
	const table = "{prefix}users";
	const id = "user_id";
	const name = "user_name";
	const mail = "user_email";
	protected static $cache = array ();
	protected static $cache_exist = array ();

	public static function get ($ids = 0, $reload = FALSE)
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
			return NULL;
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
	}

	public static function get_all ()
	{
		$statement = Core::$PDO->query("SELECT " . self::id . " FROM " . self::table);
		$fetchAll = $statement->fetchAll(PDO::FETCH_ASSOC);
		$users = array ();
		foreach ($fetchAll as $item) {
			array_push($users, self::get($item["user_id"]));
		}

		return $users;
	}

	/**
	 *
	 * @return void
	 */
	public static function session ()
	{
		$user_id = intval(define_session("user_id", "0"));
		if ($user_id != 0) {
			if (self::exist($user_id)) {
				self::$session_user = self::get($user_id);
			}
		}
	}

	private static $session_user = FALSE;

	/**
	 * @return bool|User
	 */
	public static function get_session ()
	{
		return self::$session_user;
	}

	public static function login ($user, $password) //TODO: Salt Generieren und Last_login hinzufuegen
	{
		if (check_mail($user)) {
			$search = array ("user_email" => $user);
		}
		else {
			$search = array ("user_name" => $user);
		}
		$class = self::search($search);
		if ($class !== FALSE) {
			$pass_hash = $class->salt($password);
			if ($pass_hash == $class->get_password()) {
				set_session("user_id", $class->get_ID());
				if (intval(read_session("user_id")) == $class->get_ID()) {
					$class->update_last_login($password);
					self::$session_user = $class;
					return TRUE;
				}
				return FALSE;
			}
			else {
				return FALSE;
			}
		}
		else {
			return FALSE;
		}
	}

	public static function create ($user_name, $mail, $password, $other = NULL)
	{
		$array = array (
			"user_name" => $user_name,
			"mail"      => $mail,);
		if (is_array($other)) {
			foreach ($other as $key => $item) {
				if ($key != "user_name" && $key != "mail") {
					$array[ $key ] = $item;
				}
			}
		}
		if (!self::search(array (self::name => $array["user_name"])) instanceof User && !self::search(array (self::mail => $array["mail"])) instanceof User) {
			if (Event\Handler::event("iko.user.registration", $array)) {
				Core::$PDO->beginTransaction();
				$time = time();
				$statement = Core::$PDO->exec("INSERT INTO " . self::table . "(" . self::name . ", " . self::mail . ", " . "user_date_joined) VALUES('" . $array["user_name"] . "', '" . $array["mail"] . "', '" . $time . "')");
				if ($statement > 0) {
					Core::$PDO->commit();
					$user = self::search(array (
						self::name => $array["user_name"],
						self::mail => $array["mail"]));
					Core::$PDO->beginTransaction();
					if ($user instanceof User) {
						$gen_password = $user->salt($password);
						$statement_two = Core::$PDO->exec("UPDATE " . self::table . " Set user_password = '" . $gen_password . "' WHERE " . self::id . " = " . $user->get_id());
						if ($statement_two > 0) {
							Core::$PDO->commit();
							$array["user_id"] = $user->get_id();
							Event\Handler::event_Final("iko.user.registration", $array);
							log::add("user", 0, 0,
								"Iko.User.Registration: User_name = '" . $user->get_user_name() . "'", $array);

							return TRUE;
						}
						else {
							Core::$PDO->rollBack();
							return FALSE;
						}
					}
					else {
						throw new Exception("User Error");
					}
				}
				else {
					Core::$PDO->rollBack();
					return FALSE;
				}
			}
			else {
				return FALSE;
			}
		}
		else {
			return FALSE;
		}

	}

	public static function test ()
	{
		echo "Ich bins";
	}

	public static function chat ($name, $text, $pre)
	{
		return str_replace("Penis", "", $text);
	}

	/*
	 * Event\Handler Test Functions
	 */
	public static function chat2 ($name, $text, $pre)
	{
		if ($text != $pre) {
			return str_replace("Jeder", "Penner", $pre);
		}
		else {
			return str_replace("Jeder", "Suesser", $pre);
		}
	} // Only for Testing

	public static function init ()
	{
		User::session();
		$permissions = Permissions\Value::search(array ("permission_name" => array ("LIKE" => "iko.user.change.%")));
		foreach ($permissions as $item) {
			Event\Handler::add_event($item->get_name(), get_called_class(), "own_permission", NULL, FALSE, "get");
		}
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
	private $date_joined; // user_date_joined
	private $birthday;
	private $timezone_id;
	private $last_login;
	private $language;
	private $template;


	/**
	 * user constructor.
	 * The Constructor load the sql rows and add it to the relevant variables
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
		}
		else {
			throw new Exception("User does not exist: User_ID = " . $user_id . "");
		}
	}

	public function is_own ()
	{
		return ($this === self::get_session()) ? TRUE : FALSE;
	}

	public function get_id ()
	{
		return intval($this->id);
	}

	private $groups = array ();
	private $groups_all = array ();

	private function load_groups ()
	{
		$sql = "SELECT * FROM " . Permissions::user_assignment . " WHERE " . self::id . " = " . $this->get_id();
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

	public function own_permission ($permission, $args = NULL, $pre = NULL)
	{
		if ($this->is_own()) {
			return TRUE;
		}
		else if (self::get_session() !== FALSE) {
			return User::get_session()->has_permission($permission, $args, $pre);
		}
		else {
			return FALSE;
		}
	}

	public function get_user_name ()
	{
		return $this->name;
	}

	public function get_groups ()
	{
		return $this->groups;
	}

	private function get_password ()
	{
		return $this->password;
	}

	public function get_joined_Date ()
	{
		return date(Core::date_format(), $this->get_joined_Time());
	}

	public function get_joined_Time ()
	{
		return $this->date_joined;
	}

	public function get_last_login_Date ()
	{
		return date(Core::date_format(), $this->get_last_login_Time());
	}

	public function get_last_login_Time ()
	{
		return $this->last_login;
	}

	public function salt ($pass)
	{
		$dj = $this->get_joined_Time();
		$id = $this->get_id();
		$a = preg_replace("/[^0-9]/", "", $pass);
		$pint = (is_numeric($a)) ? (int)$a : 1;
		$t = 7;
		$ll = $this->get_last_login_Time();
		$pi = round(pi());
		$salt = sqrt($ll) + (($dj % $id) + ($pint * $ll)) * ($dj - ($id * $pint)) . $pass . (($ll % $id) + $pint) * $pi . $t . $pass . sqrt($pint + $id + $dj + $ll);

		return get_hash($salt);
	}

	public function change_password ($old, $new, $sec)
	{
		$s_old = $this->salt($old);
		$s_new = $this->salt($new);
		$s_sec = $this->salt($sec);
		if ($this->get_password() == $s_old && $s_new == $s_sec) {
			if ($this->is_own()) {

			}
		}
	}

	public function update_last_login ($pass)
	{
		if ($this->is_own()) {
			$s_pass = $this->salt($pass);
			if ($s_pass == $this->get_password()) {
				Core::$PDO->beginTransaction();
				$new_last_login = time();
				$statement = Core::$PDO->exec("UPDATE " . self::table . " Set user_last_login = '" . $new_last_login . "' WHERE " . self::id . " = " . $this->get_id());
				if ($statement > 0) {
					$this->last_login = $new_last_login;
					$new_pass = $this->salt($pass);
					$statement_two = Core::$PDO->exec("UPDATE " . self::table . " Set user_password = '" . $new_pass . "' WHERE " . self::id . " = " . $this->get_id());
					if ($statement_two > 0) {
						$this->password = $new_pass;
						Core::$PDO->commit();
					}
					else {
						Core::$PDO->rollBack();
					}
				}
				else {
					Core::$PDO->rollBack();
				}
			}
		}
	}

	public function get_language ()
	{
		return $this->language;
	}

	public function get_template ()
	{
		return $this->template;
	}

	public function get_email ()
	{
		return $this->email;
	}

	public function change_user_name ($username)
	{
		if (Event\Handler::event("iko.user.change.user_name", User::get_session(), $this->get_id())) {
			echo "Ja";
		}
		else {
			echo "nein";
		}
	}
}