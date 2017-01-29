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

use iko\{
	Core, PDO, config, module, Exception, session, user\User
};

/**
 * Class template
 * @package Iko
 */
class template
{
	/**
	 *
	 */
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
			$statement = Core::PDO()->prepare("SELECT template_id FROM " . self::table . " WHERE template_id = :template_id");
			$statement->bindParam(':template_id', $id);
			$statement->execute();
			$result = $statement->fetch(PDO::FETCH_ASSOC);
			$return = $result['template_id'];
		}
		catch (Exception $exception) {
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
					$file_template = FALSE;
					$file_entity = FALSE;
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
					$statement = Core::PDO()->prepare("INSERT INTO " . self::table . " (template_name, template_author, template_directory, template_core_version) VALUE (:template_name, :template_author, :template_directory, :template_core_version)");
					$statement->bindParam(':template_name', $name);
					$statement->bindParam(':template_author', $author);
					$statement->bindParam(':template_version', $version);
					$statement->bindParam(':template_directory', $directory);
					$statement->bindParam(':template_core_version', $core_version);

					$statement->execute();

				}
				catch (Exception $exception) {
					$template = template::get_instance();
					$template->error .= "Error CMS-0001: Failed to insert new template into database";
				}
			}
		}
	}

	public static function add_sidebar (string $sidebar = "user_sidebar", string $module = NULL)
	{
		$entity = new entity();
		$entity->get_template_entity($sidebar, $module);
		/*
		if (session::is_logged_in() !== FALSE) {
			// Check if user is logged in
			$user = User::get_session();
			if ($user !== FALSE) {
				$template->entity("user-sidebar", array ());
			}
			else {
				$template->entity("login-sidebar", array ());
			}
		}
		else {
			$template->entity("login-sidebar", array ());
		}*/
		$template = template::get_instance();
		$template->width_content = '9';
	}

	public static function no_sidebar ()
	{
		$template = self::get_instance();
		$template->width_content = '12';
	}

	/**
	 * @param $name
	 * @param $link
	 */
	public static function add_breadcrumb ($name, $link)
	{
		$template = self::get_instance();
		$entity = new entity();
		$template->breadcrumbs .= $entity->return_entity("cms.breadcrumb", array (
			"breadcrumb_name" => $name,
			"breadcrumb_link" => $link));
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

			if (session::is_logged_in() !== FALSE) {
				// Check if user is logged in
				$user = User::get_session();

				if ($user !== FALSE) {
					// Get the template of the user
					$this->set_template($user->get_template());
					$this->current_user_avatar = $user->get_avatar();
					$this->current_user_name = $user->get_name();
				}
				else {
					// if user is not logged in load default template
					$this->set_template(0);
					$this->current_user_name = "Gast";
					$this->current_user_avatar = User::get(1)->get_avatar();
				}
			}
			else {
				// load default template when no user module is activated
				$this->set_template(0);
				$this->current_user_name = "Guest";
				$this->current_user_avatar = User::get(1)->get_avatar();
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
					$statement = Core::PDO()->prepare("SELECT * FROM " . self::table . " WHERE template_id = :template_id");
					$statement->bindParam(':template_id', $this->template_id);
					$statement->execute();
					$result = $statement->fetch(PDO::FETCH_ASSOC);
					foreach ($result as $key => $value) {
						$this->{$key} = $value;
					}
				}
				catch (Exception $exception) {
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
	 * @param string       $string
	 * @param array | NULL $params
	 *
	 * @return mixed|string
	 */
	public function bladeSyntax (string $string, $params = NULL, $entities = array ())
	{
		if ($params === NULL) {
			$params = $this->param;
		}
		if ($entities === NULL) {
			$entities = $this->entity;
		}


		$syntax_blade = array (
			'/{{ (.*) }}/U',
			// text or variable
			'/{{-v (.*) = (.*) }}/U',
			'/(\s*)@(if|elseif|foreach|for|while)(\s*\(.*\))/',
			'/(\s*)@(endif|endforeach|endfor|endwhile)(\s*)/',
			'/(\s*)@(else)(\s*)/',
			'/(\s*)@unless(\s*\(.*\))/',
			'/§§ (.*) §§/U',
			// Entity
			'/%% (.*) %%/U');// Param


		$syntax_php = array (
			'<?php echo $1; ?>',
			'<?php $1 = $2; ?>',
			'$1<?php $2$3: ?>',
			'$1<?php $2; ?>',
			'$1<?php $2: ?>$3',
			'$1<?php if( ! ($2)): ?>',
			'<?php echo (isset($this->entity["$1"]))?$this->entity["$1"]:""; ?>',
			'<?php echo (isset($params["$1"]))?$params["$1"]:""; ?>');

		foreach ($entities as $name => $content) {
			if (preg_match('/§§ (.*) §§/U', $content) != 0 || preg_match('/%% (.*) %%/U', $content) != 0) {
				$this->entity[ $name ] = $this->bladeSyntax($content, NULL, array ());
			}
		}

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
	 * @param $entity
	 * @param $content
	 */
	public function add_entity ($entity, $content)
	{
		$this->entity[ $entity ] = $content;
	}

	/**
	 * Sets the template to the wanted template
	 * If template does not exist, default template will be loaded
	 *
	 * @param int $id
	 */
	private function set_template (int $id)
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
		$entity = new entity();
		if ($this->error != "") $entity->get_template_entity("cms.error");
		if ($this->warning != "") $entity->get_template_entity("cms.warning");
		if ($this->notice != "") $entity->get_template_entity("cms.notice");

		if (Core::Config()->get("show_memory")) {
			$value = memory_get_usage(TRUE);
			$unit = array (
				'B',
				'KB',
				'MB',
				'GB',
				'TB',
				'PB');

			$value = @round($value / pow(1024, ($i = floor(log($value, 1024)))), 2) . ' ' . $unit[ $i ];
			$this->used_memory = "Memory: ".$value;
		}

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
		return $this->output_parameter($var);
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

	public function output_parameter (string $parameter)
	{
		if (array_key_exists($parameter, $this->param)) {
			return $this->param[ $parameter ];
		}
		else {
			return FALSE;
		}

	}

	/**
	 * @return string
	 */
	public function get_directory (): string
	{
		return $this->template_directory;
	}
}