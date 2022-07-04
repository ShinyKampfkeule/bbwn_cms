<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
defined('_JEXEC') or die;

use Joomla\Registry\Registry;

jimport('joomla.form.formfield');

class JFormFieldEcona extends JFormField
{
	public $type = 'Econa';

	public function getInput()
	{
		if (!$this->value)
		{
			$this->value = '{}';
		}

		$data = json_decode($this->value);

		if (!isset($data->key))
		{
			$data->key = uniqid('plg_fields_econa_');
		}

		if (!isset($data->itemId))
		{
			$data->itemId = $this->form->getValue('id');
		}

		if (!isset($data->context))
		{
			$data->context = $this->form->getName();
		}

		if (!isset($data->filename))
		{
			$data->filename = '';
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('fieldparams'));
		$query->from($db->qn('#__fields'));
		$query->where($db->qn('name') . ' = ' . $db->q($this->fieldname));
		$db->setQuery($query);
		$result = $db->loadResult();
		$params = new Registry($result);

		$ratios = array();
		$defaults = array();
		$defaults[] = (object) array('ratio' => '16/9');
		$defaults[] = (object) array('ratio' => '4/3');
		$defaults[] = (object) array('ratio' => '1/1');

		$aspect_ratios = $params->get('aspect_ratios', $defaults);

		foreach ($aspect_ratios as $aspect_ratio_entry)
		{
			if (!$aspect_ratio_entry->ratio)
			{
				continue;
			}
			$entry = new stdClass;
			$entry->label = $aspect_ratio_entry->ratio;
			$parts = explode('/', $aspect_ratio_entry->ratio);
			$entry->value = $parts[0] / $parts[1];
			$ratios[] = $entry;
		}
		$data->ratios = $ratios;
		$data->free_ratio = $params->get('free_aspect_ratio', 1);

		$data->params = $params;

		$this->value = json_encode($data);
		$required = $this->required ? 'required aria-required="true"' : '';
		$validator = '<input ' . $required . ' type="hidden" id="' . $this->id . '" value="' . htmlspecialchars($data->filename, ENT_COMPAT, 'UTF-8') . '" data-role="econa-validator" />';
		$field = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '" data-econa="' . $data->key . '" />';
		$form = JForm::getInstance('fields.econa.' . $data->key, JPATH_SITE . '/plugins/fields/econa/forms/econa.xml', array('control' => $data->key));

		// JCE media manager support
		$application = JFactory::getApplication();
		$application->triggerEvent('onPlgSystemJceContentPrepareForm', array($form, $data));

		$form->bind(array('econa' => $data));
		$form->setValue('preview', 'econa', $this->value);

		return '<div class="econa-field-container">' . $form->renderFieldset('', array('hiddenLabel' => true)) . $field . $validator . '</div>';
	}
}
