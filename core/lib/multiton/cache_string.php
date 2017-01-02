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
 * Date: 02.01.2017
 * Time: 02:14
 */

namespace iko\lib\multiton;

class cache_string extends cache
{
	public static function get ($id = 0, $reload = FALSE)
	{
		$class = get_called_class();
		if (is_string($id)) {
			if (!isset($class::$cache[ $id ]) || $class::$cache[ $id ] == NULL || $reload) {
				if ($class::exist($id, $reload)) {
					$class::$cache[ $id ] = new $class($id);
				}
				else {
					$class::$cache[ $id ] = NULL;
				}
			}

			return $class::$cache[ $id ];
		}

		return NULL;
	}

	public static function gets ($ids = 0, $reload = FALSE)
	{
		$class = get_called_class();
		if (is_array($ids) || is_string($ids)) {
			if (is_array($ids)) {
				$class::exist($ids);
			}
			if (is_string($ids)) {
				$ids = array ($ids);
			}
			$user_array = array ();
			foreach ($ids as $id) {
				if (!isset($class::$cache[ $id ]) || $class::$cache[ $id ] == NULL || $reload) {
					if ($class::exist($id, $reload)) {
						$class::$cache[ $id ] = new $class($id);
						array_push($user_array, $class::$cache[ $id ]);
					}
				}
				else {
					array_push($user_array, $class::$cache[ $id ]);
				}
			}
			if (count($user_array) == 0) {
				return FALSE;
			}

			return $user_array;
		}

		return array ();
	}
}