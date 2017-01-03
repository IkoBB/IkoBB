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

namespace iko\Event;

use iko\module;

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
		//Set the needed static array as $array
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
		//Default result value
		$result = FALSE;
		//$var will get the same like $args. The first Event_registered function will be able to use both
		$var = $args;
		//Normal if statement
		//Is there no registered Event for the triggered Event it will return true
		if (isset($array[ $name ]) && $array[ $name ] != NULL && is_array($array[ $name ])) {
			$false_counter = 0;
			foreach ($array[ $name ] as $value) {
				//load Complete Module at start and all needed Data from the array
				$module = module::get($value["module"]);
				if (!$module->is_load()) {
					$module->load_complete();
				}
				$class = $value["class"];
				$function = $value["function"];
				//This statement will be used for registered Events with no static function to use
				if (!isset($value["is_func_static"]) || $value["is_func_static"] == FALSE) {
					//Create a ReflectionClass from the original one to get new Instance and to handle it better
					$reflection = new \ReflectionClass($class);
					//If there isset an instance and it's equal with the same class it will set it
					if (isset($value["instance"]) && $value["instance"] != NULL && get_class($value["instance"]) == $reflection->getName()) {
						$class = $value["instance"];
					}
					//If the Class can get over a special static function it will use them from the class. It will get as a return value from the function eval
					elseif (isset($value["can_init_over"]) && $value["can_init_over"] != NULL) {
						$class = eval("return " . $class . "::" . $value["can_init_over"] . "(" . $init_Args . ");");
					}
					//The simplest way is to create a new instance like $var = new class($init_args); with the reflectionclass
					else {
						$class = $reflection->newInstance($init_Args);
					}
					//Will call the function that will be used with the Event_name the original args and the edited or the previous bool statement
					$var = $class->$function($name, $args, $var);
				}
				//Will call the function over eval with the same parameter like in a normal class.
				else {
					$var = eval("return " . $class . "::" . $function . "(" . var_export($name,
							TRUE) . "," . var_export($args,
							TRUE) . ", " . var_export($var, TRUE) . ");");
				}
				//If the returned $var is a bool statement it will be handled with the $false_counter
				if ($type == "event" && $var === FALSE) {
					$false_counter++;
				}
			}
			//Will set the $result as true if $false_counter is lower than 1 and $false_counter is not the same like the sum of the $array[$name] / Event
			if ($type == "event" && $false_counter != count($array[ $name ]) && $false_counter < 1) {
				$result = TRUE;
			}
		}
		else {
			return TRUE;
		}
		//After all Done a normal Event get's a real return value but only if something was set
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
		return self::add_trigger(__FUNCTION__, $module, $name, $class, $func, $instance, $isFuncStatic,
			$canInitialOver);
	}

	public static function add_event_final ($module, $name, $class, $func, $instance = NULL, $isFuncStatic = FALSE, $canInitialOver = NULL)
	{
		return self::add_trigger(__FUNCTION__, $module, $name, $class, $func, $instance, $isFuncStatic,
			$canInitialOver);
	}

	public static function add_last_event ($module, $name, $class, $func, $instance = NULL, $isFuncStatic = FALSE, $canInitialOver = NULL)
	{
		return self::add_trigger(__FUNCTION__, $module, $name, $class, $func, $instance, $isFuncStatic,
			$canInitialOver);
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
			if (isset($array[ $name ]) && $array[ $name ] != NULL) {
				return TRUE;
			}
			else {
				return FALSE;
			}
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