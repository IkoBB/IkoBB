<?php
/**
 *
 * This file is part of IkoBB Forum and belongs to the module <Language>.
 *
 * @copyright (c) IkoBB <https://www.ikobb.de>
 * @license       GNU General Public License, version 3 (GPL-3.0)
 *
 * For full copyright and license information, please see
 * the LICENSE file.
 *
 */
/**
 * Created by PhpStorm.
 * User: Marcel
 * Date: 11.01.2017
 * Time: 23:29
 */
namespace iko\language;

use iko\config;
use iko\Core;
use iko\lib\singleton\instance;
use iko\session;
use iko\user\User;

class language extends instance implements iLanguage
{
	const default = "english";
	const table = "{prefix}translation";
	const name = "translation_key";
	protected static $instance = NULL;

	private $current = NULL;
	private $languages = array ();

	protected function __construct ()
	{
		$lang = NULL;
		if (session::is_logged_in()) {
			$lang = User::get_session()->get_language();
		}
		if ($lang == NULL || $lang == "") {
			$config = config::load("pdo", "language");
			$lang = $config->default_language ?? self::default;
		}
		$this->current = $lang;

		$sql = "SHOW COLUMNS FROM " . self::table;
		$statement = Core::PDO()->query($sql);
		$fetch_all = $statement->fetchAll();
		foreach ($fetch_all as $item) {
			if ($item["Field"] != self::name) {
				array_push($this->languages, $item["Field"]);
			}
		}
	}

	public function get_current (): string
	{
		return $this->current;
	}

	public function is_supported_language (string $lang): bool
	{
		if (array_search($lang, $this->languages) !== FALSE) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	public function get_languages (): array
	{
		return $this->languages;
	}
}