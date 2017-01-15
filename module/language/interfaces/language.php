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
 * Time: 23:28
 */
namespace iko\language;
interface iLanguage
{
	public function is_supported_language (string $lang): bool;

	public function get_current (): string;

	public function get_languages (): array;
}