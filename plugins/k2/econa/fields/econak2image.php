<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
defined('_JEXEC') or die;

use Joomla\Registry\Registry;

jimport('joomla.form.formfield');

class JFormFieldEconaK2Image extends JFormField
{
	public $type = 'EconaK2Image';

	public function getInput()
	{
		$application = JFactory::getApplication();
		$itemId = $application->input->getInt('cid');

		JTable::addIncludePath(JPATH_SITE . '/plugins/k2/econa/tables');
		$row = JTable::getInstance('Image', 'EconaK2Table');
		$row->load(array('resourceId' => $itemId, 'resourceType' => 'com_k2.item'));
		$image = null;

		// Detect K2 version
		$component = JComponentHelper::getComponent('com_k2');
		$extension = JTable::getInstance('extension');
		$extension->load($component->id);
		$manifest = json_decode($extension->manifest_cache);
		$k2Version = $manifest->version;

		// Get extra aspect ratios
		$plugin = JPluginHelper::getPlugin('k2', 'econa');
		$params = new Registry($plugin->params);

		$ratios = array();
		$defaults = array();
		$defaults[] = (object) array('ratio' => '16/9');
		$defaults[] = (object) array('ratio' => '4/3');
		$defaults[] = (object) array('ratio' => '1/1');
		$aspect_ratios = $params->get('aspect_ratios', $defaults);

		foreach ($aspect_ratios as $aspect_ratio_entry)
		{
			$entry = new stdClass;
			$entry->label = $aspect_ratio_entry->ratio;
			$parts = explode('/', $aspect_ratio_entry->ratio);
			$entry->value = $parts[0] / $parts[1];
			$ratios[] = $entry;
		}

		// Econa image
		if ($row->filename)
		{
			$image = JUri::root(true) . '/media/k2/items/cache/' . $row->filename . '_XL.jpg?t=' . time();
		}

		// Core K2 image
		$hash = md5('Image' . $itemId);

		if (is_null($image) && JFile::exists(JPATH_SITE . '/media/k2/items/cache/' . $hash . '_XL.jpg'))
		{
			$image = JUri::root(true) . '/media/k2/items/cache/' . $hash . '_XL.jpg?t=' . time();
		}
		JHtml::_('jquery.framework');
		$document = JFactory::getDocument();
		$document->addStyleSheet(JUri::root(true) . '/plugins/k2/econa/css/cropper.min.css');
		$document->addStyleSheet(JUri::root(true) . '/plugins/k2/econa/css/app.css');
		$document->addScript(JUri::root(true) . '/plugins/k2/econa/js/cropper.min.js');
		$document->addScript(JUri::root(true) . '/plugins/k2/econa/js/jquery.ui.widget.js');
		$document->addScript(JUri::root(true) . '/plugins/k2/econa/js/jquery.fileupload.js');
		$document->addScript(JUri::root(true) . '/plugins/k2/econa/js/jquery.iframe-transport.js');
		$document->addScriptDeclaration("var econaSessionToken = '" . JSession::getFormToken() . "'; var econaK2Version = '" . $k2Version . "';");
		$document->addScript(JUri::root(true) . '/plugins/k2/econa/js/app.js');

		ob_start();
		include dirname(__FILE__) . '/tmpl/image.php';
		$contents = ob_get_clean();

		return $contents;
	}

	public function getLabel()
	{
		return;
	}
}
