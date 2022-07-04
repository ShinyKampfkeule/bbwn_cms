<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
defined('_JEXEC') or die;

use Joomla\Registry\Registry;

jimport('joomla.form.formfield');

class JFormFieldEconaArticleImage extends JFormField
{
	public $type = 'EconaArticleImage';

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

		$plugin = JPluginHelper::getPlugin('content', 'econa_article_images');
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
		$filename = '';
		$width = 0;
		$height = 0;

		if ($this->value)
		{
			$timestamp = JFactory::getDate($this->form->getValue('modified'))->toUnix();
			$image = JUri::root(true) . '/' . $this->value . '?t=' . $timestamp;
			$filename = basename($this->value);

			if (JFile::exists(JPATH_SITE . '/' . $this->value))
			{
				$img = new JImage(JPATH_SITE . '/' . $this->value);
				$width = $img->getWidth();
				$height = $img->getHeight();
			}
		}

		$tmp = uniqid('plg_content_econa_article_images_');
		$form = JForm::getInstance('econa_article_images', JPATH_SITE . '/plugins/content/econa_article_images/forms/econa.xml');

		// JCE media manager support
		$data = array();
		$application->triggerEvent('onPlgSystemJceContentPrepareForm', array($form, $data));

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
