<?php
/**
 *
 * This file is part of IkoBB Forum and belongs to the module <CMS>.
 *
 * @copyright (c) 2016 IkoBB <https://www.ikobb.de>
 * @license       GNU General Public License, version 3 (GPL-3.0)
 *
 * For full copyright and license information, please see
 * the LICENSE file.
 *
 */

namespace Iko;


class cms
{
	const table = "{prefix}users";
	const column_id = "cms_id";

	public static function exists ($site_id)
	{
		if (is_numeric($site_id) && $site_id != 0 && $site_id != NULL) {
			$statement = Core::$PDO->prepare("SELECT " . self::column_id . " FROM " . self::table . " WHERE " . self::column_id . " = :id");
			$statement->bindParam(':id', $site_id);
			$statement->execute();
			if ($statement->rowCount() > 0) {
				return TRUE;
			}
			else {
				return FALSE;
			}
		}
		else {
			return FALSE;
		}
	}

	function _construct ($site_id)
	{
		if (isset($site_id) && self::exists($site_id) === TRUE) {
			$this->load_content($site_id);
		}
		else {
			$this->load_content(1);
		}
	}

	private function load_content ($site_id)
	{
		$statement = Core::$PDO->prepare("SELECT * FROM " . self::table . " WHERE " . self::column_id . " = :id");
		$statement->bindParam(':id', $site_id);
		$statement->execute();
		$site = $statement->fetch(PDO::FETCH_ASSOC);

		$template = template::get_instance();
		$template->entity("cms_site", array (
			"title"   => $site['cms_title'],
			"content" => parser::bbCodes($site["cms_content"])));
	}
}