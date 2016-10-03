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

namespace Iko;

class template
{
	private static $instance = null;

	public static function get_instance()
	{
		if (self::$instance == null) {
			self::$instance = new template();
		}

		return self::$instance;
	}


	private $template_id;
	private $template_author;
	private $template_directory;
	private $template_name;
	private $template_required_version;
	private $template_version;
	private $template;
	private $param = array ();
	private $entity = array ();

	private function __construct()
	{
		$this->template_id = 1;
		// $this->template_id = Core::template;
		// ToDo: Get template which the user wants to have

		// Variablen aus Tabelle
		try {
			$statement = Core::$PDO->prepare("SELECT * FROM iko_templates WHERE template_id = :template_id");
			$statement->bindParam(':template_id', $this->template_id);
			$statement->execute();
			$result = $statement->fetch();

			$this->template_author = $result['template_author'];
			$this->template_directory = $result['template_directory'];
			$this->template_name = $result['template_name'];
			$this->template_required_version = $result['template_required_core_version'];
			$this->template_version = $result['template_version'];
		}
		catch (\PDOException $exception) {
			throw new Exception("Error #1234: " . $exception);
		}

		// check directory & check required version core::version <= $template_required_version
		if (file_exists(Core::$basepath . 'template/' . $this->template_directory . '/template.html') && version_compare(Core::version, $this->template_required_version, '<=')) {
			// ToDo: $template static?
			$this->template = file_get_contents(Core::$basepath . '/template/' . $this->template_directory . '/template.html');
		}
		else {
			throw new Exception("Error #4321: The version of the template is lower than the version of the core. Please update your template.");
			// ToDo: Set user template to default template core::User->set_template(default);
		}
	}

	private function bladeSyntax($string)
	{
		$syntax_blade = array (
			'/{{ (.*) }}/',
			// text or variable
			'/{{-v (.*) = (.*) }}/',
			'/(\s*)@(if|elseif|foreach|for|while)(\s*\(.*\))/',
			'/(\s*)@(endif|endforeach|endfor|endwhile)(\s*)/',
			'/(\s*)@(else)(\s*)/',
			'/(\s*)@unless(\s*\(.*\))/',
			'/%% (.*) %%/',
			// Param
			'/§§ (.*) §§/'); // Entity
		$syntax_php = array (
			'<?php echo $1; ?>',
			'<?php $1 = $2; ?>',
			'$1<?php $2$3: ?>',
			'$1<?php $2; ?>',
			'$1<?php $2: ?>$3',
			'$1<?php if( ! ($2)): ?>',
			'<?php echo $this->param["$1"]; ?>',
			'<?php echo $this->entity["$1"]; ?>');
		$string = preg_replace($syntax_blade, $syntax_php, $string);
		//@empty
		$string = str_replace('@empty', '<?php endforeach; ?><?php else: ?>', $string);
		//@forelse
		$string = str_replace('@endforelse', '<?php endif; ?>', $string);
		//@endunless
		$string = str_replace('@endunless', '<?php endif; ?>', $string);

		ob_start();
		eval('?>' . $string . '');
		$string = ob_get_clean();
		@ob_end_clean();

		return $string;
	}

	public function entity($entity, $parameters)
	{
		if (file_exists(Core::$basepath . 'template/' . $this->template_directory . '/entities.html')) {
			$entities = file_get_contents(Core::$basepath . 'template/' . $this->template_directory . '/entities.html');
			preg_match("/<!-- start:" . $entity . " -->(.*)<!-- end:" . $entity . " -->/is", $entities, $unparsed_entity);
			foreach ($parameters as $parameter => $value) {
				$param[$parameter] = $value;
			}

			$parsed_entity = $this->bladeSyntax($unparsed_entity[1]);
			$this->entity[$entity] = $parsed_entity;


		}
	}

	public function __toString()
	{
		return $this->bladeSyntax($this->template);
	}

	public function __get($var)
	{
		if (isset($this->param[$var])) {
			return $this->param[$var];
		}
		else {
			return "";
		}
	}

	public function __set($var, $value)
	{
		$this->param[$var] = $value;
	}
}