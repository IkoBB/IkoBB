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
			/** @noinspection PhpUndefinedVariableInspection */
			if (!isset($class::$cache[ $id ]) || $class::$cache[ $id ] == NULL || $reload) {
				/** @noinspection PhpUndefinedMethodInspection */
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

	public static function gets ($ids = 0, $reload = FALSE):array
	{
		$class = get_called_class();
		if (is_array($ids) || is_string($ids)) {
			if (is_array($ids)) {
				/** @noinspection PhpUndefinedMethodInspection */
				$class::exist($ids);
			}
			if (is_string($ids)) {
				$ids = array ($ids);
			}
			$array = array ();
			foreach ($ids as $id) {
				/** @noinspection PhpUndefinedVariableInspection */
				if (!isset($class::$cache[ $id ]) || $class::$cache[ $id ] == NULL || $reload) {
					/** @noinspection PhpUndefinedMethodInspection */
					if ($class::exist($id, $reload)) {
						$class::$cache[ $id ] = new $class($id);
						array_push($array, $class::$cache[ $id ]);
					}
				}
				else {
					array_push($array, $class::$cache[ $id ]);
				}
			}
			if (count($array) == 0) {
				return $array;
			}

			return $array;
		}

		return array ();
	}
}