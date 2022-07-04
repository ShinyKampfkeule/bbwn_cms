<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
defined('_JEXEC') or die;

class EconaContentTableImage extends JTable
{
	public function __construct(&$_db)
	{
		parent::__construct('#__econa', array('resourceId', 'resourceType'), $_db);
		$this->resourceType = 'com_content.article';
	}

	public function onAfterLoad()
	{
		$this->sizes = json_decode($this->sizes);
	}
}
