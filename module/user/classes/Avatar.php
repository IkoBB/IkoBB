<?php
/**
 *
 * This file is part of IkoBB Forum and belongs to the module <User>.
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
 * Date: 18.12.2016
 * Time: 21:02
 */
namespace iko\user\profile;

use iko\module;
use iko\user\iUser;
use iko\user\User;

class Avatar implements iAvatar// ToDo: Create a concept for User Avatar and how to save the needed data over the user class
{
	private static $types = array (
		"file",
		"gravatar",
		"identicon",
		"mm",
		"wavatar");
	private $data;
	private $user;
	public function __construct (User $user, string $data)
	{
		$this->user = $user;
		try {
			$this->data = unserialize($data);
		}
		catch (\Exception $ex) {

		}
		finally {
			if ($this->data == NULL || $this->data == FALSE) {
				$this->data = array ("type" => "default");
			}
		}
	}

	public function get (): string
	{
		if ($this->data["type"] != NULL) {
			$name = $this->data["value"] ?? $this->get_user()->get_name();
			switch ($this->data["type"]) {
				case "gravatar":
					$string = $this->get_gravatar($this->get_user()->get_email());
				break;
				case "identicon":
					$string = $this->get_gravatar($name, 80, "identicon");
				break;
				case "mm":
					$string = $this->get_gravatar($name, 80, "mm");
				break;
				case "monsterid":
					$string = $this->get_gravatar($name, 80, "monsterid");
				break;
				case "wavatar":
					$string = $this->get_gravatar($name, 80, "wavatar");
				break;
				case "file":
					$filename = module::get("user")->get_path() . "user_images/" . $this->user->get_id() . ".png";
					if (is_file($filename)) {
						$string = $filename;
					}
					else {
						$string = $this->get_gravatar($this->user->get_email());
					}
				break;
				default:
					$string = $this->get_gravatar($this->user->get_email());
				break;
			}
		}
		else {
			$string = $this->get_gravatar($this->user->get_email());
		}

		return $string;
	}

	/**
	 * Get either a Gravatar URL or complete image tag for a specified email address.
	 *
	 * @param string $email The email address
	 * @param mixed  $s     Size in pixels, defaults to 80px [ 1 - 2048 ]
	 * @param string $d     Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
	 * @param string $r     Maximum rating (inclusive) [ g | pg | r | x ]
	 * @param bool   $img   True to return a complete IMG tag False for just the URL
	 * @param array  $atts  Optional, additional key/value attributes to include in the IMG tag
	 *
	 * @return String containing either just a URL or a complete image tag
	 * @source https://gravatar.com/site/implement/images/php/
	 */
	private function get_gravatar ($email, $s = 80, $d = 'mm', $r = 'g', $img = FALSE, $atts = array ())
	{
		$url = 'https://www.gravatar.com/avatar/';
		$url .= md5(strtolower(trim($email)));
		$url .= "?s=$s&d=$d&r=$r";
		if ($img) {
			$url = '<img src="' . $url . '"';
			foreach ($atts as $key => $val) {
				$url .= ' ' . $key . '="' . $val . '"';
			}
			$url .= ' />';
		}

		return $url;
	}

	/**
	 * @param $type
	 * @param $values
	 *
	 * @return bool
	 *
	 * @permissions iko.user.set.user_avatar
	 *              Own setting don't need Permissions
	 */
	public function set ($type, $values): bool
	{
		return $this->user->set_avatar($this->convert($type, $values));
	}

	public function convert ($type, $values)
	{
		if (array_search($type, self::$types) !== FALSE) {
			$data = array ("type" => $type);
			if ($type != "file" && $type != "gravatar") {
				$data["value"] = $values;
			}

			return $data;
		}

		return FALSE;
	}

	public function __toString ()
	{
		return $this->get();
	}
	public function get_user():iUser {
		return $this->user;
	}

}