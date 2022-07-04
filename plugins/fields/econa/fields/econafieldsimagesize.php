<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldEconaFieldsImageSize extends JFormFieldList
{
	public $type = 'EconaFieldsImageSize';

	public function getOptions()
	{
		$options = array();
		$application = JFactory::getApplication();
		$id = $application->input->getInt('id');
		$model = JModelLegacy::getInstance('Field', 'FieldsModel', array('ignore_request' => true));
		$field = $model->getItem($id);
		$fieldparams = $field->fieldparams;

		if (is_array($fieldparams) && isset($fieldparams['sizes']) && $fieldparams['sizes'])
		{
			if (is_string($fieldparams['sizes']))
			{
				$sizes = json_decode($fieldparams['sizes']);

				if (is_object($sizes) && isset($sizes->identifier) && is_array($sizes->identifier))
				{
					foreach ($sizes->identifier as $key => $identifier)
					{
						$options[] = JHtml::_('select.option', $identifier, $sizes->label[$key]);
					}
				}
			}
			elseif (is_array($fieldparams['sizes']))
			{
				foreach ($fieldparams['sizes'] as $entry)
				{
					$options[] = JHtml::_('select.option', $entry['identifier'], $entry['label']);
				}
			}

			$options = array_merge(parent::getOptions(), $options);
		}

		return $options;
	}
}
