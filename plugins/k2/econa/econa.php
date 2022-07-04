<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
defined('_JEXEC') or die;

if (file_exists(JPATH_ADMINISTRATOR . '/components/com_k2/lib/k2plugin.php'))
{
	require_once JPATH_ADMINISTRATOR . '/components/com_k2/lib/k2plugin.php';
}
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
JTable::addIncludePath(JPATH_SITE . '/plugins/k2/econa/tables');

class plgK2Econa extends K2Plugin
{
	public $pluginName = 'econa';
	public $pluginNameHumanReadable = 'Econa';
	public $sizes = array('XS', 'S', 'M', 'L', 'XL', 'Generic');
	public $properies = array('imageXSmall', 'imageSmall', 'imageMedium', 'imageLarge', 'imageXLarge', 'imageGeneric');

	public function __construct(&$subject, $params)
	{
		$language = JFactory::getLanguage();
		$language->load('plg_k2_econa.sys', JPATH_SITE . '/plugins/k2/econa');
		parent::__construct($subject, $params);
		$this->pluginNameHumanReadable = JText::_('PLG_K2_ECONA_FILENAME_LABEL');
	}

	public function onContentPrepare($context, &$item, &$params, $page = 0)
	{
		if ($context == 'com_k2.relatedByTag')
		{
			$plugins = new JRegistry($item->plugins);
			$image = $plugins->get('econafilename');

			if ($image)
			{
				$filename = JFile::stripExt($image);
				$extension = JFile::getExt($image);

				foreach ($this->properies as $key => $property)
				{
					$item->$property = JUri::root(true) . '/media/k2/items/cache/' . $image . '_' . $this->sizes[$key] . '.jpg';
				}
				$size = 'image' . $item->params->get('itemRelatedImageSize', '0');

				if (isset($item->$size))
				{
					$item->image = $item->$size;
				}
			}
		}
	}

	public function onK2PrepareContent(&$item, $params, $page = 0)
	{
		if (isset($item->plugins))
		{
			$plugins = new JRegistry($item->plugins);
			$image = $plugins->get('econafilename');

			if ($image)
			{
				$filename = JFile::stripExt($image);
				$extension = JFile::getExt($image);

				foreach ($this->properies as $key => $property)
				{
					$item->$property = JUri::root(true) . '/media/k2/items/cache/' . $image . '_' . $this->sizes[$key] . '.jpg';
				}

				// K2 content module
				if ($params->get('parsedInModule'))
				{
					$size = 'image' . $params->get('itemImgSize', 'Small');

					if (isset($item->$size))
					{
						$item->image = $item->$size;
					}
				}
			}
		}
	}

	public function onBeforeK2Save(&$item, $isNew)
	{
		// Get application
		$application = JFactory::getApplication();

		// Get the plugins data
		$plugins = new JInput($application->input->get('plugins', '', 'array'));

		// Get upload value
		$upload = $plugins->getCmd('econaupload');

		// Override default upload with our file
		if ($upload)
		{
			$application->input->set('existingImage', '/media/econa/tmp/' . $upload);
		}

		// Get delete value
		$delete = $plugins->getInt('econadelete');

		// Override default delete
		if ($delete)
		{
			$application->input->set('del_image', '1');
		}

		// Proxy on before save event to content plugin since it contains all the logic
		JPluginHelper::importPlugin('content', 'econa');
		$application->triggerEvent('onK2BeforeSave', array($item, $isNew));
	}

