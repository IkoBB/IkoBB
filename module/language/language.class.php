<?php
/**
 * Created by PhpStorm.
 * User: Pascal W
 * Date: 04.11.2016
 * Time: 18:14
 */


namespace iko\language;

use iko\Core;
use iko;

class language extends languageConfigs
{
	public function getLanguage ($language)
	{
		if ($this->supportedLanguageExist($language) === TRUE) {
			try {
				$sql = "SELECT " . language_Keys::name . ", " . $language . " FROM " . parent::tableTranslation . "";
				$statement = Core::$PDO->query($sql);
				$fetchall = $statement->fetchAll(iko\PDO::FETCH_ASSOC);
				$array = array ();
				foreach ($fetchall as $item) {
					array_push($array, $item[ language_Keys::name ]);
				}
				$keys = language_Keys::get($array);
				$array = array ();
				foreach ($keys as $item) {
					$item->set_lang($language);
					array_push($array, array (
						language_Keys::name => $item->get_key(),
						"lang"              => $language,
						"value"             => "" . $item . ""));

				}

				/*foreach($fetchall as $item){
					foreach($item as $key=>$value){
						if($key=="translation_key"){
							$translationKey=$value;
						}
						if($key==$language){
							$translation=$value;
						}
					}
				}

				$test = (new class($item->get_key(), $language) extends language_keys {
						public function __construct ($name, $lang)
						{
							parent::__construct($name);
							$this->set_lang = $lang;
						}

						public function __toString ()
						{
							return $this->{$this->set_lang};
						}
					}
					);
				*/

				return $array;
			}
			catch (\PDOException $exception) {
				throw new Exception("Error#1234:" . $exception);
			}
		}
		else {
			//language do not exists
			throw new Exception("Error#1234:");
		}
	}


	public function insertData ($translation_key, $language = array (), $data = array ())
	{
		foreach ($language as $languages) {
			$allLanguagesSupported = $this->supportedLanguageExist($languages);
			echo $allLanguagesSupported;
			if ($allLanguagesSupported !== TRUE) {
				return FALSE;
			}
		}
		if ($translation_key == "") {
			return FALSE;
		}
		else {
			$key = iko\language\language_Keys::get($translation_key);
			if ($key == NULL) {
				return FALSE;
			}
			else {
				//Für jede Spalte wird ein Wert hinzugefügt mit einem SQL Befehl
				//Langueg muss noch mit DAT verglichen werden
				$countLanguage = count($language);
				$countData = count($data);

				if ($countLanguage >= $countData) {
					while ($countLanguage > $countData) {
						array_push($data, "NULL");
						$countData++;
						var_dump($data);
					}

					/*try
					{
						$sql = "Insert " . language_Keys::name . ", " . $language . " FROM " . parent::tableTranslation . "";

						$statement = Core::$PDO->query($sql);
					}
					catch(iko\Exception $exception)
					{

					}
					*/

				}
				else {
					return FALSE;
				}

			}
		}


	}


}



