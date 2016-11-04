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
	const table = "{prefix}language";
	const translation = " {prefix}translation";
	const supported = array (
		"german",
		"english");

	private static $instance = NULL;

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
