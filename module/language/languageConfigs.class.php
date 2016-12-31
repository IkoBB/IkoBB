<?php
/**
 * Created by PhpStorm.
 * User: Pascal W
 * Date: 04.12.2016
 * Time: 14:40
 */

namespace iko\language;

use iko;
use iko\Core;

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

	public function createDataTranslationKey ($translationKey)
	{

		$sql = "SELECT " . $translationKey . " FROM " . self::tableTranslation . "";
		$statement = Core::$PDO->query($sql);

		if ($statement === FALSE) {
			$sql1 = "INSERT INTO" . " " . self::tableTranslation . "(translation_key) VALUES " . "('" . $translationKey . "')" . "";
			$statement1 = Core::$PDO->exec($sql1);
			
		}

		//TODO: Wenn irgendetwas aus der Klasse geladen wird, soll dies überprüft werden
	}
}