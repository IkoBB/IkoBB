<?php
/**
 * Created by PhpStorm.
 * User: Pascal W
 * Date: 04.11.2016
 * Time: 18:14
 */


namespace Iko;


class language extends languageFunction
{


}


class languageConfigs
{
	const tableTranslation = "iko_translation";
	const defaultLanguage = "english";

	const supportedLanguages = array (
		"english",
		"german",);

	public function getDefaultLanguage ()
	{
		$defaultLanguage = self::defaultLanguage;

		return $defaultLanguage;
	}

	public function getSupportedLanguages ()
	{
		$supportedLanguages = self::supportedLanguages;

		return $supportedLanguages;
	}

	public function supportedLanguageExist ($supportedLanguage)
	{
		$sql = "SELECT " . $supportedLanguage . " FROM " . self::tableTranslation . "";
		$statement = Core::$PDO->query($sql);

		if ($statement !== FALSE) {
			return TRUE;
		}
		else {
			//TODO throw exeption
			echo "Language : {$supportedLanguage} do not exist";

			return FALSE;
		}
	}

	//TODO: Wenn irgendetwas aus der Klasse geladen wird, soll dies überprüft werden
}

class languageFunction extends languageConfigs
{

	public function getLanguage ($language)
	{
		if ($this->supportedLanguageExist($language) === TRUE) {
			if (array_search($language, self::supportedLanguages) !== FALSE) {
				try {
					$sql = "SELECT translation_key, " . $language . " FROM " . self::tableTranslation . "";
					$statement = Core::$PDO->query($sql);
					$fetchall = $statement->fetchAll(PDO::FETCH_ASSOC);

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
					*/

					return $fetchall;
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


	}

	public function insertData ($translation_key, $language = self::supportedLanguages, $data = array ())
	{
		//TODO: Für jeden translation_key überprüfen ob dieser schon vorhanden ist, wenn nicht Datensatz erstellen, bei jeder Sprache, welche leer bleiben soll, muss dieser Wert leer bleiben
		// -> Array anlegen, vielleicht mit anderer Funktion aufrufen, dass jede Sprache abgefrufen wird und der Wert für jede Spalte erstellt wird
		var_dump($language);
		if (count(self::supportedLanguages == count($data))) {
			$x = 0;
			while ($x <= count($language) - 1) {
				if ($this->supportedLanguageExist($language[ $x ]) === TRUE) {
					echo $data[ $x ];
				}
				$x++;
			}
		}
		else {
			if (count(self::supportedLanguages < count($data))) {
				echo "Error";
				//TODO: throw new exepction
			}
			else {
				//TODO; rethink
			}
		}
	}
}