	public function onAfterK2Save(&$item, $isNew)
	{
		// Get application
		$application = JFactory::getApplication();

		// Get input
		$plugins = new JInput($application->input->get('plugins', '', 'array'));
		$upload = $plugins->getCmd('econaupload');
		$delete = $plugins->getBool('econadelete');
		$filename = $plugins->getString('econafilename');

		// Cleanup tmp folder. K2 has already created the images
		$file = JPATH_SITE . '/media/econa/tmp/' . $upload;

		if ($upload && JFile::exists($file))
		{
			JFile::delete($file);
		}

		// Get current image information
		if (!class_exists('EconaK2TableImage'))
		{
			require_once JPATH_SITE . '/plugins/k2/econa/tables/image.php';
		}
		$image = JTable::getInstance('Image', 'EconaK2Table');
		$image->load(array('resourceId' => $item->id, 'resourceType' => 'com_k2.item'));

		// Case 1: Delete
		if ($delete)
		{
			if ($image)
			{
				// Delete Econa image files
				if ($image->filename)
				{
					foreach ($this->sizes as $size)
					{
						$path = JPATH_SITE . '/media/k2/items/cache/' . $image->filename . '_' . $size . '.jpg';

						if (JFile::exists($path))
						{
							JFile::delete($path);
						}
					}
				}

				// Delete database record
				if ($image->resourceId)
				{
					$image->delete();
				}
			}

			return true;
		}

		// Case 2: Remove custom filename
		if (!$filename && $image && $image->filename)
		{
			$targetFilename = md5('Image' . $item->id);

			foreach ($this->sizes as $size)
			{
				$source = JPATH_SITE . '/media/k2/items/cache/' . $image->filename . '_' . $size . '.jpg';
				$target = JPATH_SITE . '/media/k2/items/cache/' . $targetFilename . '_' . $size . '.jpg';

				if (JFile::exists($source))
				{
					JFile::move($source, $target);
				}
			}

			// Delete database record
			if ($image->resourceId)
			{
				$image->delete();
			}

			return true;
		}

		// Case 3: Apply custom filename
		if ($filename)
		{

						// Validate filename
			$filename = $this->validateFilename($filename, $item->id);

			// Set source and target filenames
			$targetFilename = $filename;
			$sourceFilename = null;
			$hash = md5('Image' . $item->id);

			if ($upload)
			{
				$sourceFilename = $hash;
			}
			elseif ($image && $image->filename && $image->filename != $filename)
			{
				$sourceFilename = $image->filename;
			}
			elseif ((!$image || !$image->filename) && JFile::exists(JPATH_SITE . '/media/k2/items/cache/' . $hash . '_M.jpg'))
			{
				$sourceFilename = $hash;
			}

			// Rename files
			if ($sourceFilename)
			{
				foreach ($this->sizes as $size)
				{
					$source = JPATH_SITE . '/media/k2/items/cache/' . $sourceFilename . '_' . $size . '.jpg';
					$target = JPATH_SITE . '/media/k2/items/cache/' . $targetFilename . '_' . $size . '.jpg';

					if (JFile::exists($source))
					{
						if ($this->params->get('keep_native_images', 1))
						{
							JFile::copy($source, $target);
						}
						else
						{
							JFile::move($source, $target);
						}
					}
				}
			}

			// If filename has changed remove the old files
			if ($image && $filename != $image->filename)
			{
				foreach ($this->sizes as $size)
				{
					$path = JPATH_SITE . '/media/k2/items/cache/' . $image->filename . '_' . $size . '.jpg';

					if (JFile::exists($path))
					{
						JFile::delete($path);
					}
				}
			}

			// Keep extra large image so we don't break the image preview column in administration lists
			if (!$this->params->get('keep_native_images', 1))
			{
				JFile::copy(JPATH_SITE . '/media/k2/items/cache/' . $filename . '_XL.jpg', JPATH_SITE . '/media/k2/items/cache/' . md5('Image' . $item->id) . '_XL.jpg');
			}

			// Save to database
			$input = array();
			$input['resourceId'] = $item->id;
			$input['filename'] = $filename;
			$input['extension'] = 'jpg';
			$image->save($input);

			return true;
		}

		return true;
	}

	public function onAfterK2Delete($itemId)
	{
		// Get current image information
		$image = JTable::getInstance('Image', 'EconaK2Table');
		$image->load(array('resourceId' => $itemId, 'resourceType' => 'com_k2.item'));

		// Delete files
		if ($image->filename)
		{
			foreach ($this->sizes as $size)
			{
				$path = JPATH_SITE . '/media/k2/items/cache/' . $image->filename . '_' . $size . '.jpg';

				if (JFile::exists($path))
				{
					JFile::delete($path);
				}
			}
		}

		// Delete database record
		if ($image->resourceId)
		{
			$image->delete();
		}
	}

	public function onAfterK2Close()
	{
		$application = JFactory::getApplication();
		$plugins = new JInput($application->input->get('plugins', '', 'array'));
		$upload = $plugins->getCmd('econaupload');
		$file = JPATH_SITE . '/media/econa/tmp/' . $upload;

		if ($upload && JFile::exists($file))
		{
			JFile::delete($file);
		}
	}

	public function onAjaxEcona()
	{
		if (!JSession::checkToken())
		{
			throw new Exception(JText::_('JINVALID_TOKEN'));
		}
		$application = JFactory::getApplication();

		if ($application->isClient('administrator'))
		{
			$user = JFactory::getUser();

			if (!$user->authorise('core.create', 'com_k2') && !$user->authorise('core.edit', 'com_k2') && !$user->authorise('core.edit.own', 'com_k2'))
			{
				throw new Exception(JText::_('JGLOBAL_AUTH_ACCESS_DENIED'));
			}
		}
		else
		{
			if (JFile::exists(JPATH_SITE . '/components/com_k2/helpers/permissions.php'))
			{
				require_once JPATH_SITE . '/components/com_k2/helpers/permissions.php';
				K2HelperPermissions::setPermissions();

				if (!K2HelperPermissions::canAddItem())
				{
					throw new Exception(JText::_('JGLOBAL_AUTH_ACCESS_DENIED'));
				}
			}
			else
			{
				throw new Exception(JText::_('JGLOBAL_AUTH_ACCESS_DENIED'));
			}
		}

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
		// Get application
		$application = JFactory::getApplication();

		// Get input
		$key = $application->input->getCmd('econaKey');
		$upload = $application->input->getCmd('econaUpload');
		$file = $application->input->files->get('image');
		$path = $application->input->getPath('path');
		$path = substr($path, strlen(JUri::root(true)));

		// Key is required
		if (!$key)
		{
			throw new Exception(JText::_('PLG_K2_ECONA_NO_UPLOAD_KEY_PROVIDED'));
		}

		// Ensure that file is an image
		jimport('joomla.image.image');
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
			throw new Exception(JText::_('PLG_K2_ECONA_COULD_NOT_UPLOAD_IMAGE'));
		}

