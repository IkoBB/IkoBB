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

namespace iko\cms;

use iko\Core;
use iko\PDO;
use iko\config;
use iko\module;
use iko\Exception;

/**
 * Class template
 * @package Iko
 */
class template
{
	const table = "{prefix}templates";

	/**
	 * @var null
	 */
	private static $instance = NULL;

	/**
	 * Initiation of the class
	 * Only one instance of the template class is allowed
	 *
	 * @return \Iko\cms\template
	 */
	public static function get_instance (): template
	{
		if (self::$instance == NULL) {
			self::$instance = new template();
		}

		return self::$instance;
	}

	/**
	 * Function to check if the entered template_id exists
	 *
	 * @param $id
	 *
	 * @return bool
	 * @throws \iko\Exception
	 */
	public static function template_exists ($id)
	{
		try {
			$statement = Core::$PDO->prepare("SELECT template_id FROM " . self::table . " WHERE template_id = :template_id");
			$statement->bindParam(':template_id', $id);
			$statement->execute();
			$result = $statement->fetch(PDO::FETCH_ASSOC);
			$return = $result['template_id'];
		}
		catch (\PDOException $exception) {
			throw new Exception("Error #1234: " . $exception);
		}
		if (is_int($return) && $return > 0) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	/**
	 * Installs new template
	 *
	 * @param $name
	 * @param $author
	 * @param $version
	 * @param $directory
	 * @param $core_version
	 * @param $zip_file
	 *
	 * @throws \iko\Exception
	 */
	public static function install ($name, $author, $version, $directory, $core_version, &$zip_file)
	{
		if ($zip_file['error'] == 1) {
			$extension = substr($zip_file['tmp_name'], -4);
			if ($extension == '.zip' && ($zip_file['type'] == 'application/zip' || $zip_file['type'] == 'application/octet-stream')) {

				$filename = basename($zip_file['name']);
				move_uploaded_file($zip_file['tmp_name'], 'templates/' . $directory . '/' . $filename);

				$zip = new \ZipArchive();
				$resource = $zip->open('templates/' . $directory . '/' . $filename);

				if ($resource === TRUE) {
					for ($i = 0; $i < $zip->numFiles; $i++) {
						if (basename($zip->getNameIndex($i)) == 'template.html') {
							$file_template = TRUE;
						}
						elseif (basename($zip->getNameIndex($i)) == 'entities.html') {
							$file_entity = TRUE;
						}

						if ($file_entity === TRUE && $file_template === TRUE) {
							$zip->extractTo('templates/' . $directory . '/');
							$zip->close();
							break;
						}
					}
				}


				try {
					$statement = Core::$PDO->prepare("INSERT INTO " . self::table . " (template_name, template_author, template_directory, template_core_version) VALUE (:template_name, :template_author, :template_directory, :template_core_version)");
					$statement->bindParam(':template_name', $name);
					$statement->bindParam(':template_author', $author);
					$statement->bindParam(':template_version', $version);
					$statement->bindParam(':template_directory', $directory);
					$statement->bindParam(':template_core_version', $core_version);

					$statement->execute();

				}
				catch (\PDOException $exception) {
					throw new Exception("Error #1234: " . $exception);
				}
			}
		}
	}

	public static function add_sidebar() {
		$template = self::get_instance();
		$template->entity("sidebar", array ());
		$template->width_content = '9';
	}

	public static function no_sidebar() {
		$template = self::get_instance();
		$template->width_content = '12';
	}

	public static function add_breadcrumb($name, $link) {
		$template = self::get_instance();
		$template->breadcrumbs .= $template->entity("breadcrumb", array(
			"breadcrumb_name" => $name,
			"breadcrumb_link" => $link
		), TRUE);
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
				$user = \iko\user\User::get_session();
				if ($user !== FALSE) {
					// Get the template of the user
					$this->set_template($user->get_template());
				}
				else {
					// if user is not logged in load default template
					$this->set_template(0);
				}
			}
			else {
				// load default template when no user module is activated
				$this->set_template(0);
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
			if (!isset($this->template_directory) || $this->template_directory == '') {
				// Get all template variables from table
				try {
					$statement = Core::$PDO->prepare("SELECT * FROM " . self::table . " WHERE template_id = :template_id");
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
		eval('namespace iko; ?>' . $string . '');
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
	 * Sets the template to the wanted template
	 * If template does not exist, default template will be loaded
	 */
	private function set_template ($id)
	{
		if (self::template_exists($id) === TRUE) {
			$this->template_id = $id;
		}
		else {
			$config = config::load("pdo", "cms");
			$this->template_id = $config->site_template;
		}
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