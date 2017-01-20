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
 * Date: 30.09.2016
 * Time: 22:09
 */
namespace iko\user\permissions;

use iko\lib\multiton\cache_string;
use iko\module;
use iko\user\Permissions;
use iko\Core as Core;
use iko\PDO as PDO;

class Value extends cache_string
{
	const table = Permissions::table;
	const name = Permissions::name;

	public static $cache = array ();
	public static $cache_exist = array ();

	private $name;
	private $module;
	private $comment;

	protected function __construct (string $name)
	{
		if (is_string($name) && self::exist($name)) {
			$sql = "SELECT * FROM " . self::table . " WHERE " . self::name . " = '" . $name . "'";
			$statement = Core::PDO()->query($sql);
			$fetch = $statement->fetch(PDO::FETCH_ASSOC);
			foreach ($fetch as $key => $value) {
				$temp_key = str_replace("permission_", "", $key);
				$temp_key = str_replace("_name", "", $temp_key);
				$this->{$temp_key} = $value;
			}
		}
	}

	public function get_name (): string
	{
		return $this->name;
	}

	public function get_module ():module {
		if(!$this->module instanceof module)
			$this->module = module::get($this->module);
		return $this->module;
	}

	public function get_comment ():string
	{
		return $this->comment;
	}

	public function __toString ()
	{
		return $this->get_name();
	}
}