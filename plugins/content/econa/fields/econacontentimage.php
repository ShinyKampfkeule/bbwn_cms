<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
defined('_JEXEC') or die;

use Joomla\Registry\Registry;

jimport('joomla.form.formfield');

class JFormFieldEconaContentImage extends JFormField
{
	public $type = 'EconaContentImage';

	public function getInput()
	{
		$application = JFactory::getApplication();

		if ($application->isClient('administrator'))
		{
			$articleId = $application->input->getInt('id');
		}
		else
		{
			$articleId = $application->input->getInt('a_id');
		}

		JTable::addIncludePath(JPATH_SITE . '/plugins/content/econa/tables');
		$row = JTable::getInstance('Image', 'EconaContentTable');
		$row->load(array('resourceId' => $articleId,'resourceType' => 'com_content.article'));

		$plugin = JPluginHelper::getPlugin('content', 'econa');
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

		$image = null;

		if ($row->filename)
		{
			$timestamp = JFactory::getDate($this->form->getValue('modified'))->toUnix();
			$identifier = end($row->sizes);
			reset($row->sizes);
			$image = JUri::root(true) . '/images/econa/content/article/' . $row->filename . '_' . $identifier . '.' . $row->extension . '?t=' . $timestamp;
		}
		JHtml::_('jquery.framework');
		$document = JFactory::getDocument();
		$document->addStyleSheet(JUri::root(true) . '/plugins/content/econa/css/cropper.min.css');
		$document->addStyleSheet(JUri::root(true) . '/plugins/content/econa/css/featherlight.min.css');
		$document->addStyleSheet(JUri::root(true) . '/plugins/content/econa/css/app.css');
		$document->addScript(JUri::root(true) . '/plugins/content/econa/js/cropper.min.js');
		$document->addScript(JUri::root(true) . '/plugins/content/econa/js/featherlight.min.js');
		$document->addScript(JUri::root(true) . '/plugins/content/econa/js/jquery.ui.widget.js');
		$document->addScript(JUri::root(true) . '/plugins/content/econa/js/jquery.fileupload.js');
		$document->addScript(JUri::root(true) . '/plugins/content/econa/js/jquery.iframe-transport.js');
		$document->addScriptDeclaration("var econaSessionToken = '" . JSession::getFormToken() . "';");
		$document->addScript(JUri::root(true) . '/plugins/content/econa/js/app.js?v=1.5.0');

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
