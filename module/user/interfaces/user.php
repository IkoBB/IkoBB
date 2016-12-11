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
 * Date: 05.11.2016
 * Time: 20:04
 */
namespace iko\user;

interface iUser
{
	/**
	 * @return boolean
	 */
	public function is_own ();

	/**
	 * @return integer
	 */
	public function get_id ();

	/**
	 * @return string
	 */
	public function get_user_name ();

	/**
	 * @return array|Group
	 */
	public function get_groups ();

	/**
	 * @return string as Date Format
	 */
	public function get_joined_Date ();

	/**
	 * @return integer
	 */
	public function get_joined_Time ();

	/**
	 * @return string as Date Format
	 */
	public function get_last_login_Date ();

	/**
	 * @return integer
	 */
	public function get_last_login_Time ();

	/**
	 * @param $pass
	 *
	 * @return string
	 *
	 * The salt is for more security
	 */
	public function salt ($pass);

	/**
	 * @param string $old
	 * @param string $new
	 * @param string $sec
	 *
	 * @return mixed
	 */
	public function change_password ($old, $new, $sec);

	/**
	 * @param $pass
	 *
	 * @return void
	 */
	public function update_last_login ($pass);

	/**
	 * @return string
	 */
	public function get_language ();

	/**
	 * @return integer
	 */
	public function get_template ();

}
