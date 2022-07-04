<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
defined('_JEXEC') or die;

jimport('joomla.form.formfield');

class JFormFieldEconaFieldsImage extends JFormField
{
	public $type = 'EconaFieldsImage';

	public function getInput()
	{
		$image = null;
		$data = json_decode($this->value);

		if (is_null($data))
		{
			$data = (object) array('params' => array());
		}

		if ($data && isset($data->filename) && $data->filename && isset($data->itemId) && $data->itemId)
		{
			$identifier = end($data->sizes);
			$image = JUri::root(true) . '/images/econa/fields/' . $data->fieldId . '/' . str_replace('.', '_', $data->context) . '/' . $data->itemId . '/' . $data->filename . '_' . $identifier . '.' . $data->extension;
		}

		if (!isset($data->params->media))
		{
			$data->params->media = 1;
		}

		$JVersion = version_compare(JVERSION, '4.0', 'ge') ? '4': '3';
		$econaVersion = $this->getEconaVersion();
		$document = JFactory::getDocument();
		$document->addScriptDeclaration("var econaBaseUrl = '" . JUri::base(true) . "'; var econaSessionToken = '" . JSession::getFormToken() . "'; var econaJVersion = '" . $JVersion . "'; var econaPlaceholderImage = '" . JUri::root(true) . '/media/econa/images/placeholder.jpg' . "';");
		$document->addStyleSheet(JUri::root(true) . '/media/econa/css/app.css', array('version' => $econaVersion));
		$document->addStyleSheet(JUri::root(true) . '/media/econa/css/cropper.min.css', array('version' => $econaVersion));
		$document->addScript(JUri::root(true) . '/media/econa/js/main.min.js', array('version' => $econaVersion), array('defer' => true));

		ob_start();
		include dirname(__FILE__) . '/tmpl/image.php';
		$contents = ob_get_clean();

		return $contents;
	}

	public function getLabel()
	{
		return;
	}

	private function getEconaVersion()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('manifest_cache'))->from($db->qn('#__extensions'))->where($db->qn('element') . ' = ' . $db->q('econa'))->where($db->qn('folder') . ' = ' . $db->q('fields'));
		$db->setQuery($query);
		$manifest = json_decode($db->loadResult());

		return $manifest->version;
	}
}
