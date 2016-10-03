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
namespace Iko;

require_once 'core/core.php';
$class = new module("iko");


// Loading and checking template engine
$template_loader = new module("template");
$template_loader->check();
$template_loader->load();
$template = template::get_instance();

// Create 3 new parameters called content, title and userprofile. They can be accessed with blade syntax with %% content %%, %% title %% and %% userprofile %%.
$template->content = parser::bbCodes("[b]Welcome to the IkoBB demo and testing page[/b]

[u]Smileys:[/u]
:drool: :) :D :( <3 :sob: :bread: :flag_de: :flushed: :'( 
[code]:drool: :) :D :( <3 :sob: :bread: :flag_de: :flushed: :'([/code]

[u]Ordered list[/u]
[ol=I]
[*] Test
[*]2. Test
[/ol]
[code][ol=I]
[*] Test
[*]2. Test
[/ol][/code]

[u]PHP Code[/u]
[code=php]<?php
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
namespace Iko;

require_once 'core/core.php';
\$class = new module(\"iko\");


// Loading and checking template engine
\$template_loader = new module(\"template\");
\$template_loader->check();
\$template_loader->load();
\$template = template::get_instance();[/code]


[code=csharp]using System;

class Program
{
    static void Main(String[] args)
    {
        Console.WriteLine(\"Hello, world!\");
    }
}[/code]
");
$template->title = "IkoBB - Demo & Testing page";
$template->username = "N8boy";
$template->entity("TEST", array ());


// Output the template
echo $template;
