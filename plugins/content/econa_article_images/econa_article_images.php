<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
defined('_JEXEC') or die;

use Joomla\Registry\Registry;

ini_set('memory_limit', '512M');
jimport('joomla.image.image');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class PlgContentEcona_Article_Images extends JPlugin
{
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage('plg_content_econa_article_images', JPATH_PLUGINS . '/content/econa_article_images/language');
		$this->loadLanguage('plg_content_econa_article_images.sys', JPATH_PLUGINS . '/content/econa_article_images/language');

		// BC
		$plugin = JPluginHelper::getPlugin('content', 'econa');

		if (!$plugin)
		{
			$plugin = (object) array('params' => '');
		}
		$legacyParams = new Registry($plugin->params);
		$this->params->def('resize', $legacyParams->get('rezize_article_images', 1));
		$this->params->def('jpeg', $legacyParams->get('article_images_jpeg', 1));
		$this->params->def('intro_image_width', $legacyParams->get('intro_image_width', 400));
		$this->params->def('intro_image_quality', $legacyParams->get('intro_image_quality', 75));
		$this->params->def('full_image_width', $legacyParams->get('full_image_width', 800));
		$this->params->def('full_image_quality', $legacyParams->get('full_image_quality', 90));
	}

	public function onContentPrepareForm($form, $data)
	{
		JForm::addFieldPath(__DIR__ . '/fields');
		$form->setFieldAttribute('image_intro', 'type', 'EconaArticleImage', 'images');
		$form->setFieldAttribute('image_fulltext', 'type', 'EconaArticleImage', 'images');
	}

	public function onContentAfterSave($context, $item, $isNew)
	{
		if (($context == 'com_content.article' || $context == 'com_content.form') && isset($item->images))
		{
			$modified = false;

			$images = $item->images;

			if (is_string($images))
			{
				$images = json_decode($item->images);
			}

			$application = JFactory::getApplication();
			$input = $application->input->get('econa', '', 'array');

			$mediaParams = JComponentHelper::getParams('com_media');
			$mediaPath = $mediaParams->get('image_path', 'images');
			$econaPath = 'econa-article-images/' . $item->id;

			$sourcePath = JPATH_SITE . '/media/econa/tmp';
			$targetPath = JPATH_SITE . '/' . $mediaPath . '/' . $econaPath;

			if (isset($input['image_intro']) && $input['image_intro']['upload'])
			{
				if (!JFolder::exists($targetPath . '/intro'))
				{
					JFolder::create($targetPath . '/intro');
				}

				$source = $sourcePath . '/' . $input['image_intro']['upload'];
				$target = $targetPath . '/intro/' . $input['image_intro']['filename'];

				if (JFile::move($source, $target))
				{
					$modified = true;
					$images->image_intro = substr($target, strlen(JPATH_SITE . '/'));

					if ($this->params->get('resize'))
					{
						$sourceProperties = $this->getSourceProperties($target);
						$targetProperties = $this->getTargetProperties($sourceProperties, 'intro');

						if ($sourceProperties->width > $targetProperties->width)
						{
							$processor = new JImage($target);
							$processor->resize($targetProperties->width, 0, false);
							$processor->toFile($targetProperties->path, $targetProperties->type, array('quality' => $targetProperties->quality));
							$processor->destroy();
							$images->image_intro = JFile::stripExt($images->image_intro) . '.' . $targetProperties->extension;
						}
					}
				}
			}

			if (isset($input['image_fulltext']) && $input['image_fulltext']['upload'])
			{
				if (!JFolder::exists($targetPath . '/full'))
				{
					JFolder::create($targetPath . '/full');
				}

				$source = $sourcePath . '/' . $input['image_fulltext']['upload'];
				$target = $targetPath . '/full/' . $input['image_fulltext']['filename'];

				if (JFile::move($source, $target))
				{
					$modified = true;
					$images->image_fulltext = substr($target, strlen(JPATH_SITE . '/'));

					if ($this->params->get('resize'))
					{
						$sourceProperties = $this->getSourceProperties($target);
						$targetProperties = $this->getTargetProperties($sourceProperties, 'full');

						if ($sourceProperties->width > $targetProperties->width)
						{
							$processor = new JImage($target);
							$processor->resize($targetProperties->width, 0, false);
							$processor->toFile($targetProperties->path, $targetProperties->type, array('quality' => $targetProperties->quality));
							$processor->destroy();
							$images->image_fulltext = JFile::stripExt($images->image_fulltext) . '.' . $targetProperties->extension;
						}
					}
				}
			}

			if ($modified)
			{
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->update($db->qn('#__content'));
				$query->set($db->qn('images') . ' = ' . $db->q(json_encode($images)));
				$query->where($db->qn('id') . ' = ' . $db->q($item->id));
				$db->setQuery($query);
				$db->execute();
			}
		}

		return true;
	}

	public function onContentAfterClose($context)
	{
		if ($context == 'com_content.article' || $context == 'com_content.form')
		{
			$application = JFactory::getApplication();
			$input = $application->input->get('econa', '', 'array');

			if (isset($input['image_intro']) && $input['image_intro']['upload'] && JFile::exists(JPATH_SITE . '/media/econa/tmp/' . $input['image_intro']['upload']))
			{
				JFile::delete(JPATH_SITE . '/media/econa/tmp/' . $input['image_intro']['upload']);
			}

			if (isset($input['image_fulltext']) && $input['image_fulltext']['upload'] && JFile::exists(JPATH_SITE . '/media/econa/tmp/' . $input['image_fulltext']['upload']))
			{
				JFile::delete(JPATH_SITE . '/media/econa/tmp/' . $input['image_fulltext']['upload']);
			}
		}

		return true;
	}

	public function onContentAfterDelete($context, $item)
	{
		if ($context == 'com_content.article' || $context == 'com_content.form' && $item->id)
		{
			$mediaParams = JComponentHelper::getParams('com_media');
			$mediaPath = $mediaParams->get('image_path', 'images');
			$econaPath = 'econa-article-images/' . $item->id;
			$path = JPATH_SITE . '/' . $mediaPath . '/' . $econaPath;

			if (JFolder::exists($path))
			{
				JFolder::delete($path);
			}
		}

		$application = JFactory::getApplication();
		JPluginHelper::importPlugin('fields', 'econa');
		$application->triggerEvent('onEconaContentAfterDelete', array($context, $item));

		return true;
	}

	public function onAjaxEcona_article_images()
	{
		if (!JSession::checkToken())
		{
			throw new Exception(JText::_('JINVALID_TOKEN'));
		}

		$user = JFactory::getUser();

		if (!$user->authorise('core.create', 'com_content') && !$user->authorise('core.edit', 'com_content') && !$user->authorise('core.edit.own', 'com_content'))
		{
			throw new Exception(JText::_('JGLOBAL_AUTH_ACCESS_DENIED'));
		}

		$application = JFactory::getApplication();
		$task = $application->input->getCmd('task');

		if ($task == 'process')
		{
			return $this->process();
		}
		elseif ($task == 'delete')
		{
			return $this->clean();
		}
		else
		{
			return $this->upload();
		}
	}

	private function upload()
	{
		$application = JFactory::getApplication();
		$key = $application->input->getCmd('econaKey');
		$upload = $application->input->getCmd('econaUpload');
		$file = $application->input->files->get('file_intro');

		if (!$file)
		{
			$file = $application->input->files->get('file_full');
		}
		$path = $application->input->getPath('path');

		// Key is required
		if (!$key)
		{
			throw new Exception(JText::_('PLG_CONTENT_ECONA_ARTICLE_IMAGES_NO_UPLOAD_KEY_PROVIDED'));
		}

		// Ensure that file is an image
		$image = new JImage();

		if ($path)
		{
			$image->loadFile(JPATH_SITE . '/' . $path);
		}
		else
		{
			$image->loadFile($file['tmp_name']);
		}

		// Save path
		$savepath = JPATH_SITE . '/media/econa/tmp';

		if (!JFolder::exists($savepath))
		{
			JFolder::create($savepath);
		}

		// Delete any previous uploaded file in tmp directory
		if ($upload && JFile::exists($savepath . '/' . $upload))
		{
			JFile::delete($savepath . '/' . $upload);
		}

		// Upload depending on source
		if ($path)
		{
			$filename = basename($path);
			$extension = JFile::getExt($filename);
			$name = JFile::stripExt($filename);
			$result = JFile::copy(JPATH_SITE . '/' . $path, $savepath . '/' . $key . '.' . $extension);
		}
		else
		{
			$filename = basename($file['name']);
			$extension = JFile::getExt($filename);
			$name = JFile::stripExt($filename);
			$result = JFile::upload($file['tmp_name'], $savepath . '/' . $key . '.' . $extension);
		}

		// Check for upload/copy failure
		if (!$result)
		{
			throw new Exception(JText::_('PLG_CONTENT_ECONA_ARTICLE_IMAGES_COULD_NOT_UPLOAD_IMAGE'));
		}

		$upload = $key . '.' . $extension;
		$preview = JUri::root(true) . '/media/econa/tmp/' . $upload;

		$response = new stdClass();
		$response->preview = $preview;
		$response->upload = $upload;
		$response->filename = $name . '.' . $extension;

		$image = new JImage(JPATH_SITE . '/media/econa/tmp/' . $upload);
		$response->width = $image->getWidth();
		$response->height = $image->getHeight();

		return $response;
	}

	private function process()
	{
		$application = JFactory::getApplication();
		$key = $application->input->getCmd('econaKey');
		$upload = $application->input->getCmd('econaUpload');
		$current = $application->input->getString('econaCurrent');
		$x = $application->input->getFloat('x');
		$y = $application->input->getFloat('y');
		$width = $application->input->getFloat('width');
		$height = $application->input->getFloat('height');
		$rotate = $application->input->getCmd('rotate');
		$scaleX = $application->input->getCmd('scaleX');
		$scaleY = $application->input->getCmd('scaleY');

		if (!$current && !$upload)
		{
			throw new Exception(JText::_('PLG_CONTENT_ECONA_ARTICLE_IMAGES_NO_IMAGE_PROVIDED'));
		}

		$file = null;

		if ($upload)
		{
			$file = JPATH_SITE . '/media/econa/tmp/' . $upload;
		}
		elseif ($current)
		{
			$source = JPATH_SITE . '/' . $current;
			$upload = $key . '.' . JFile::getExt($source);
			JFile::copy($source, JPATH_SITE . '/media/econa/tmp/' . $upload);
			$file = JPATH_SITE . '/media/econa/tmp/' . $upload;
		}

		if (!$file || !JFile::exists($file))
		{
			throw new Exception(JText::_('PLG_CONTENT_ECONA_ARTICLE_IMAGES_IMAGE_FILE_NOT_FOUND'));
		}
		$source = $this->getSourceProperties($file);
		$basename = basename($file);
		$extension = JFile::getExt($basename);
		$name = JFile::stripExt($basename);

		$image = new JImage($file);

		if ($rotate)
		{
			if (strpos($rotate, '-') === 0)
			{
				$rotate = abs($rotate);
			}
			else
			{
				$rotate = 360 - abs($rotate);
			}
			$image = $image->rotate($rotate);
		}

		if ($scaleX == '-1' && $scaleY == '-1')
		{
			$image = $image->flip(IMG_FLIP_BOTH);
		}
		elseif ($scaleX == '-1')
		{
			$image = $image->flip(IMG_FLIP_HORIZONTAL);
		}
		elseif ($scaleY == '-1')
		{
			$image = $image->flip(IMG_FLIP_VERTICAL);
		}
		$image = $image->crop($width, $height, $x, $y);
		$width = $image->getWidth();
		$height = $image->getHeight();
		$image->toFile(JPATH_SITE . '/media/econa/tmp/' . $upload, $source->type);
		$image->destroy();

		$upload = $name . '.' . $extension;
		$preview = JUri::root(true) . '/media/econa/tmp/' . $upload;

		$response = new stdClass();
		$response->preview = $preview;
		$response->upload = $upload;
		$response->width = $width;
		$response->height = $height;

		return $response;
	}

	private function clean()
	{
		$application = JFactory::getApplication();
		$upload = $application->input->getCmd('econaUpload');
		$file = JPATH_SITE . '/media/econa/tmp/' . $upload;

		if ($upload && JFile::exists($file))
		{
			JFile::delete($file);
		}
	}

	private function getSourceProperties($path)
	{
		$source = null;

		try
		{
			$source = JImage::getImageFileProperties($path);
			$source->path = $path;
			$source->extension = JFile::getExt($path);
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $source;
	}

	private function getTargetProperties($source, $imageType)
	{
		// Detect target mime depending on source
		switch ($source->mime) {
					case 'image/gif':
						$type = IMAGETYPE_GIF;
						$extension = 'gif';

						break;
					case 'image/png':
						$type = IMAGETYPE_PNG;
						$extension = 'png';

						break;
					default:
						$type = IMAGETYPE_JPEG;
						$extension = 'jpg';

						break;
				}

		// Check for convert to JPEG option
		if ($this->params->get('jpeg', true))
		{
			$type = IMAGETYPE_JPEG;
			$extension = 'jpg';
		}

		// Detect path
		$path = $extension != $source->extension ? JFile::stripExt($source->path) . '.' . $extension : $source->path;

		// Detect width and quality based on context
		if ($imageType == 'intro')
		{
			$width = (int) $this->params->get('intro_image_width', 400);
			$quality = (int) $this->params->get('intro_image_quality', 100);
		}
		elseif ($imageType == 'full')
		{
			$width = (int) $this->params->get('full_image_width', 800);
			$quality = (int) $this->params->get('full_image_quality', 100);
		}

		// Normalise quality for PNG processing
		if ($type === IMAGETYPE_PNG)
		{
			$pngQuality = ($quality - 100) / 11.111111;
			$quality = round(abs($pngQuality));
			$quality = (int) $quality;
		}

		return (object) array('type' => $type, 'extension' => $extension, 'width' => $width, 'quality' => $quality, 'path' => $path);
	}
}
