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
 * Time: 21:48
 */
namespace iko\user;

use iko\user\profile\iContent;

class User_profile implements iUser_profile
{
	private $user;
	private $user_id;
	private $fields = array ();

	/**
	 * User_profile constructor.
	 *
	 * @param \iko\user\User $user
	 */
	public function __construct (User $user)
	{
		$this->user = $user;
		$this->user_id = $user->get_id();
	}

	public function get (string $name): iContent
	{
		return $this->fields[ $name ] ?? NULL;
	}

	public function __get ($name)
	{
		return $this->get($name);
	}

	public function __set ($name, $value)
	{
		$this->set($name, $value);
	}

	public function set (string $name, $value): bool
	{
		return $this->get($name)->set_value($value);
	}

	public function __isset ($name)
	{
		if (isset($this->fields[ $name ]) && $this->fields[ $name ] !== NULL) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	public function create (iContent $item): bool
	{
		return FALSE;
	}
}