<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('spacer');

class JFormFieldEconaFieldsSpacer extends JFormFieldSpacer
{
	public $type = 'EconaFieldsSpacer';

	public function getLabel()
	{
		$this->element['label'] = $this->value;

		return parent::getLabel();
	}
}
