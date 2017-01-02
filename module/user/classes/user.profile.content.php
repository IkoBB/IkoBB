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
	Core, PDO
};
use iko\user\User;
use iko\user\User_profile;

class Content
{

	const table = User_profile::profiles;
	const id = User::id;
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
		$this->field_id = $this->field->get_id();
		$statement = Core::$PDO->query("SELECT * FROM " . self::table . " WHERE " . self::id . " = " . $this->user_id . " AND " . Field::id . " = " . $this->field_id);
		$fetch = $statement->fetch(PDO::FETCH_ASSOC);
		if ($fetch !== FALSE) {
			$this->property = $fetch["user_profile_property"];
			$this->value = $fetch["user_profile_value"];
		}
		else {
			$create_statement = Core::$PDO->exec("INSERT INTO " . self::table . "(" . self::id . ", " . Field::id . ", user_profile_property, user_profile_value) VALUE('" . $this->user_id . "', '" . $this->field_id . "', '0', '')");
			if ($create_statement !== FALSE && $create_statement == 1) {
				$this->property = 0;
				$this->value = "";
			}
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
	 * @return string
	 */
	public function get_name (): string
	{
		return $this->name;
	}

	/**
	 * @return \iko\user\profile\Field
	 */
	public function get_field (): Field
	{
		return $this->field;
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

	/**
	 * @param $value
	 *
	 * @return bool
	 */
	public function set ($value): bool
	{
		try {
			if (!is_string($value)) {
				$value = "" . $value;
			}
			if ($value != "") {
				$this->value = $value;
			}
		}
		catch (\Exception $ex) {
			return FALSE;
		}

		return FALSE;
	}

	public function change_property ()
	{

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
}