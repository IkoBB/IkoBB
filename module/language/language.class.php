<?php
/**
 * Created by PhpStorm.
 * User: Pascal W
 * Date: 04.11.2016
 * Time: 18:14
 */


namespace iko\language;


class language extends languageConfigs
{
	public function getLanguage ($language)
	{
		if ($this->supportedLanguageExist($language) === TRUE) {
			try {
				$sql = "SELECT " . language_Keys::name . ", " . $language . " FROM " . parent::tableTranslation . "";
				$statement = Core::$PDO->query($sql);
				$fetchall = $statement->fetchAll(PDO::FETCH_ASSOC);
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

	public function insertData ($translation_key, $language = self::supportedLanguages, $data = array ())
	{
		//TODO: Für jeden translation_key überprüfen ob dieser schon vorhanden ist, wenn nicht Datensatz erstellen, bei jeder Sprache, welche leer bleiben soll, muss dieser Wert leer bleiben
		// -> Array anlegen, vielleicht mit anderer Funktion aufrufen, dass jede Sprache abgefrufen wird und der Wert für jede Spalte erstellt wird
		var_dump($language);
		if (count(parent::supportedLanguages == count($data))) {
			$x = 0;
			while ($x <= count($language) - 1) {
				if ($this->supportedLanguageExist($language[ $x ]) === TRUE) {
					echo $data[ $x ];
				}
				$x++;
			}
		}
		else {
			if (count(self::supportedLanguages) < count($data)) {
				echo "Error";
				//TODO: throw new exepction
			}
			else {
				//TODO; rethink
			}
		}
	}

}



