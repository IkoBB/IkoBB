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

	/**
	 * Initiation of the class
	 * Only one instance of the class is allowed
	 *
	 * @return \Iko\template|null
	 */
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
	private $template_required_core_version;
	private $template_version;
	private $template;
	private $param = array ();
	private $entity = array ();

	/**
	 * template constructor.
	 * - Defines template_id
	 * - Loads the information of the template
	 * - Loads the template before parsing
	 *
	 * @throws \Iko\Exception
	 */
	private function __construct()
	{
		if (strpos(Core::$currentfile, Core::$adminpath) === false) {
			// ToDo: Get template which the user wants to have
			// $this->template_id = Core::template;
			$this->template_id = 1;
			// Get all variables from table
			try {
				$statement = Core::$PDO->prepare("SELECT * FROM iko_templates WHERE template_id = :template_id");
				$statement->bindParam(':template_id', $this->template_id);
				$statement->execute();
				$result = $statement->fetch(PDO::FETCH_ASSOC);
				foreach ($result as $key => $value) {
					$this->{$key} = $value;
				}
			}
			catch (\PDOException $exception) {
				throw new Exception("Error #1234: " . $exception);
			}
		}
		else {
			$this->template_id = 0;
			$this->template_author = 'IkoBB';
			$this->template_directory = 'admin';
			$this->template_name = 'admin';
			$this->template_required_core_version = '1.0.0a';
			$this->template_version = '1.0.0a';
		}

		// check directory & check required version core::version <= $template_required_version
		if (file_exists(Core::$basepath . 'template/' . $this->template_directory . '/template.html') && version_compare(Core::version, $this->template_required_core_version, '<=')) {
			// ToDo: $template static?
			$this->template = file_get_contents(Core::$basepath . '/template/' . $this->template_directory . '/template.html');
		}
		else {
			throw new Exception("Error #4321: The version of the template is lower than the version of the core. Please update your template.<br>" . Core::version . " | " . $this->template_required_core_version);
			// ToDo: Set user template to default template core::User->set_template(default);
		}
	}

	/**
	 * BladeSyntax Parser
	 * - {{ }} is used for text {{ This is a text }} or variables {{ $variable }}
	 * - %% %% is used for parameters like title %% title %%
	 * - §§ §§ is used for entities like sidebar §§ sidebar §§
	 *
	 * @param $string
	 *
	 * @return mixed|string
	 */
	private function bladeSyntax($string)
	{
		$syntax_blade = array (
			'/{{ (.*) }}/U',
			// text or variable
			'/{{-v (.*) = (.*) }}/U',
			'/(\s*)@(if|elseif|foreach|for|while)(\s*\(.*\))/',
			'/(\s*)@(endif|endforeach|endfor|endwhile)(\s*)/',
			'/(\s*)@(else)(\s*)/',
			'/(\s*)@unless(\s*\(.*\))/',
			'/%% (.*) %%/U',
			// Param
			'/§§ (.*) §§/U'); // Entity
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
		if (ob_get_length()) ob_end_clean();

		return $string;
	}

	/**
	 * Adds an entity to the template
	 *
	 * @param       $entity
	 * @param array $parameters
	 */
	public function entity($entity, $parameters = array ())
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

	/**
	 * @return mixed|string
	 *
	 * @ToDo: Variables like {{ Core::$version }} can't be used. Core is not defined.
	 */
	public function __toString()
	{
		return $this->bladeSyntax($this->template);
	}

	/**
	 * @param $var
	 *
	 * @return mixed|string
	 */
	public function __get($var)
	{
		if (isset($this->param[$var])) {
			return $this->param[$var];
		}
		else {
			return "";
		}
	}

	/**
	 * Adds a new parameter to template class
	 *
	 * @param $var
	 * @param $value
	 */
	public function __set($var, $value)
	{
		$this->param[$var] = $value;
	}
}