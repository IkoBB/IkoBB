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
 * Time: 02:05
 */

namespace iko\user\profile;

use iko\{
	Core, PDO, Event\Handler
};
use iko\lib\multiton\cache_int;
use iko\user\{
	iUser, User
};

class Field extends cache_int implements iField
{
	protected static $cache = array ();
	protected static $cache_exist = array ();

	public static function create (): bool
	{
		if (Handler::event("iko.user.profile.fields.create", NULL, User::get_session()->get_id(), FALSE)) {
			$user = User::get_session();

		}

		return FALSE;
	}

	private $id;
	private $name;
	private $options;
	private $display;
	private $owner;


	protected function __construct (int $id)
	{
		$this->id = $id;
		if (self::exist($id)) {
			$statement = Core::PDO()->query("SELECT * FROM " . self::table . " WHERE " . self::id . " = $id");
			$fetch = $statement->fetch(PDO::FETCH_ASSOC);
			foreach ($fetch as $key => $value) {
				$temp_key = str_replace("user_field_", "", $key);
				$this->{$temp_key} = $value;
			}
			$this->options = unserialize($this->options);
			$this->owner = User::get($this->owner);
		}
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
		if (is_callable(get_called_class(), $func)) {
			$this->{$func}($values);
		}
	}

	/**
	 * @return int
	 */
	public function get_id (): int
	{
		return $this->id;
	}

	/**
	 * @return \iko\user\iUser
	 */
	public function get_owner (): iUser
	{
		return $this->owner;
	}

	/**
	 * @return string
	 */
	public function get_display (): string
	{
		return $this->display;
	}

	/**
	 * @return string
	 */
	public function get_name (): string
	{
		return $this->name;
	}

	/**
	 * @return array
	 *
	 * Return the Options for the field as array.
	 */
	public function get_options ()
	{
		return $this->options;
	}

	/**
	 * @param string $value
	 *
	 * @return bool
	 * @permission iko.user.profile.fields.set.name
	 *             Need this Permission or is owner of this type of field
	 */
	public function set_name (string $value): bool
	{
		$user = User::get_session();
		if (Handler::event("iko.user.profile.fields.set.name", User::get_session(),
				$this) || $this->get_owner()->get_id() == $user->get_id()
		) {
			if ($value != $this->get_name()) {
				$statement = Core::PDO()->prepare("UPDATE " . self::table . " Set " . self::name . " = :value WHERE " . self::id . " = " . $this->get_id());
				$statement->bindParam(":value", $value);
				$statement->execute();
				if ($statement > 0) {
					$this->name = $value;
				}
			}
		}

		return FALSE;
	}
}
