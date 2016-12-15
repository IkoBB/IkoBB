<?php
/**
 *
 * This file is part of IkoBB Forum and belongs to the module <template>.
 *
 * @copyright (c) IkoBB <https://www.ikobb.de>
 * @license       GNU General Public License, version 3 (GPL-3.0)
 *
 * For full copyright and license information, please see
 * the LICENSE file.
 *
 */

namespace iko\cms;

use Emojione;
use iko\Core;
use iko\PDO;

class parser
{

	const table = "{prefix}bbcodes";

	/**
	 * Checks if the url is valid and if its an allowed protocol like http or https
	 *
	 * @param $url
	 *
	 * @return bool
	 */
	public static function check_url ($url)
	{
		$valid_url = FALSE;
		if (filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED)) {
			$parsed_url = parse_url($url);
			$valid_url = (boolean)preg_match('#\\Ahttps?\\z#ui', $parsed_url['scheme']);
		}

		return $valid_url;
	}

	/**
	 * Transforms a text smiley like :grin: or an unicode emoji (for example send by an mobile phone) to an
	 * image based emoji
	 *
	 * @param $string
	 *
	 * @return string
	 */
	public static function text_to_emoji ($string)
	{
		$return = '';
		$client = new Emojione\Client(new Emojione\Ruleset());
		$client->ascii = TRUE;
		$client->imageType = 'svg';
		if (isset($string)) {
			$return = $client->toImage($string);
		}

		return $return;
	}

	/**
	 * If a string has native unicode emojis, for example send by mobile devices, it will be translated to a string
	 *
	 * @param $string
	 *
	 * @return string
	 */
	public static function emoji_to_text ($string)
	{
		$client = new Emojione\Client(new Emojione\Ruleset());
		$return = $client->toShort($string);

		return $return;
	}

	/**
	 * Transforms a string to an string which has coding syntax highlighting
	 *
	 * @param        $string
	 * @param string $language
	 * @param bool   $highlight
	 *
	 * @return mixed|string
	 */
	public static function syntax_highlighter ($string, $language = "c", $highlight = FALSE)
	{
		$return = "";
		if (isset($string) && $string != "") {
			$string = html_entity_decode($string);
			$geshi = new \GeSHi($string, $language);
			$geshi->set_header_type(GESHI_HEADER_PRE_TABLE);
			$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
			if ($highlight != FALSE) {
				$geshi->highlight_lines_extra(array ($highlight));
			}

			$return = $geshi->parse_code();
		}

		return $return;
	}

	public static function dynamic_time ($time)
	{
		if (is_valid_unix_timestamp($time) !== TRUE) {
			$time = strtotime($time);
		}
		$now = time();
		$dif = $now - $time;
		if ($dif <= 60) {
			return "Just now"; // Just now
		}
		elseif ($dif > 60 && $dif <= 300) {
			return "A few minutes ago"; // A few minutes ago
		}
		elseif ($dif > 300 && $dif <= 3600) {
			return round($dif / 60) . " minutes ago"; // 6 minutes ago
		}
		elseif ($dif > 3600 && $dif <= 7200) {
			return round($dif / 3600) . " hour ago"; // 2 hours ago
		}
		elseif ($dif > 7200 && $dif <= 86400) {
			return round($dif / 3600) . " hours ago"; // 2 hours ago
		}
		elseif ($dif > 86400 && $dif <= 604800) {
			return round($dif / 86400) . " days ago";
		}
		else {
			return date(Core::date_format(), $time);
		}

	}


	public $BBCodes = array ();

	public function __construct ()
	{
		try {
			$statement = Core::$PDO->prepare("SELECT * FROM " . self::table . " ORDER BY bbcode_id ASC");
			$statement->execute();
			$result = $statement->fetchAll(PDO::FETCH_ASSOC);
			foreach ($result as $BBcode) {
				$this->BBCodes[ $BBcode['bbcode_tag'] ] = array (
					'pattern'     => $BBcode['pattern'],
					'replacement' => $BBcode['replacement']);
			}
		}
		catch (\PDOException $exception) {
			throw new Exception("Error #1234: " . $exception);
		}
	}

	public function add_BBCode ($tag, $pattern, $replacement)
	{
		try {
			$statement = Core::$PDO->prepare("INSERT INTO " . self::table . " (BBCode, pattern, replacement) VALUE (:tag, :pattern, :replacement)");
			$statement->bindParam(':tag', $tag);
			$statement->bindParam(':pattern', $pattern);
			$statement->bindParam(':replacement', $replacement);

			$statement->execute();

		}
		catch (\PDOException $exception) {
			throw new Exception("Error #1234: " . $exception);
		}
	}

	public function parse ($text, $bbcodes = TRUE, $emoji = TRUE)
	{
		if($bbcodes === TRUE) {
			$text = $this->bbCodes($text);
		}
		if($emoji === TRUE) {
			$text = self::text_to_emoji($text);
		}
		return $text;
	}


	/**
	 * Parser for BBCodes
	 * All possible BBCodes can be found in our Wiki:
	 * http://www.ikobb.info
	 *
	 * @param string $text
	 *
	 * @return mixed|string
	 */

	private function bbCodes ($text)
	{
		$text = str_replace(array (
			'<',
			'>',), array (
			'&lt;',
			'&gt;',), $text);
		$text = str_replace(array (
			"\r\n",
			"\r",
			"\n"), "<br>", $text);

		foreach ($this->BBCodes as $BBCode => $keys) {
			if ($BBCode == '[noparse]') {
				if (strpos($text, $BBCode) !== FALSE) {
					$text = preg_replace_callback($keys['pattern'], function ($matches) {
						return $this->escape_html($matches[1]);
					}, $text);
				}
			}
			elseif ($BBCode == '[tt]') {
				if (strpos($text, $BBCode) !== FALSE) {
					$text = preg_replace_callback($keys['pattern'], function ($matches) {

						return '<code>' . $this->escape_html($matches[1]) . '</code>';
					}, $text);
				}

			}
			elseif ($BBCode == '[code]') {
				if (strpos($text, $BBCode) !== FALSE) {
					$text = preg_replace_callback($keys['pattern'], function ($matches) {

						return self::syntax_highlighter($this->escape_html($matches[1]));
					}, $text);
				}

			}
			elseif ($BBCode == '[code=') {
				if (strpos($text, $BBCode) !== FALSE) {
					$text = preg_replace_callback($keys['pattern'], function ($matches) {

						return self::syntax_highlighter($this->escape_html($matches[2]), $matches[1]);
					}, $text);
				}

			}
			elseif ($BBCode == '[img]') {
				if (strpos($text, $BBCode) !== FALSE) {
					$text = preg_replace_callback($keys['pattern'], function ($matches) {
						$return = '';
						$url = trim($matches[1]);
						if (self::check_url($url)) {
							$return = '<img src="' . $url . '" alt="user-provided image" class="bbCode_img">'; // ToDo: Update alt="" text
						}

						return $return;
					}, $text);
				}
			}
			elseif ($BBCode == '[url]') {
				if (strpos($text, $BBCode) !== FALSE) {
					$text = preg_replace_callback($keys['pattern'], function ($matches) {
						$return = '';
						$url = trim($matches[1]);
						if (self::check_url($url)) {
							$return = '<a href="' . $url . '" target="_blank">' . $url . '</a>';
						}

						return $return;
					}, $text);
				}

			}
			elseif ($BBCode == '[url=') {
				if (strpos($text, $BBCode) !== FALSE) {
					$text = preg_replace_callback($keys['pattern'], function ($matches) {
						$return = '';
						$url = trim($matches[1]);
						$text = $matches[2];
						if (self::check_url($url)) {
							$return = '<a href="' . $url . '" target="_blank">' . $text . '</a>';
						}

						return $return;
					}, $text);
				}

			}
			elseif ($BBCode == '[media=') {

				if (strpos($text, $BBCode) !== FALSE) {
					$text = preg_replace_callback($keys['pattern'], function ($matches) {
						$return = "";
						if (strtolower($matches[1]) == 'youtube') {
							$return = '<iframe width="560" height="315" src="//www.youtube.com/embed/' . $matches[2] . '?rel=0" frameborder="0" allowfullscreen></iframe>';
						}
						elseif (strtolower($matches[1]) == 'vimeo') {
							$return = '<iframe src="//player.vimeo.com/video/' . $matches[2] . '?title=0&amp;byline=0&amp;portrait=0&amp;badge=0&amp;color=ffffff" width="560" height="240" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
						}
						elseif (strtolower($matches[1]) == 'twitch') {
							$return = '<iframe src="https://player.twitch.tv/?channel=' . strtolower($matches[2]) . '" frameborder="0" scrolling="no" height="378" width="620"></iframe><a href="https://www.twitch.tv/' . strtolower($matches[2]) . '?tt_medium=live_embed&tt_content=text_link" style="padding:2px 0px 4px; display:block; width:345px; font-weight:normal; font-size:10px; text-decoration:underline;">Watch live video from ' . $matches[2] . ' on www.twitch.tv</a>';
						}

						return $return;
					}, $text);
				}

			}
			else {
				if (strpos($text, $BBCode) !== FALSE) {
					$text = preg_replace($keys['pattern'], $keys['replacement'], $text);
				}
			}
		}

		return $text;
	}

	private function escape_html ($text)
	{
		$text = str_replace('<br>', "\n", $text);
		$text = htmlentities($text, NULL, NULL, FALSE);

		return $text;
	}

}