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
 * Time: 21:34
 */
namespace iko\user;

interface iGroup
{
	public function get_id ();

	public function get_rang ();

	public function set_rang ($group_rang);

	public function get_parents ();

	public function get_style ();
}