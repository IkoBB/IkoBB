<?php
/**
 * Created by PhpStorm.
 * User: Marcel
 * Date: 11.12.2016
 * Time: 20:30
 */
namespace iko;
class module_permission
{
	public static function has ($permission, $function)
	{
		$debug = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 3);
		if ($debug[2]["class"] == "iko\\module") {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}
}