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
 * Time: 02:12
 */
namespace iko\lib\multiton;

class cache_int extends cache
{
	public static function get ($id = 0, $reload = FALSE)
	{
		$class = get_called_class();
		if (is_numeric($id)) {
			$id = intval($id);
		}
		if (is_int($id)) {
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
		if (is_string($ids) && !is_numeric($ids)) {
			return array ();
		}
		if (is_numeric($ids)) {
			$ids = intval($ids);
		}
		if (is_array($ids) || is_int($ids)) {
			if (is_array($ids)) {
				$class::exist($ids);
			}
			if (is_int($ids)) {
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