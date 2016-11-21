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

/**
 * Class template
 * @package Iko
 */
class template
{
	/**
	 * @var null
	 */
	private static $instance = NULL;

	/**
	 * Initiation of the class
	 * Only one instance of the template class is allowed
	 *
	 * @return \Iko\template|null
	 */
	public static function get_instance ()
	{
		if (self::$instance == NULL) {
			self::$instance = new template();
		}

		return self::$instance;
	}


	/**
	 * @var int
	 */
	private $template_id;
	/**
	 * @var string
	 */
	private $template_author;
	/**
	 * @var string
	 */
	private $template_directory;
	/**
	 * @var string
	 */
	private $template_name;
	/**
	 * @var string
	 */
	private $template_required_core_version;
	/**
	 * @var string
	 */
	private $template_version;
	/**
	 * @var string
	 */
	private $template;
	/**
	 * @var array
	 */
	private $param = array ();
	/**
	 * @var array
	 */
	private $entity = array ();

	/**
	 * Template constructor
	 * - Defines template_id
	 * - Loads the information of the template
	 * - Loads the template before parsing
	 *
	 * @throws \Iko\Exception
	 */
	private function __construct ()
	{
		if (strpos(Core::$currentfile, Core::$adminpath) === FALSE) {
			// Check if the user module is loaded
			if (module::load_status("user")) {
				// Check if user is logged in
				$user = User::get_session();
				if ($user !== FALSE) {
					// Get the template of the user
					$this->template_id = $user->get_template();
				}
				else { // if user is not logged in load default template
					$config = config::load("pdo", "cms");
					$this->template_id = $config->site_template;
				}
			}
			else { // load default template when no user module is activated
				$config = config::load("pdo", "cms");
				$this->template_id = $config->site_template;
			}
		}
		else {
			// If user is in admin path load admin template
			$this->template_id = 0;
			$this->template_author = 'IkoBB';
			$this->template_directory = 'admin';
			$this->template_name = 'admin';
			$this->template_required_core_version = '1.0.0a';
			$this->template_version = '1.0.0a';
		}


		if ($this->template_id !== NULL) {
			if ($this->template_id != 0) {
				// Get all template variables from table
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

			// check directory & check required version core::version <= $template_required_version
			if (file_exists(Core::$basepath . 'template/' . $this->template_directory . '/template.html') && version_compare(Core::version,
					$this->template_required_core_version, '<=')
			) {
				$this->template = file_get_contents(Core::$basepath . '/template/' . $this->template_directory . '/template.html');
			}
			else {
				throw new Exception("Error #4321: The version of the template is lower than the version of the core. Please update your template.<br>Core version: " . Core::version . "<br> Required Core Version: " . $this->template_required_core_version);
				// ToDo: Set user template to default template core::User->set_template(default);
			}
		}
		else {
			throw new Exception("Error #1234: No template id set.");
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
	private function bladeSyntax ($string)
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
			'<?php echo (isset($this->param["$1"]))?$this->param["$1"]:""; ?>',
			'<?php echo (isset($this->entity["$1"]))?$this->entity["$1"]:""; ?>');
		$string = preg_replace($syntax_blade, $syntax_php, $string);
		//@empty
		$string = str_replace('@empty', '<?php endforeach; ?><?php else: ?>', $string);
		//@forelse
		$string = str_replace('@endforelse', '<?php endif; ?>', $string);
		//@endunless
		$string = str_replace('@endunless', '<?php endif; ?>', $string);

		ob_start();
		eval('namespace Iko; ?>' . $string . '');
		$string = ob_get_clean();
		if (ob_get_length()) ob_end_clean();

		return $string;
	}

	/**
	 * Adds an entity to the template
	 *
	 * @param       $entity
	 * @param array $parameters
	 * @param bool  $return
	 *
	 * @return bool
	 */
	public function entity ($entity, $parameters = array (), $return = FALSE)
	{
		if (file_exists(Core::$basepath . 'template/' . $this->template_directory . '/entities.html')) {
			$entities = file_get_contents(Core::$basepath . 'template/' . $this->template_directory . '/entities.html');
			preg_match("/<!-- start:" . $entity . " -->(.*)<!-- end:" . $entity . " -->/is", $entities,
				$unparsed_entity);
			foreach ($parameters as $parameter => $value) {
				$this->param[ $parameter ] = $value;
			}
			$parsed_entity = $this->bladeSyntax($unparsed_entity[1]);
			if ($return === FALSE) {
				$this->entity[ $entity ] = $parsed_entity;
			}
			else {
				return $parsed_entity;
			}

		}

		return FALSE;
	}

	/**
	 * @return mixed|string
	 *
	 */
	public function __toString ()
	{
		return $this->bladeSyntax($this->template);
	}

	/**
	 * Gets the value of a parameter
	 *
	 * @param $var
	 *
	 * @return mixed|string
	 */
	public function __get ($var)
	{
		if (isset($this->param[ $var ])) {
			return $this->param[ $var ];
		}
		else {
			return "";
		}
	}

	/**
	 * Adds a new parameter to the template class
	 *
	 * @param $var
	 * @param $value
	 */
	public function __set ($var, $value)
	{
		$this->param[ $var ] = $value;
	}
}