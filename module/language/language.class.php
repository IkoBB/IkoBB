<?php
/**
 * Created by PhpStorm.
 * User: Pascal W
 * Date: 04.11.2016
 * Time: 18:14
 */


namespace Iko;


class language
{
	//TODO: Add dbname somewhere else
	const dbname = "ikobb";
	const table = "{prefix}language";
	const translation = " {prefix}translation";
	const supported = array (
		"german",
		"english");

	private static $instance = NULL;
	private static $default_language = "english";

	public static function get_instance ()
	{
		if (self::$instance == NULL) {
			self::$instance = new language();
		}

		return self::$instance;
	}


	private $german = array ();
	private $english = array ();
	private $lang = "english";

	private $translations = array ();

	private function __construct ()
	{
		/*module::request("user");
		if(module::load_status("user")) {
			if($user = User::get_session() !== FALSE) {
				$this->lang = $user->get_system_language();
			}
		}
		else{

		}*/
		$this->load_language("german");
		$this->load_language("english");
	}


	//TODO: User logged in ?
	//      load language of user
	//          -> if no language choosen than standard
	public static function ckeck_user_language ()
	{
		$session = User::get_session();
		//$session = 1;
		if ($session != 0) {
			//user logged in
			$choosen_language = User::get_language();
			//$choosen_language = "german";
			if (array_search($choosen_language, self::supported) === FALSE) {
				$language = self::$default_language;

				return $language;
			}
			else {
				return $choosen_language;
			}

		}
		else {
			//no user logged in -> Option to find out guest changed language ?
			$language = self::$default_language;

			return $language;

		}

	}

	//TODO: Are all needed modules have the correct version ? module::user for example

	public function load_language ($language)
	{
		if (array_search($language, self::supported) !== FALSE) {
			try {
				$statement = Core::$PDO->query("SELECT translation_key, " . $language . " FROM " . self::translation . " ");
				$fetchall = $statement->fetchAll(PDO::FETCH_ASSOC);
				foreach ($fetchall as $item) {
					foreach ($item as $key => $value) {
						if ($key == "translation_key") {
							$id = $value;
						}
						if ($key == $language) {
							$this->{$language}[ $id ] = $value;
						}
					}
				}

			}
			catch (\PDOException $exception) {
				throw new Exception("Error #1234: " . $exception);
			}
		}
	}

	public function __get ($value)
	{
		return (isset($this->{$this->lang}[ $value ])) ? $this->{$this->lang}[ $value ] : "";
		// (Bedienung)?TRUE:ELSE
	}

}
