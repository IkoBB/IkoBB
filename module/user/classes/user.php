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
namespace iko\user;

use iko\{
	cms\template, Core, Exception, language\language, module, PDO, Event\Handler, log, session, user\profile\iAvatar, user\profile\iContent
};
use function iko\{
	set_session, check_mail, define_session, get_hash, read_session
};

class User extends operators implements iUser
{

	protected static $cache = array ();
	protected static $cache_exist = array ();

	public static function get ($id = 0, $reload = FALSE): iUser
	{
		return parent::get($id, $reload);
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
				if (session::compare_salt()) {
					self::$session_user = self::get($user_id);
				}
				else {
					session::renew();
				}
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

	public static function login ($user, $password): bool
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
					session::renew_salt($password);
					$class->update_last_login($password);
					self::$session_user = $class;

					return TRUE;
				}
			}
		}

		return FALSE;
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
			if (Handler::event("iko.user.registration", $array)) {
				Core::PDO()->beginTransaction();
				$time = time();
				$statement = Core::PDO()->exec("INSERT INTO " . self::table . "(" . self::name . ", " . self::mail . ", " . "user_date_joined) VALUES('" . $array["user_name"] . "', '" . $array["mail"] . "', '" . $time . "')");
				if ($statement > 0) {
					Core::PDO()->commit();
					$user = self::search(array (
						self::name => $array["user_name"],
						self::mail => $array["mail"]));
					Core::PDO()->beginTransaction();
					if ($user instanceof User) {
						$gen_password = $user->salt($password);
						$statement_two = Core::PDO()->exec("UPDATE " . self::table . " Set user_password = '" . $gen_password . "' WHERE " . self::id . " = " . $user->get_id());
						if ($statement_two > 0) {
							Core::PDO()->commit();
							$array["user_id"] = $user->get_id();
							Handler::event_Final("iko.user.registration", $array);
							log::add("user", 0, 0,
								"Iko.User.Registration: User_name = '" . $user->get_user_name() . "'", $array);

							return TRUE;
						}
						else {
							Core::PDO()->rollBack();
						}
					}
					else {
						throw new Exception("User Error");
					}
				}
				else {
					Core::PDO()->rollBack();
				}
			}
		}

		return FALSE;
	}

	public static function init ()
	{
		User::session();
		$permissions = Permissions\Value::searches(array ("permission_name" => array ("LIKE" => "iko.user.set.%")));
		if ($permissions !== FALSE) {
			foreach ($permissions as $item) {
				if ($item instanceof Permissions\Value) {
					Handler::add_event(module::get("user"), $item->get_name(), get_called_class(), "own_permission",
						NULL, FALSE, "get");
				}
			}
		}
		$permissions = Permissions\Value::searches(array (
			"permission_name" => array (
				"LIKE"     => "iko.user.%",
				"NOT LIKE" => "iko.user.set.%")));
		if ($permissions !== FALSE) {
			foreach ($permissions as $item) {
				if ($item instanceof Permissions\Value) {
					Handler::add_event(module::get("user"), $item->get_name(), get_called_class(),
						"session_has_permission", NULL, TRUE);
				}
			}
		}
	}


	private $password;
	private $email;
	private $avatar = "";
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
	private $profile;

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
			$statement = Core::PDO()->query("SELECT * FROM " . self::table . " WHERE " . self::id . " = $user_id");
			$fetch = $statement->fetch(PDO::FETCH_ASSOC);
			foreach ($fetch as $key => $value) {
				$temp_key = str_replace("user_", "", $key);
				$this->{$temp_key} = $value;
			}
		}
		else {
			throw new Exception("User does not exist: User_ID = " . $user_id . "");
		}
	}


	public function get_id (): int
	{
		return intval($this->id);
	}

	private $groups = NULL;
	private $groups_all = NULL;

	private function load_groups ()
	{
		$this->groups_all = array ();
		$this->groups = array ();
		$sql = "SELECT * FROM " . Permissions::user_assignment . " WHERE " . self::id . " = " . $this->get_id();
		$statement = Core::PDO()->query($sql);
		$fetch_all = $statement->fetchAll(PDO::FETCH_ASSOC);
		foreach ($fetch_all as $fetch) {
			$group = group::get($fetch[ Group::id ]);
			if (array_search($group, $this->groups, TRUE) === FALSE) {
				array_push($this->groups, $group);
			}
			if (array_search($group, $this->groups_all, TRUE) === FALSE) {
				array_push($this->groups_all, $group);
			}
			foreach ($group->get_parents_all() as $item) {
				if (array_search($item, $this->groups_all, TRUE) === FALSE) {
					array_push($this->groups_all, $item);
				}
			}
		}
	}

	public function reload_groups ()
	{
		$this->load_groups();
	}

	private function is_own ()
	{
		if (self::get_session() !== FALSE && $this->get_id() == self::get_session()->get_id()) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	public function own_permission ($permission, $args = NULL, $pre = NULL): bool
	{
		if ($this->get_id() == $args) {
			return TRUE;
		}
		else if (self::get_session() !== FALSE) {
			return User::get_session()->has_permission($permission, $args, $pre);
		}

		return FALSE;
	}

	public function get_groups ()
	{
		if ($this->groups_all == NULL && $this->groups == NULL) {
			$this->load_groups();
		}

		return $this->groups;
	}

	public function get_all_groups ()
	{
		if ($this->groups_all == NULL && $this->groups == NULL) {
			$this->load_groups();
		}

		return $this->groups_all;
	}

	private function get_password (): string
	{
		return $this->password;
	}

	public function get_joined_Date (): string
	{
		return date(Core::date_format(), $this->get_joined_Time());
	}

	public function get_joined_Time (): int
	{
		return intval($this->date_joined);
	}

	public function get_last_login_Date (): string
	{
		return date(Core::date_format(), $this->get_last_login_Time());
	}

	public function get_last_login_Time (): int
	{
		return intval($this->last_login);
	}

	public function salt ($pass): string
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

	public function change_password ($old, $new, $sec): bool
	{
		$s_old = $this->salt($old);
		$s_new = $this->salt($new);
		$s_sec = $this->salt($sec);
		if ($this->get_password() == $s_old && $s_new == $s_sec) {
			if ($this->is_own()) {
				$sql = "UPDATE " . self::table . " Set user_password = '" . $s_new . "' WHERE " . self::id . " = " . $this->get_id();
				$statement = Core::PDO()->exec($sql);
				if ($statement == 1) {
					$this->password = $s_new;

					return TRUE;
				}
			}
		}

		return FALSE;
	}

	/**
	 * @return mixed
	 */
	public function get_signature ()
	{
		return $this->signature;
	}

	/**
	 * @return mixed
	 */
	public function get_about_user ()
	{
		return $this->about_user;
	}

	/**
	 * @return mixed
	 */
	public function get_location_id ()
	{
		return $this->location_id;
	}

	/**
	 * @return mixed
	 */
	public function get_gender ()
	{
		return $this->gender;
	}

	/**
	 * @return mixed
	 */
	public function get_birthday ()
	{
		return $this->birthday;
	}

	/**
	 * @return mixed
	 */
	public function get_timezone_id ()
	{
		return $this->timezone_id;
	}

	/**
	 * @param $pass
	 */
	protected function update_last_login ($pass)
	{
		if ($this->is_own()) {
			$s_pass = $this->salt($pass);
			if ($s_pass == $this->get_password()) {
				Core::PDO()->beginTransaction();
				$new_last_login = time();
				$statement = Core::PDO()->exec("UPDATE " . self::table . " Set user_last_login = " . $new_last_login . " WHERE " . self::id . " = " . $this->get_id());
				if ($statement > 0) {
					$this->last_login = $new_last_login;
					$new_pass = $this->salt($pass);
					$statement_two = Core::PDO()->exec("UPDATE " . self::table . " Set user_password = '" . $new_pass . "' WHERE " . self::id . " = " . $this->get_id());
					if ($statement_two > 0) {
						$this->password = $new_pass;
						Core::PDO()->commit();
					}
					else {
						Core::PDO()->rollBack();
					}
				}
				else {
					Core::PDO()->rollBack();
				}
			}
		}
	}

	public function compare_password (string $pass):bool
	{
		if ($this->salt($pass) == $this->get_password()) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * @return string
	 */
	public function get_language (): string
	{
		return $this->language;
	}

	/**
	 * @return int
	 */
	public function get_template (): int
	{
		return $this->template;
	}

	/**
	 * @return string
	 */
	public function get_email (): string
	{
		return $this->email;
	}

	/**
	 *
	 *
	 * @return \iko\user\User_profile
	 */
	public function get_profile (): User_profile
	{
		if ($this->profile == NULL) {
			$this->profile = new User_profile($this);
		}

		return $this->profile;
	}

	/**
	 * @param string $value
	 *
	 * @return \iko\user\profile\iContent
	 */
	public function get_profile_field (string $value): iContent
	{
		return $this->get_profile()->{$value};
	}

	public function get_avatar (): iAvatar
	{
		if ($this->avatar == NULL || !$this->avatar instanceof iAvatar) {
			$this->avatar = new Avatar($this, strval($this->avatar));
		}

		return $this->avatar;
	}

	/**
	 * @param $value
	 *
	 * @return mixed
	 */
	public function __get ($value)
	{
		$func = 'get_' . $value;
		if (is_callable(get_called_class(), $func)) {
			return $this->{$func}();
		}
		else {
			return NULL;
		}
	}

	/**
	 * @param $name
	 * @param $values
	 */
	public function __set ($name, $values)
	{
		$func = "set_" . $name;
		if ($name != "password") {
			if (is_callable(get_called_class(), $func)) {
				$this->{$func}($values);
			}
		}
	}

	/**
	 * @param $name
	 *
	 * @return bool
	 */
	public function __isset ($name)
	{
		return (isset($this->{$name}) && $this->{$name} !== NULL);
	}

	/**
	 * @param $name
	 * @param $value
	 *
	 * @return bool
	 */
	private function set ($name, $value): bool
	{
		$handler_name = "iko.user.set." . str_replace("set_", "", $name);
		$table_name = "user_" . str_replace("set_", "", $name);
		$class_name = str_replace("user_", "", $table_name);
		if (Handler::event($handler_name, $this->get_id(), User::get_session()->get_id())) {
			if ($value !== NULL && $value != $this->{$class_name}) {
				Core::PDO()->beginTransaction();
				$sql = "UPDATE " . self::table . " Set " . $table_name . " = :value";

				$sql .= " WHERE user_id = " . $this->get_id();
				$statement = Core::PDO()->prepare($sql);
				$statement->bindParam(":value", $value);
				$statement->execute();
				if ($statement > 0) {
					$old = $this->{$class_name};
					$this->{$class_name} = $value;
					Core::PDO()->commit();
					log::add("user", "info", 2, "Changed " . $class_name . " from " . $old . " to " . $value . "",
						array (
							"old" => $old,
							"new" => $value,
							"id"  => $this->get_id()));

					return TRUE;
				}
				else {
					Core::PDO()->rollBack();
				}
			}
		}

		return FALSE;
	}

	/**
	 * @param $name
	 *
	 * @return bool
	 *
	 * @permission iko.user.set.user_name
	 *             Needed for external changes
	 *             Own setting don't need Permissions
	 */
	public function set_name ($name): bool
	{
		return $this->set(__FUNCTION__, $name);
	}

	/**
	 * @param $email
	 *
	 * @return bool
	 *
	 * @permission iko.user.set.user_email
	 *             Needed for external changes
	 *             Own setting don't need Permissions
	 */
	public function set_email ($email): bool
	{
		if (check_mail($email) && self::search(array ("user_email" => $email)) === FALSE) {
			return $this->set(__FUNCTION__, $email);
		}

		return FALSE;
	}

	/**
	 * @param $value
	 *
	 * @return bool
	 *
	 * @permission iko.user.set.user_template
	 *             Needed for external changes
	 *             Own setting don't need Permissions
	 */
	public function set_template ($value): bool
	{
		if (template::template_exists($value)) {
			return $this->set(__FUNCTION__, $value);
		}

		return FALSE;
	}

	/**
	 * @param $value
	 *
	 * @return bool
	 *
	 * @permission iko.user.set.user_language
	 *             Needed for external changes
	 *             Own setting don't need Permissions
	 */
	public function set_language ($value): bool
	{
		if (language::get_instance()->is_supported_language($value)) {
			return $this->set(__FUNCTION__, $value);
		}
		else {
			return FALSE;
		}
	}

	/**
	 * @param      $type
	 * @param null $value
	 *
	 * @return bool
	 *
	 * @permission iko.user.set.user_avatar
	 *             Needed for external changes
	 *             Own setting don't need Permissions
	 */
	public function set_avatar ($type, $value = NULL)
	{
		if (!is_array($type)) {
			$type = $this->get_avatar()->convert($type, $value);
		}
		if (is_array($type)) {
			return $this->set(__FUNCTION__, unserialize($type));
		}

		return FALSE;
	}


	/**
	 * @param $group
	 * @param $func
	 *
	 * @return bool
	 *
	 * @permission iko.user.groups.add
	 * @permission iko.user.groups.remove
	 * @permission iko.user.group.ID.add
	 * @permission iko.user.group.ID.remove
	 *             Allows User to add/remove Groups to User.
	 *             Allows User to add User to Groups.
	 */
	private function change_group ($group, $func): bool
	{
		$func = str_replace("_group", "", $func);
		if (Handler::event("iko.user.groups." . $func, $this->get_id(), User::get_session()->get_id())) {
			if (is_int($group)) {
				$group = Group::get($group);
			}
			else if ($group instanceof permissions\Group) {
				$group = $group->get_class();
			}
			if ($group instanceof Group && (($func == "add" && array_search($group, $this->get_groups(),
							TRUE) === FALSE) || ($func == "remove" && array_search($group, $this->get_groups(),
							TRUE) !== FALSE))
			) {
				if (Handler::event("iko.user.group." . $group->get_id() . "." . $func, $this->get_id(),
					User::get_session()->get_id())
				) {
					$sql = "";
					if ($func == "add") {
						$sql = "INSERT INTO " . Permissions::user_assignment . " (" . self::id . "," . Group::id . ") VALUE('" . $this->get_id() . "', '" . $group->get_id() . "')";
					}
					else if ($func == "remove") {
						$sql = "DELETE FROM " . Permissions::user_assignment . " WHERE " . self::id . " = " . $this->get_id() . " AND " . Group::id . " = " . $group->get_id();
					}
					$statement = Core::PDO()->exec($sql);
					if ($statement == 1) {
						$this->load_groups();
						$group->reload_members();
						if (($func == "add" && array_search($group, $this->get_groups(),
									TRUE) !== FALSE) || ($func == "remove" && array_search($group, $this->get_groups(),
									TRUE) === FALSE)
						) {
							return TRUE;
						}
					}
				}
			}
		}

		return FALSE;
	}

	/**
	 * @param $group
	 *
	 * @return bool
	 *
	 * @permission iko.user.groups.add
	 *             Allows User to add Groups to User.
	 *             Allows User to add User to Groups.
	 */
	public function add_group ($group): bool
	{
		return $this->change_group($group, __FUNCTION__);
	}

	/**
	 * @param $group
	 *
	 * @return bool
	 *
	 * @permission iko.user.groups.remove
	 *             Allows User to remove Groups to User.
	 *             Allows User to remove User to Groups.
	 */
	public function remove_group ($group): bool
	{
		return $this->change_group($group, __FUNCTION__);
	}
}