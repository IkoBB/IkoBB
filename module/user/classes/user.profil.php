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

use iko\user\profile\Content;

class User_profile implements iUser_profile
{
	const profiles = "{prefix}user_profiles";
	const fields = "{prefix}user_fields";


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
		$this->user_id = $user->id;
	}

	public function get (string $name): Content
	{
		return $this->fields[ $name ] ?? NULL;
	}

	public function set (string $name, $value): bool
	{
		return $this->get($name)->set($value);
	}

	public function create (Content $item): bool
	{

	}
}