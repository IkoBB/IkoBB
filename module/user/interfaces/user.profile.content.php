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
 * Date: 15.01.2017
 * Time: 00:58
 */
namespace iko\user\profile;

use iko\user\iUser;
use iko\user\iUser_profile;

interface iContent
{
	const table = iUser_profile::profiles;
	const id = iUser::id;
	const value = "user_profile_value";
	const property = "user_profile_property";

	public function get_name (): string;

	public function get_field (): iField;

	public function get_user (): iUser;

	public function get ();

	public function get_value ();

	public function get_property ();

	public function set_property ($value): bool;

	public function set_value ($value): bool;
}