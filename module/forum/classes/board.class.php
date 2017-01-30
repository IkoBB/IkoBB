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

class board extends structure
{
	const table = '{prefix}forum_board';
	const id = 'forum_board_id';

	const column_name = "forum_board_name";
	const column_description = "forum_board_description";
	const column_parent = "forum_board_parent";

	protected static $cache = array ();
	protected static $cache_exist = array ();

	public static function get ($id = 0, $reload = FALSE): board
	{
		return parent::get($id, $reload);
	}
}