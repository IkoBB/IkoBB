<?php
/**
 *
 * This file is part of IkoBB Forum and belongs to the module <Iko>.
 *
 * @copyright (c) IkoBB <https://www.ikobb.de>
 * @license       GNU General Public License, version 3 (GPL-3.0)
 *
 * For full copyright and license information, please see
 * the LICENSE file.
 *
 */
/**
 * @author Marcel
 *
 */
namespace Iko;

interface config_interface
{
	/**
	 * @param string $name
	 * @param mixed  $value
	 * @param string $comment
	 */
	function add ($name, $value, $comment);

	/**
	 * @param string $name
	 * @param mixed  $value
	 * @param string $comment
	 */
	function set ($name, $value, $comment = "");
}