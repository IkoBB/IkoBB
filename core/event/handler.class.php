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
 * Created by PhpStorm.
 * User: Marcel
 * Date: 05.11.2016
 * Time: 22:02
 */

namespace Iko\Event;

use Iko\module;

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
				$module = module::get($value["module"]);
				if (!$module->is_load()) {
					$module->load_complete();
				}
				$class = $value["class"];
				$function = $value["function"];
				if (!isset($value["is_func_static"]) || $value["is_func_static"] == FALSE) {
					$reflection = new \ReflectionClass($class);
					if (isset($value["instance"]) && $value["instance"] != NULL && get_class($value["instance"]) == $reflection->getName()) {
						$class = $value["instance"];
					}
					elseif (isset($value["can_init_over"]) && $value["can_init_over"] != NULL) {
						$class = eval("return " . $class . "::" . $value["can_init_over"] . "(" . $init_Args . ");");
					}
					else {
						$class = $reflection->newInstance($init_Args);
					}
					$var = $class->$function($name, $args, $var);
				}
				else {
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
		else {
			return TRUE;
		}
		if ($type == "event") {
			if (count($array[ $name ]) > 0) {
				if (isset($var) && !is_bool($var)) {
					return $var;
				}
				else {
					return $result;
				}
			}
			else {
				return TRUE;
			}
		}
	}


	public static function event_Final ($name, $args = NULL, $init_Args = NULL)
	{
		self::trigger(__FUNCTION__, $name, $args, $init_Args);
	}

	public static function FinalAll ()
	{
		$args = NULL;
		$init_Args = NULL;
		foreach (self::$FinalAll as $name => $xyz) {
			foreach (self::$FinalAll[ $name ] as $value) {
				$class = $value["class"];
				$function = $value["function"];
				if (!isset($value["is_func_static"]) || $value["is_func_static"] === FALSE) {
					$reflection = new ReflectionClass($class);
					if (isset($value["instance"]) && $value["instance"] != NULL && get_class($value["instance"]) == $reflection->getName()) {
						$class = $value["instance"];
					}
					elseif (isset($value["can_init_over"]) && $value["can_init_over"] != NULL) {
						$class = eval("return " . $class . "::" . $value["can_init_over"] . "(" . $init_Args . ");");
					}
					else {
						$class = $reflection->newInstance($init_Args);
					}
					$class->$function($name, $args);
				}
				else {
					eval($class . "::" . $function . "(" . var_export($name, TRUE) . "," . var_export($args,
							TRUE) . ");");
				}
			}
		}
	}

	public static function add_event ($module, $name, $class, $func, $instance = NULL, $isFuncStatic = FALSE, $canInitialOver = NULL)
	{
		self::add_trigger(__FUNCTION__, $module, $name, $class, $func, $instance, $isFuncStatic, $canInitialOver);
	}

	public static function add_event_final ($module, $name, $class, $func, $instance = NULL, $isFuncStatic = FALSE, $canInitialOver = NULL)
	{
		self::add_trigger(__FUNCTION__, $module, $name, $class, $func, $instance, $isFuncStatic, $canInitialOver);
	}

	public static function add_last_event ($module, $name, $class, $func, $instance = NULL, $isFuncStatic = FALSE, $canInitialOver = NULL)
	{
		self::add_trigger(__FUNCTION__, $module, $name, $class, $func, $instance, $isFuncStatic, $canInitialOver);
	}

	private static function add_trigger ($type, $module, $name, $class, $func, $instance = NULL, $isFuncStatic = FALSE, $canInitialOver = NULL)
	{
		if ($module instanceof module) {
			$module = $module->get_name();
		}
		if (module::exist($module)) {
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
				"module"         => $module,
				"class"          => $class,
				"function"       => $func,
				"instance"       => $instance,
				"is_func_static" => $isFuncStatic,
				"can_init_over"  => $canInitialOver));
		}
	}

	/*
	 * array(
	 * "module" => "MODULENAME",        0
	 * "class" => "class",              1
	 * "function" => "functionname",    2
	 * "instance" => "instance",        3
	 * "is_func_static" => false,       4
	 * "can_init_over" => ""            5
	 * )
	 */

	public static function init ()
	{

	}
}

?>