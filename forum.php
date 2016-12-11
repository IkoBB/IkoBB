<?php
/**
 *
 * This file is part of IkoBB Forum and belongs to the module <Iko>.
 *
 * @copyright (c) IkoBB <https://www.ikobb.de>
 * @license GNU General Public License, version 3 (GPL-3.0)
 *
 * For full copyright and license information, please see
 * the LICENSE file.
 *
 */
namespace iko;

require_once 'core/core.php';


module::request("cms");
module::request("user");
$template = template::get_instance();


$template->title = "IkoBB";
$template->sub_title = "Demo & Testing page";
$template->username = "Administrator";

$template->entity("sidebar",array());

$template->content = $template->entity("forum-list", array (), true);

echo $template;
