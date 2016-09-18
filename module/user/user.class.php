<?php
namespace Iko;

class user {
	private static $users = array();
	public static function get($user_id = 0) {
		if($user_id != 0 && $user_id != null) {
			if(is_string($user_id)) {
				$user_id = array($user_id);
			}
			foreach($user_id as $id) {
				if(!isset(self::$users[$id]) || self::$users[$id] != null) {
					if(self::search($args)) {
						
					}
				}
			}
		}
	}
	public static function search($args = array(), $or = false) {
		$sql = "SELECT user_id FROM users WHERE";
		$equal = ($or) ? "OR" : "AND";
		if( count($args) > 0 ) {
			$string = "";
			foreach($args as $key => $var) {
				if(is_string($var))
					$string .= ' ' . $key . ' LIKE "' . $var . '"';
				if(is_int($var)) {
					$string .= ' ' . $key . ' = ' . $var . '"';
				}
				if(is_bool($var)) {
					$var = intval($var);
					$string .= ' ' . $key . ' = ' . $var . '"';
				}
			}
		}
	}
	public static function exist($args = array()) {
		
	}
	
	
	
	protected function __construct($user_id) {
		
	}
}