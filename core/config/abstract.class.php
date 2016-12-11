<?php
/**
 *
 * This file is part of IkoBB Forum and belongs to the module <Iko>.
 *
 * @copyright (c) IkoBB <https://www.ikobb.de>
 * @license       GNU General Public License, version 3 (GPL-3.0)
 *
 * For full copyright and license information, please see
 * the LICENSE file.
 *
 */
/**
 * @author Marcel
 *
 */
namespace iko;

abstract class config_loader implements config_interface
{
	protected $config_class;

	/**
	 * config_loader constructor.
	 *
	 * @param $config_class
	 */
	protected function __construct ($config_class)
	{
		$this->config_class = $config_class;
	}

	/**
	 * {@inheritDoc}
	 * @see \Iko\config_interface::set()
	 */
	public abstract function set ($name, $value, $comment = "");

	/**
	 * {@inheritDoc}
	 * @see \Iko\config_interface::add()
	 */
	public abstract function add ($name, $value, $comment);

	/**
	 * @return mixed
	 */
	protected abstract function load_Config ();

	/**
	 * @return mixed
	 */
	public function get_config_class ()
	{
		return $this->config_class;
	}
}
