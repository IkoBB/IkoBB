<?php
/**
 *
 * This file is part of IkoBB Forum and belongs to the module <Core>.
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
 * Date: 11.01.2017
 * Time: 23:31
 */
namespace iko\lib\singleton;

class instance
{
	private static $instance = NULL;

	public static function get_instance ($args = NULL):instance
	{
		$class = get_called_class();
		if ($class::$instance == NULL) {
			$class::$instance = new $class($args);
		}

		return $class::$instance;
	}
}