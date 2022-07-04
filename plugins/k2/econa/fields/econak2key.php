<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('hidden');

class JFormFieldEconaK2Key extends JFormFieldHidden
{
	public $type = 'EconaK2Key';

	public function getInput()
	{
		$this->value = uniqid('plg_k2_econa_');

		return parent::getInput();
	}

	public function getLabel()
	{
		return;
	}
}
