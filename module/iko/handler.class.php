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
 * Time: 22:02
 */

namespace Iko\Event;

class Handler
{
	private static $event = array ();
	private static $eventFinal = array ();
	private static $FinalAll = array ();

	private function __construct ()
	{

	}

	public static function test ()
	{
		echo "<br><pre>";
		print_r(self::$event);
		print_r(self::$eventFinal);
		print_r(self::$FinalAll);
		echo "<br>";
	}

	public static function event ($name, $args = NULL, $init_Args = NULL)
	{
		return self::trigger(__FUNCTION__, $name, $args, $init_Args);
	}

	private static function trigger ($type, $name, $args = NULL, $init_Args = NULL)
	{
		switch ($type) {
			case 'eventFinal':
				$array = &self::$eventFinal;
			break;
			case 'FinalAll':
				$array = &self::$FinalAll;
			break;
			default:
				$array = &self::$event;
			break;
		}
		$result = FALSE;
		$var = NULL;
		if (isset($array[ $name ]) && $array[ $name ] != NULL && is_array($array[ $name ])) {
			$false_counter = 0;
			foreach ($array[ $name ] as $value) {
				if (!isset($value[3]) || $value[3] == FALSE) {
					$class = $value[0];
					$function = $value[1];
					$reflection = new \ReflectionClass($class);
					if (isset($value[2]) && $value[2] != NULL && get_class($value[2]) == $reflection->getName()) {
						$class = $value[2];
					}
					elseif (isset($value[4]) && $value[4] != NULL) {
						$class = eval("return " . $class . "::" . $value[4] . "(" . $init_Args . ");");
					}
					else {
						$class = $reflection->newInstance($init_Args);
					}
					$var = $class->$function($name, $args, $var);
				}
				else {
					$class = $value[0];
					$function = $value[1];
					$var = eval("return " . $class . "::" . $function . "(" . var_export($name,
							TRUE) . "," . var_export($args,
							TRUE) . ", " . var_export($var, TRUE) . ");");
				}
				if ($type == "event" && $var === FALSE) {
					$false_counter++;
				}
			}
			if ($type == "event" && $false_counter != count($array[ $name ]) && $false_counter < 1) {
				$result = TRUE;
			}
		}
		if ($type == "event") {
			if (isset($var) && !is_bool($var)) {
				return $var;
			}
			else {
				return $result;
			}
		}
	}

	public static function eventFinal ($name, $args = NULL, $init_Args = NULL)
	{
		self::trigger(__FUNCTION__, $name, $args, $init_Args);
	}

	public static function FinalAll ()
	{
		$args = NULL;
		$init_Args = NULL;
		foreach (self::$FinalAll as $name => $xyz) {
			foreach (self::$FinalAll[ $name ] as $value) {
				if (!isset($value[3]) || $value[3] === FALSE) {
					$class = $value[0];
					$function = $value[1];
					$reflection = new ReflectionClass($class);
					if (isset($value[2]) && $value[2] != NULL && get_class($value[2]) == $reflection->getName()) {
						$class = $value[2];
					}
					elseif (isset($value[4]) && $value[4] != NULL) {
						$class = eval("return " . $class . "::" . $value[4] . "(" . $init_Args . ");");
					}
					else {
						$class = $reflection->newInstance($init_Args);
					}
					$class->$function($args);
				}
				else {
					$class = $value[0];
					$function = $value[1];
					eval($class . "::" . $function . "(" . $args . ");");
				}
			}
		}
	}

	public static function add_event ($name, $class, $func, $instance = NULL, $isFuncStatic = FALSE, $canInitialOver = NULL)
	{
		self::add_trigger(__FUNCTION__, $name, $class, $func, $instance, $isFuncStatic, $canInitialOver);
	}

	public static function add_event_final ($name, $class, $func, $instance = NULL, $isFuncStatic = FALSE, $canInitialOver = NULL)
	{
		self::add_trigger(__FUNCTION__, $name, $class, $func, $instance, $isFuncStatic, $canInitialOver);
	}

	public static function add_last_event ($name, $class, $func, $instance = NULL, $isFuncStatic = FALSE, $canInitialOver = NULL)
	{
		self::add_trigger(__FUNCTION__, $name, $class, $func, $instance, $isFuncStatic, $canInitialOver);
	}

	private static function add_trigger ($type, $name, $class, $func, $instance = NULL, $isFuncStatic = FALSE, $canInitialOver = NULL)
	{
		switch ($type) {
			case 'add_event_final':
				$array = &self::$eventFinal;
			break;
			case 'add_last_event':
				$array = &self::$FinalAll;
			break;
			default:
				$array = &self::$event;
			break;
		}
		if (!isset($array[ $name ]) || $array[ $name ] == NULL) {
			$array[ $name ] = array ();
		}
		array_push($array[ $name ], array (
			$class,
			$func,
			$instance,
			$isFuncStatic,
			$canInitialOver));
	}

	public static function init ()
	{

	}
}

?>