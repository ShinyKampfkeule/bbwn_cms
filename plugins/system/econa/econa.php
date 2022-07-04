<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

class plgSystemEcona extends JPlugin
{
	public function onAfterRoute()
	{
		$application = JFactory::getApplication();
		$option = $application->input->get('option');
		$view = $application->input->get('view');
		$task = $application->input->get('task');

		if (version_compare(JVERSION, '4.0', 'lt') && $application->isClient('administrator'))
		{
			require_once JPATH_SITE . '/plugins/system/econa/observer/econa.php';
			require_once JPATH_SITE . '/plugins/content/econa/tables/image.php';
			JObserverMapper::addObserverClassToClass('JTableObserverEcona', 'EconaContentTableImage');

			// K2 cancel
			if (JFile::exists(JPATH_SITE . '/components/com_k2/k2.php'))
			{
				require_once JPATH_SITE . '/plugins/system/econa/observer/k2.php';
				require_once JPATH_ADMINISTRATOR . '/components/com_k2/tables/k2item.php';
				JObserverMapper::addObserverClassToClass('JTableObserverK2', 'TableK2Item');

				if ($option == 'com_k2' && $view == 'item' && $task == 'cancel')
				{
					JPluginHelper::importPlugin('k2', 'econa');
					$application->triggerEvent('onAfterK2Close');
				}
			}
		}

		// Joomla! article cancel
		if ($option == 'com_content' && $task == 'article.cancel')
		{
			JPluginHelper::importPlugin('content', 'econa');
			JPluginHelper::importPlugin('content', 'econa_article_images');
			$application->triggerEvent('onContentAfterClose', array('com_content.article'));
		}
	}

	/*public function onAfterTranslationSave($data) {
		$context = $data['option'].'.'.$data['catid'];
		$article = (object) $data['jform'];
		$article->id = $data['reference_id'];
		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('fields', 'econa');
		$application->triggerEvent('onContentAfterSave', array($context, $article, false));
	}*/
}