		$upload = $key . '.' . $extension;
		$preview = JUri::root(true) . '/media/econa/tmp/' . $upload;

		$response = new stdClass();
		$response->preview = $preview;
		$response->upload = $upload;
		$response->filename = $name;

		return $response;
	}

	private function process()
	{
		$application = JFactory::getApplication();
		$key = $application->input->getCmd('econaKey');
		$upload = $application->input->getCmd('econaUpload');
		$id = $application->input->getInt('id');
		$x = $application->input->getFloat('x');
		$y = $application->input->getFloat('y');
		$width = $application->input->getFloat('width');
		$height = $application->input->getFloat('height');
		$rotate = $application->input->getCmd('rotate');
		$scaleX = $application->input->getCmd('scaleX');
		$scaleY = $application->input->getCmd('scaleY');

		if (!$id && !$upload)
		{
			throw new Exception(JText::_('PLG_K2_ECONA_NO_IMAGE_PROVIDED'));
		}

		$file = null;

		if ($upload)
		{
			$file = JPATH_SITE . '/media/econa/tmp/' . $upload;
		}
		else
		{
			$table = JTable::getInstance('Image', 'EconaK2Table');
			$table->load(array('resourceId' => $id, 'resourceType' => 'com_k2.item'));

			if ($table->filename)
			{
				$source = $table->filename . '_XL.jpg';
			}
			else
			{
				$source = md5('Image' . $id) . '_XL.jpg';
			}
			$upload = $key . '.jpg';
			JFile::copy(JPATH_SITE . '/media/k2/items/cache/' . $source, JPATH_SITE . '/media/econa/tmp/' . $upload);
			$file = JPATH_SITE . '/media/econa/tmp/' . $upload;
		}

		if (!$file || !JFile::exists($file))
		{
			throw new Exception(JText::_('PLG_K2_ECONA_IMAGE_FILE_NOT_FOUND'));
		}

		$basename = basename($file);
		$extension = JFile::getExt($basename);
		$name = JFile::stripExt($basename);

		ini_set('memory_limit', '512M');
		jimport('joomla.image.image');
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
		$image->toFile(JPATH_SITE . '/media/econa/tmp/' . $upload);
		$image->destroy();

		$upload = $name . '.' . $extension;
		$preview = JUri::root(true) . '/media/econa/tmp/' . $upload;

		$response = new stdClass();
		$response->preview = $preview;
		$response->upload = $upload;

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

	private function validateFilename($filename, $itemId)
	{
		$sourceFilename = trim($filename);
		$filename = JFilterOutput::stringURLUnicodeSlug($filename);

		if (!$filename)
		{
			$filename = uniqid('plg_k2_econa_');
		}
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('resourceId'));
		$query->from($db->quoteName('#__econa'));
		$query->where($db->quoteName('resourceType') . ' = ' . $db->quote('com_k2.item'));
		$query->where($db->quoteName('filename') . ' = ' . $db->quote($filename));
		$query->where($db->quoteName('resourceId') . ' != ' . $db->quote($itemId));
		$db->setQuery($query);
		$result = $db->loadResult();

		if ($result)
		{
			$filename .= '_' . uniqid();
		}
		$filename = trim($filename);

		if ($sourceFilename != $filename)
		{
			$query = $db->getQuery(true);
			$query->select($db->quoteName('plugins'));
			$query->from($db->quoteName('#__k2_items'));
			$query->where($db->quoteName('id') . ' = ' . (int) $itemId);
			$db->setQuery($query);
			$plugins = $db->loadResult();
			$plugins = json_decode($plugins);

			if (is_object($plugins) && isset($plugins->econafilename))
			{
				$plugins->econafilename = $filename;
				$query = $db->getQuery(true);
				$query->update($db->quoteName('#__k2_items'));
				$query->set($db->quoteName('plugins') . ' = ' . $db->quote(json_encode($plugins)));
				$query->where($db->quoteName('id') . ' = ' . (int) $itemId);
				$db->setQuery($query);
				$db->execute();
			}
		}

		return $filename;
	}
}
