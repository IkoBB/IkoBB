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
namespace iko;

/**
 * @author Marcel
 *
 */
class session
{
	private static $user_agent;
	private static $user_ip;
	private static $session_id;
	private static $user_id;
	private static $user_salt;

	public static function init($phase)
	{
		switch ($phase) {
			case 0:
				self::load_session();
			break;
			case 1:

			break;
			default:
				NULL;
			break;
		}
	}

	private static function load_session ()
	{
		$id = NULL;
		if (isset_get("ikobb_ssid")) {
			$id = define_get("ikobb_ssid");
		}
		if (isset_post("ikobb_ssid")) {
			$id = define_post("ikobb_ssid");
		}
		if ($id != NULL) {
			session_id($id);
		}
		@session_start();
		self::$session_id = session_id();
		self::$user_agent = define_session("user_agent", "");
		self::$user_id = define_session("user_id", "");
		self::$user_ip = define_session("user_ip", "");
		self::$user_salt = define_session("user_salt", "");
		$ipadress = $_SERVER['REMOTE_ADDR'];
		if (self::$user_agent == "" && self::$user_id == "" && self::$user_ip == "") {
			self::$user_agent = $_SERVER['HTTP_USER_AGENT'];
			set_session("user_agent", self::$user_agent);
			self::$user_id = 0;
			set_session("user_id", self::$user_id);
			self::$user_ip = $ipadress;
			set_session("user_ip", self::$user_ip);
			self::$user_salt = self::get_salt();
			set_session("user_salt", self::$user_salt);
		}
		else {
			if (self::$user_ip != $ipadress || self::$user_salt != self::get_salt()) {
				set_session("user_ip", "0");
				set_session("user_id", "0");
				self::renew();
			}
		}
	}

	public static function renew ()
	{
		session_destroy();
		header("Refresh: 1");
		echo '<script>window.location.reload();</script>';
		exit;
	}

	public static function is_logged_in ()
	{
		if (self::$user_id != 0) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	private static function get_salt ()
	{
		$string = "";
		$string .= $_SESSION["user_id"] . $_SESSION["user_ip"] . session_id();

		return get_hash($string);
	}

	public static function renew_salt ($pass = "")
	{
		if ($pass == "") {
			self::renew();
		}
		else {
			$current_user_id = intval(read_session("user_id"));
			if ($current_user_id == 0) {
				self::renew();
			}
			else {
				$current_user = user\User::get($current_user_id);
				if ($current_user->compare_password($pass)) {
					self::$user_id = $current_user->get_id();
					self::$user_salt = self::get_salt();
					set_session("user_id", self::$user_id);
					set_session("user_salt", self::$user_salt);
				}
				else {
					self::renew();
				}
			}
		}
	}

	public static function compare_salt ()
	{
		if (self::get_salt() == self::$user_salt) {
			return TRUE;
		}

		return FALSE;
	}
}