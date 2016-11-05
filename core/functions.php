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
namespace Iko;

/**
 * It will check the Mail address for the Following format:
 * MYNAME@example.com
 *
 * @param $mail
 *
 * @return bool
 */
function check_mail ($mail)
{
	$var1 = explode("@", $mail);
	if (count($var1) == 2) {
		$var2 = explode(".", $var1[1]);
		if (count($var2) > 1) {
			$var2_last = $var2[ count($var2) - 1 ];
			$len = strlen($var2_last);
			if ($len >= 2) {
				return TRUE;
			}
			else return FALSE;
		}
		else return FALSE;
	}
	else return FALSE;
}

/**
 * @param      $name
 * @param null $var
 *
 * @return null|string
 */
function define_session ($name, $var = NULL)
{
	if (isset_session($name)) {
		$wert = $_SESSION[ $name ];

		return htmlspecialchars($wert);
	}
	else {
		return $var;
	}
}

/**
 * @param $name
 *
 * @return bool
 */
function isset_session ($name)
{
	if (isset($_SESSION[ $name ]) && $_SESSION[ $name ] != "") {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

/**
 * @param $name
 * @param $value
 *
 * @return bool
 */
function set_session ($name, $value)
{
	$wert = htmlspecialchars($value);
	$_SESSION[ $name ] = $wert;
	if ($_SESSION[ $name ] == $wert) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

/**
 * @param $length
 *
 * @return string
 */
function random_string ($length)
{
	$base = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890.:_-!§$%()[]{}?#+*<>";
	$result = "";
	for ($i = 0; $i < $length; $i++) {
		$num = mt_rand(0, strlen($base) - 1);
		$result = $result . $base[ $num ];
	}

	return $result;
}

/**
 * @param $name
 *
 * @return bool
 */
function isset_get ($name)
{
	if (isset($_GET[ $name ])) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

/**
 * @param      $name
 * @param null $var
 *
 * @return null|string
 */
function define_get ($name, $var = NULL)
{
	if (isset_get($name)) {
		return htmlspecialchars($_GET[ $name ]);
	}
	else {
		return $var;
	}
}

/**
 * @param $name
 *
 * @return bool
 */
function isset_post ($name)
{
	if (isset($_POST[ $name ])) {
		return TRUE;
	}
	else {
		return FALSE;
	}
}

/**
 * @param      $name
 * @param null $var
 *
 * @return null|string
 */
function define_post ($name, $var = NULL)
{
	if (isset_post($name)) {
		return htmlspecialchars($_POST[ $name ]);
	}
	else {
		return $var;
	}
}

/**
 * @param $name
 *
 * @return string
 */
function read_post ($name)
{
	return htmlspecialchars($_POST[ $name ]);
}

/**
 * @param $name
 *
 * @return string
 */
function read_get ($name)
{
	return htmlspecialchars($_GET[ $name ]);
}

/**
 * @param $name
 *
 * @return string
 */
function read_session ($name)
{
	return htmlspecialchars($_SESSION[ $name ]);
}

/**
 * @param $var
 *
 * @return string
 */
function get_hash ($var)
{
	return hash("sha256", $var);
}

/**
 * @param $timestamp
 *
 * @return bool
 */
function is_valid_unix_timestamp($timestamp)
{
	return ((string) (int) $timestamp === $timestamp) && ($timestamp <= PHP_INT_MAX) && ($timestamp >= ~PHP_INT_MAX);
}


class functions
{
	/**
	 * @param $mail
	 *
	 * @return bool
	 */
	public static function check_mail ($mail)
	{
		return check_mail($mail);
	}

	/**
	 * @param $length
	 *
	 * @return string
	 */
	public static function random_string ($length)
	{
		return random_string($length);
	}

	/**
	 * @param $var
	 *
	 * @return string
	 */
	public static function get_hash ($var)
	{
		return get_hash($var);
	}

	/**
	 * @param $name
	 *
	 * @return bool
	 */
	public static function isset_get ($name)
	{
		return isset_get($name);
	}

	/**
	 * @param $name
	 *
	 * @return bool
	 */
	public static function isset_post ($name)
	{
		return isset_post($name);
	}

	/**
	 * @param $name
	 *
	 * @return bool
	 */
	public static function isset_session ($name)
	{
		return isset_session($name);
	}

	/**
	 * @param      $name
	 * @param null $var
	 *
	 * @return null|string
	 */
	public static function define_get ($name, $var = NULL)
	{
		return define_get($name, $var);
	}

	/**
	 * @param      $name
	 * @param null $var
	 *
	 * @return null|string
	 */
	public static function define_post ($name, $var = NULL)
	{
		return define_post($name, $var);
	}

	/**
	 * @param      $name
	 * @param null $var
	 *
	 * @return null|string
	 */
	public static function define_session ($name, $var = NULL)
	{
		return define_session($name, $var);
	}

	/**
	 * @param $name
	 *
	 * @return string
	 */
	public static function read_get ($name)
	{
		return read_get($name);
	}

	/**
	 * @param $name
	 *
	 * @return string
	 */
	public static function read_post ($name)
	{
		return read_post($name);
	}

	/**
	 * @param $name
	 *
	 * @return string
	 */
	public static function read_session ($name)
	{
		return read_session($name);
	}

	/**
	 * @param $name
	 * @param $value
	 *
	 * @return bool
	 */
	public static function set_session ($name, $value)
	{
		return set_session($name, $value);
	}
	public static function is_valid_unix_timestamp($timestamp)
	{
		return is_valid_unix_timestamp($timestamp);
	}
}