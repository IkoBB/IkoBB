<?php
/**
 * This file is part of IkoBB Forum and belongs to the module <Forum>.
 *
 * @copyright (c) 2017 IkoBB <https://www.ikobb.de>
 * @license       GNU General Public License, version 3 (GPL-3.0)
 *
 * For full copyright and license information, please see
 * the LICENSE file.
 *
 */


namespace iko\forum;

class category extends structure
{
	const table = '{prefix}forum_categories';
	const id = 'forum_category_id';

	const column_name = "forum_category_name";
	const column_description = "forum_category_description";
	const column_parent = "forum_category_parent";
	const column_parent_type = "forum_category_parent_type";

	protected static $cache = array ();
	protected static $cache_exist = array ();

	public static function get ($id = 0, $reload = FALSE): category
	{
		return parent::get($id, $reload);
	}

	public function get_child_category (): array
	{
		return array();
	}
}