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
 * Date: 16.12.2016
 * Time: 21:50
 */

namespace iko\user\profile;

use iko\{
	Core, Event\Handler, PDO
};
use iko\user\User;
use iko\user\User_profile;

class Content
{

	const table = User_profile::profiles;
	const id = User::id;
	const value = "user_profile_value";
	const property = "user_profile_property";

	protected $value = "";
	protected $property = 0;
	protected $user_class;
	protected $user_id;
	protected $field;
	protected $field_id;

	protected $create = FALSE;

	public function __construct (User $user, Field $field, bool $create = FALSE)
	{
		$this->user_class = $user;
		$this->user_id = $user->get_id();
		$this->create = $create;
		$this->field = $field;
		$this->field_id = $this->get_field()->get_id();
		$statement = Core::$PDO->query("SELECT * FROM " . self::table . " WHERE " . self::id . " = " . $this->user_id . " AND " . Field::id . " = " . $this->field_id);
		$fetch = $statement->fetch(PDO::FETCH_ASSOC);
		if ($fetch !== FALSE) {
			$this->property = $fetch[ self::value ];
			$this->value = $fetch[ self::property ];
		}
		else {
			$create_statement = Core::$PDO->exec("INSERT INTO " . self::table . "(" . self::id . ", " . Field::id . ", " . self::property . ", " . self::value . ") VALUE('" . $this->user_id . "', '" . $this->field_id . "', '0', '')");
			if ($create_statement !== FALSE && $create_statement == 1) {
				$this->property = 0;
				$this->value = "";
			}
		}
	}

	/**
	 * @return int
	 */

	/**
	 * @return string
	 */
	public function get_name (): string
	{
		return $this->get_field()->get_name();
	}

	/**
	 * @return \iko\user\profile\Field
	 */
	public function get_field (): Field
	{
		return $this->field;
	}

	public function get_user (): User
	{
		return $this->user_class;
	}

	/**
	 * @return string
	 */
	public function get ()
	{
		$string = $this->field->get_display();
		$array = $this->field->get_options()[ $this->property ];
		foreach ($array as $key => $item) {
			if ($key != "value") {
				$string = str_replace('{$' . $key . '}', $item, $string);
			}
		}
		$string = str_replace('{$value}', $this->value, $string);

		return $string;
	}

	public function get_value ()
	{
		return $this->value;
	}

	public function get_property ()
	{
		return $this->property;
	}

	/**
	 * @param string $name
	 * @param $value
	 *
	 * @return bool
	 * @permission iko.user.profile.fields.user.set
	 * @permission iko.user.profile.field.ID.user.set
	 *
	 */
	private function set ($name, $value): bool
	{
		$name = str_replace("set_", "", $name);
		if (Handler::event("iko.user.profile.fields.user." . $name, $this->user_class->get_id(),
				User::get_session()->get_id()) && Handler::event("iko.user.profile.field." . $this->get_field_id() . ".user." . $name,
				$this->user_class->get_id(), User::get_session()->get_id())
		) {
			try {
				if (!is_string($value)) {
					$value = "" . $value;
				}
				$table = (new \ReflectionClass($this))->getConstant($name);
				$sql = "UPDATE " . self::table . " Set " . $table . " = '" . $value . "' WHERE " . self::id . " = " . $this->get_user()->get_id() . " AND " . Field::id . " = " . $this->get_field()->get_id();
				$statement = Core::$PDO->exec($sql);
				if ($statement > 0) {
					$this->{$name} = $value;

					return TRUE;
				}
			}
			catch (\Exception $ex) {
			}
		}

		return FALSE;
	}

	/**
	 * @param $value
	 *
	 * @return bool
	 *
	 * @permission iko.user.profile.fields.user.property
	 */
	public function set_property ($value): bool
	{
		if ($value != $this->get_property()) {
			return $this->set(__FUNCTION__, $value);
		}

		return FALSE;
	}

	public function set_value ($value): bool
	{
		if ($value != $this->value) {
			return $this->set(__FUNCTION__, $value);
		}

		return FALSE;
	}

	public function __toString ()
	{
		return $this->get();
	}

	public function __sleep ()
	{
		return array (
			"value",
			"property",
			"user_id",
			"field_id");
	}

	public function __wakeup ()
	{
		$this->user_class = User::get($this->user_id);
		$this->field = Field::get($this->field_id);
	}

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
}