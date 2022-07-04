<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldEconaContentImageSize extends JFormFieldList
{
	public $type = 'EconaContentImageSize';

	public function getOptions()
	{
		$options = array();

		$name = $this->form->getName();

		if ($name == 'com_categories.categorycom_content')
		{
			$sizes = json_decode($this->form->getValue('sizes', 'params'));
		}
		else
		{
			$plugin = JPluginHelper::getPlugin('content', 'econa');
			if(!$plugin)
			{
				$plugin = (object) array('params' => '');
			}
			$params = is_object($plugin->params) ? $plugin->params : json_decode($plugin->params);
			if(is_string($params->sizes))
			{
				$sizes = json_decode($params->sizes);
			}
		}

		if (is_object($sizes) && isset($sizes->identifier) && is_array($sizes->identifier))
		{
			foreach ($sizes->identifier as $key => $identifier)
			{
				$options[] = JHtml::_('select.option', $identifier, $sizes->label[$key]);
			}
		}

		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
