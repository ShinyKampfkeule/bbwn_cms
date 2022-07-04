<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
defined('_JEXEC') or die;

ini_set('memory_limit', '512M');

JLoader::import('components.com_fields.libraries.fieldsplugin', JPATH_ADMINISTRATOR);
JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class PlgFieldsEcona extends FieldsPlugin
{
	protected $basePath;
	protected $baseUrl;
	protected $sizes;

	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->basePath = JPATH_SITE . '/images/econa/fields';
		$this->baseUrl = JUri::root(true) . '/images/econa/fields';
	}

	public function onContentPrepareData($context, $data)
	{
		if ($context != 'com_fields.field')
		{
			return;
		}

		if (!isset($data->type) || $data->type !== 'econa')
		{
			return;
		}

		$application = JFactory::getApplication();
		$application->enqueueMessage(JText::_('PLG_FIELDS_ECONA_SETUP_MESSAGE'));

		$formData = is_array($data) ? (object) $data : $data;

		if (!isset($formData->fieldparams['display']))
		{
			$formData->fieldparams['display'] = array();
		}

		$views = array();
		$parts = explode('.', $formData->context);
		$component = current($parts);
		jimport('joomla.filesystem.folder');

		$viewsPath = version_compare(JVERSION, '4.0', 'ge') ? JPATH_SITE . '/components/' . $component . '/tmpl' : JPATH_SITE . '/components/' . $component . '/views';

		if ($viewsPath)
		{
			$views = JFolder::folders($viewsPath);
			$views[] = 'tag';
			$views[] = 'search';
			sort($views);
		}

		if (!count($views))
		{
			return;
		}

		$excluded = array('com_content' => array('form', 'categories'));

		// We need to do this every time so we are up to date with filesystem (views added or removed by third party extension)
		$rows = array();

		foreach ($views as $view)
		{
			if (isset($excluded[$component]) && in_array($view, $excluded[$component]))
			{
				continue;
			}

			$row = array();
			$row['view'] = $view;

			// Backward compatibility. Check for previous version options
			if (isset($formData->fieldparams['image']) && $formData->fieldparams['image'])
			{
				$row['image'] = $formData->fieldparams['image'];
			}

			if (isset($formData->fieldparams['image_sizes_attribute']) && $formData->fieldparams['image_sizes_attribute'])
			{
				$row['image_sizes_attribute'] = $formData->fieldparams['image_sizes_attribute'];
			}

			if (isset($formData->fieldparams['caption']) && $formData->fieldparams['caption'])
			{
				$row['caption'] = $formData->fieldparams['caption'];
			}

			if (isset($formData->fieldparams['credits']) && $formData->fieldparams['credits'])
			{
				$row['credits'] = $formData->fieldparams['credits'];
			}

			if (isset($formData->fieldparams['modal']) && $formData->fieldparams['modal'])
			{
				$row['modal'] = $formData->fieldparams['modal'];
			}

			// Now check for current settings
			if ($formData->fieldparams['display'] && is_array($formData->fieldparams['display']))
			{
				foreach ($formData->fieldparams['display'] as $entry)
				{
					if ($entry['view'] == $view)
					{
						$row = $entry;

						break;
					}
				}
			}

			$row['title'] = JText::sprintf('PLG_FIELDS_ECONA_VIEW_TITLE', ucfirst($view));

			// Add the entry to the array
			$rows[] = $row;
		}

		if (is_array($data))
		{
			$data['fieldparams']['display'] = $rows;
		}
		else
		{
			$data->fieldparams['display'] = $rows;
		}

		$document = JFactory::getDocument();
		$document->addStyleDeclaration('#attrib-econa_display_options > .control-group > .controls { margin: 0;} #attrib-econa_display_options .subform-repeatable-group {margin: 0 0 20px 0; padding:0; border: none;}');
	}

	public function onCustomFieldsPrepareField($context, $item, $field)
	{
		if (!$this->isTypeSupported($field->type))
		{
			return;
		}

		$data = json_decode($field->value);

		if (!$data || !$data->filename)
		{
			return '';
		}

		// Load responsive images polyfill
		$document = JFactory::getDocument();
		$document->addScript(JUri::root(true) . '/plugins/content/econa/js/picturefill.min.js');

		// First detect the available images based on the plugin configuration and the existing generated files
		$baseUrl = $this->getResourceUrl($field->id, $context, $item->id);
		$sizes = $this->getSizes('sizes', $field->fieldparams);
		$images = array();
		$timestamp = false;

		if (isset($item->modified) && (int) $item->modified)
		{
			$modified = JFactory::getDate($item->modified);
			$timestamp = $modified->toUnix();
		}

		foreach ($sizes as $size)
		{
			if (in_array($size->identifier, $data->sizes))
			{
				$size->src = $baseUrl . '/' . $data->filename . '_' . $size->identifier . '.' . $data->extension;

				if ($timestamp)
				{
					$size->src .= '?t=' . $timestamp;
				}
				$images[$size->identifier] = $size;
			}
		}

		// Build image object
		$image = (object) array('src' => null, 'srcset' => null, 'srcsetWebp' => null, 'sizes' => null, 'modal' => null);

		// NEW: Display options based on view
		$application = JFactory::getApplication();
		$view = $application->input->getCmd('view');
		$displayOptions = $field->fieldparams->get('display');

		if ($displayOptions)
		{
			foreach ($displayOptions as $entry)
			{
				if ($entry->view == $view)
				{
					foreach ($entry as $key => $value)
					{
						if (!is_null($value))
						{
							$field->fieldparams->set($key, $value);
						}
					}
				}
			}
		}

		// Get sizes configuration
		$contextSizes = (array) $field->fieldparams->get('image', array());

		// If no sizes are defined return now, we should hide the image
		if (count($contextSizes) === 0)
		{
			return '';
		}

		// OG Image
		$largestImage = null;

		// Ensure we have at least one valid image
		if (isset($contextSizes[0]) && isset($images[$contextSizes[0]]))
		{
			$image->src = $images[$contextSizes[0]]->src;
			$largestImage = $image->src;
		}

		// Set responsive attributes
		$largestWidth = 0;

		if (count($contextSizes) >= 1)
		{
			$srcset = array();
			$srcsetWebp = array();

			foreach ($contextSizes as $identifier)
			{
				if (isset($images[$identifier]))
				{
					$srcset[] = $images[$identifier]->src . ' ' . $images[$identifier]->width . 'w';

					if (isset($data->webp) && $data->webp)
					{
						$srcsetWebp[] = JFile::stripExt($images[$identifier]->src) . '.webp' . ' ' . $images[$identifier]->width . 'w';
					}

					if ($images[$identifier]->width > $largestWidth)
					{
						$largestWidth = $images[$identifier]->width;
						$largestImage = $images[$identifier]->src;
					}
				}
			}
			$image->srcset = implode(', ', $srcset);
			$image->srcsetWebp = implode(', ', $srcsetWebp);
			$image->sizes = $field->fieldparams->get('image_sizes_attribute', '100vw');
		}

		// Width and height
		if (isset($data->width) && isset($data->height))
		{
			$image->width = trim($data->width);
			$image->height = trim($data->height);
		}

		// Caption, credits and alt text
		$image->caption = trim($data->caption);
		$image->credits = trim($data->credits);
		$image->alt = trim($data->alt);

		if (!$image->alt)
		{
			$image->alt = $image->caption ? $image->caption : $data->filename;
		}

		// Link
		$image->link = '';
		$image->modal = '';

		if ($field->fieldparams->get('link'))
		{
			if ($context == 'com_content.article')
			{
				if ($view == 'tag')
				{
					$image->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->core_content_id . ':' . $item->core_alias, $item->core_catid, $item->core_language));
				}
				else
				{
					if (isset($item->slug))
					{
						$slug = $item->slug;
					}
					elseif (isset($item->alias) && $item->alias)
					{
						$slug = $item->alias . ':' . $item->id;
					}
					else
					{
						$slug = $item->id;
					}
					$image->link = JRoute::_(ContentHelperRoute::getArticleRoute($slug, $item->catid, $item->language));
				}
			}
		}
		// Modal
		elseif ($field->fieldparams->get('modal') && array_key_exists($field->fieldparams->get('modal'), $images))
		{
			$modal = $images[$field->fieldparams->get('modal')];
			$image->modal = $modal->src;

			JHtml::_('jquery.framework');
			$document->addStyleSheet(JUri::root(true) . '/plugins/content/econa/css/featherlight.min.css');
			$document->addScript(JUri::root(true) . '/plugins/content/econa/js/featherlight.min.js');
			$document->addScriptDeclaration('jQuery(document).ready(function(){ jQuery(\'.econaFieldModal\').featherlight(\'image\', {targetAttr: \'href\'}); });');
		}

		// Load plugin CSS
		if ($field->fieldparams->get('css', 1))
		{
			$document->addStyleSheet(JUri::root(true) . '/media/econa/css/style.css');
		}

		if ($field->fieldparams->get('og_image', 0) && $largestImage)
		{
			$parts = explode('.', $context);
			$contextView = end($parts);

			if ($view == $contextView)
			{
				$document->setMetadata('og:image', substr(JUri::root(false), 0, -1) . $largestImage, 'property');
			}
		}

		// Assing image object to field so we can access it from our template
		$field->image = $image;

		// Render
		return parent::onCustomFieldsPrepareField($context, $item, $field);
	}

	public function onUserAfterSave($user, $isNew, $success)
	{
		if ($success)
		{
			$context = 'com_users.user';
			$row = (object) $user;
			$this->onContentAfterSave($context, $row, $isNew);
		}
	}

	public function onContentAfterSave($context, $article, $isNew)
	{
		$application = JFactory::getApplication();

		if ($application->isClient('site') && $context == 'com_content.form')
		{
			$context = 'com_content.article';
		}
		$needle = 'com_categories.category';

		if (strpos($context, $needle) === 0)
		{
			$extension = substr($context, strlen($needle));

			if ($extension)
			{
				$context = $extension . '.categories';
			}
			else
			{
				$context = $article->extension . '.categories';
			}
		}
		$fields = FieldsHelper::getFields($context, $article);
		$fieldIds = array();

		foreach ($fields as $field)
		{
			if ($field->type == 'econa')
			{
				if (in_array($field->id, $fieldIds))
				{
					continue;
				}
				$fieldIds[] = $field->id;

				if ($field->rawvalue)
				{
					$value = $field->rawvalue;
				}
				else
				{
					JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_fields/models');
					$model = JModelLegacy::getInstance('Field', 'FieldsModel', array('ignore_request' => true));
					$value = $model->getFieldValue($field->id, $article->id);
				}

				if (!$value)
				{
					continue;
				}

				$fieldData = json_decode($value);

				if (isset($fieldData->itemId) && $fieldData->itemId && $fieldData->itemId != $article->id)
				{
					$sourceFolder = $this->getResourcePath($field->id, $context, $fieldData->itemId);
					$targetFolder = $this->getResourcePath($field->id, $context, $article->id);

					if (JFolder::exists($sourceFolder))
					{
						JFolder::copy($sourceFolder, $targetFolder);
					}
					$fieldData->itemId = $article->id;
				}

				$sizes = $this->getSizes('sizes', $field->fieldparams);
				$jpeg = $field->fieldparams->get('jpeg');
				$webp = $field->fieldparams->get('webp');

				$input = $application->input->get($fieldData->key, '', 'array');

				if (!$input)
				{
					return true;
				}

				$jform = new JInput($input);
				$data = new JInput($jform->get('econa', '', 'array'));

				$caption = $data->getString('caption');
				$credits = $data->getString('credits');
				$alt = $data->getString('alt');

				$delete = $data->getBool('delete');
				$upload = $data->getCmd('upload');
				$filename = $data->getString('filename');

				$path = $this->getResourcePath($field->id, $context, $article->id);

				// Handle delete ( also when the image is going to be replaced by a new one )
				if ($delete || $upload)
				{
					if (JFolder::exists($path))
					{
						JFolder::delete($path);
					}

					if ($delete)
					{
						$filename = '';
						$sizes = array();
					}
				}

				// Validate filename if file is renamed or we have an upload
				if ($upload || ($filename && $filename != $fieldData->filename))
				{
					$filename = JFilterOutput::stringURLUnicodeSlug($filename);

					if (!$filename)
					{
						$filename = uniqid('plg_fields_econa_');
					}
				}

				// Handle upload
				$sourcepath = JPATH_SITE . '/media/econa/tmp/' . $upload;

				if ($upload && JFile::exists($sourcepath))
				{
					if (!JFolder::exists($path))
					{
						JFolder::create($path);
					}
					$source = $this->getSourceProperties($sourcepath);

					foreach ($sizes as $key => $size)
					{
						$target = $this->getTargetProperties($source, $size, $filename, $jpeg, $field->id, $context, $article->id);
						$image = new JImage($source->path);
						$resized = $image->resize($target->width, 0);
						$resized->toFile($target->path, $target->type, array('quality' => $target->quality));

						if ($webp && function_exists('imagewebp'))
						{
							$webpFile = JFile::stripExt($target->path) . '.webp';
							imagewebp($resized->getHandle(), $webpFile, $target->quality);
						}

						$resized->destroy();
						$image->destroy();

						if ($key == (count($sizes) - 1))
						{
							$imageProperties = JImage::getImageFileProperties($target->path);
						}
					}

					JFile::delete($sourcepath);

					// Detect extension
					$extension = JFile::getExt($target->path);
				}

				// Handle rename
				if (!$delete && !$upload && $filename && $fieldData->filename && $filename != $fieldData->filename)
				{
					foreach ($fieldData->sizes as $identifier)
					{
						$source = $path . '/' . $fieldData->filename . '_' . $identifier . '.' . $fieldData->extension;
						$target = $path . '/' . $filename . '_' . $identifier . '.' . $fieldData->extension;

						if (JFile::exists($source))
						{
							JFile::move($source, $target);
						}
					}
				}

				// Save to database
				$fieldData->caption = $caption;
				$fieldData->credits = $credits;
				$fieldData->alt = $alt;
				$fieldData->fieldId = $field->id;
				$fieldData->context = $context;
				$fieldData->itemId = $article->id;
				$fieldData->filename = $filename;
				$fieldData->jpeg = $jpeg;
				$fieldData->webp = $webp;

				if (isset($imageProperties))
				{
					$fieldData->width = $imageProperties->width;
					$fieldData->height = $imageProperties->height;
				}

				if (isset($extension))
				{
					$fieldData->extension = $extension;
				}
				$identifiers = array();

				foreach ($sizes as $size)
				{
					$identifiers[] = $size->identifier;
				}
				$fieldData->sizes = $identifiers;

				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->update($db->qn('#__fields_values'));
				$query->set($db->qn('value') . ' = ' . $db->q(json_encode($fieldData)));
				$query->where($db->qn('field_id') . ' = ' . (int) $field->id);
				$query->where($db->qn('item_id') . ' = ' . (int) $article->id);
				$db->setQuery($query);
				$db->execute();
			}
		}

		return true;
	}

	public function onEconaContentAfterDelete($context, $item)
	{
		return $this->onContentAfterDelete($context, $item);
	}

	public function onContentAfterDelete($context, $item)
	{
		$fields = FieldsHelper::getFields($context, $item);

		foreach ($fields as $field)
		{
			if ($field->type == 'econa')
			{
				$path = $this->getResourcePath($field->id, $context, $item->id);

				if (JFolder::exists($path))
				{
					JFolder::delete($path);
				}
			}
		}

		return true;
	}

	public function onAjaxEcona()
	{
		if (!JSession::checkToken())
		{
			throw new Exception(JText::_('JINVALID_TOKEN'));
		}

		$application = JFactory::getApplication();
		$context = $application->input->getCmd('econaContext');

		if ($context && strpos($context, '.'))
		{
			$parts = explode('.', $context);
			$component = $parts[0];
		}

		$user = JFactory::getUser();
		$isAllowed = isset($component) && $component ? $user->authorise('core.edit.value', $component) : $user->authorise('core.edit.value');

		if (!$isAllowed)
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
		$files = $application->input->files->get($key);
		$file = isset($files['econa']) ? $files['econa']['file'] : null;
		$path = $application->input->getPath('path');

		// Key is required
		if (!$key)
		{
			throw new Exception(JText::_('PLG_FIELDS_ECONA_NO_UPLOAD_KEY_PROVIDED'));
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
			throw new Exception(JText::_('PLG_FIELDS_ECONA_COULD_NOT_UPLOAD_IMAGE'));
		}

		$upload = $key . '.' . $extension;
		$preview = JUri::root(true) . '/media/econa/tmp/' . $upload;

		$response = new stdClass();
		$response->preview = $preview;
		$response->upload = $upload;
		$response->filename = $name;

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
		$fieldId = $application->input->getInt('fieldId');
		$itemId = $application->input->getInt('itemId');
		$context = $application->input->getCmd('context');
		$x = $application->input->getFloat('x');
		$y = $application->input->getFloat('y');
		$width = $application->input->getFloat('width');
		$height = $application->input->getFloat('height');
		$rotate = $application->input->getCmd('rotate');
		$scaleX = $application->input->getCmd('scaleX');
		$scaleY = $application->input->getCmd('scaleY');

		if (!$fieldId && !$upload)
		{
			throw new Exception(JText::_('PLG_FIELDS_ECONA_NO_IMAGE_PROVIDED'));
		}

		$file = null;

		if ($upload)
		{
			$file = JPATH_SITE . '/media/econa/tmp/' . $upload;
		}
		else
		{
			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_fields/models');
			$model = JModelLegacy::getInstance('Field', 'FieldsModel', array('ignore_request' => true));
			$fieldValue = $model->getFieldValue($fieldId, $itemId);

			if ($fieldValue)
			{
				$value = json_decode($fieldValue);
				$source = $value->filename . '_' . end($value->sizes) . '.' . $value->extension;
				$upload = $key . '.' . $value->extension;
				$path = $this->getResourcePath($fieldId, $context, $itemId);
				JFile::copy($path . '/' . $source, JPATH_SITE . '/media/econa/tmp/' . $upload);
				$file = JPATH_SITE . '/media/econa/tmp/' . $upload;
			}
		}

		if (!$file || !JFile::exists($file))
		{
			throw new Exception(JText::_('PLG_FIELDS_ECONA_IMAGE_FILE_NOT_FOUND'));
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

	private function getTargetProperties($source, $size = null, $filename = null, $jpg = true, $fieldId = '', $context = '', $itemId = '')
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
		if ($jpg)
		{
			$type = IMAGETYPE_JPEG;
			$extension = 'jpg';
		}

		// Set width and quality based on size
		$width = $source->width > $size->width ? $size->width : $source->width;
		$quality = $size->quality;

		// Detect path
		$path = $this->getResourcePath($fieldId, $context, $itemId) . '/' . $filename . '_' . $size->identifier . '.' . $extension;
		$url = $this->getResourceUrl($fieldId, $context, $itemId) . '/' . $filename . '_' . $size->identifier . '.' . $extension;

		// Normalise quality for PNG processing
		if ($type === IMAGETYPE_PNG)
		{
			$pngQuality = ($quality - 100) / 11.111111;
			$quality = round(abs($pngQuality));
			$quality = (int) $quality;
		}

		return (object) array('type' => $type, 'extension' => $extension, 'width' => $width, 'quality' => $quality, 'path' => $path, 'url' => $url);
	}

	private function getSizes($option, $params)
	{
		$array = array();
		$value = $params->get($option);

		if (is_string($value))
		{
			$sizes = json_decode($value);

			if (is_object($sizes) && isset($sizes->identifier))
			{
				foreach ($sizes->identifier as $key => $identifier)
				{
					$entry = new stdClass();
					$entry->label = $sizes->label[$key];
					$entry->identifier = $sizes->identifier[$key];
					$entry->width = (int) $sizes->width[$key];
					$entry->quality = (int) $sizes->quality[$key];
					$array[] = $entry;
				}
			}
		}
		elseif (is_object($value))
		{
			foreach ($value as $entry)
			{
				$entry->width = (int) $entry->width;
				$entry->quality = (int) $entry->quality;
				$array[] = $entry;
			}
		}

		usort($array, array($this, 'sortSizes'));

		return $array;
	}

	private function sortSizes($a, $b)
	{
		if ($a->width == $b->width)
		{
			return 0;
		}

		return ($a->width > $b->width) ? 1 : -1;
	}

	private function getResourcePath($fieldId, $context, $itemId)
	{
		return $this->basePath . '/' . $fieldId . '/' . str_replace('.', '_', $context) . '/' . $itemId;
	}

	private function getResourceUrl($fieldId, $context, $itemId)
	{
		return $this->baseUrl . '/' . $fieldId . '/' . str_replace('.', '_', $context) . '/' . $itemId;
	}
}
