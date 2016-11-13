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

	//      load language of user
	//          -> if no language choosen than standard
	public static function ckeck_user_language ()
	{
		//required modules
		module::request("user");

		//check status of user module
		if (module::load_status("user")) {

			$session = User::get_session();
			//$session = 1;

			//user logged in
			if ($session != 0) {
				//try to load choosen language form database
				$choosen_language = &$session->get_language();
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
		else {
			//TODO: Whats happening if module loading failed ?
			throw new Exception("Error #1234: ");
		}
	}
	//TODO: Implement function which will use check_language and load language and can load words by id
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
		return (isset($this->{$this->supported}[ $value ])) ? $this->{$this->supported}[ $value ] : "";
		// (Bedienung)?TRUE:ELSE
	}

}
