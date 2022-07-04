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
JTable::addIncludePath(JPATH_SITE . '/plugins/content/econa/tables');

if(JFile::exists(JPATH_SITE . '/components/com_content/helpers/route.php'))
{
	require_once JPATH_SITE . '/components/com_content/helpers/route.php';
}

class PlgContentEcona extends JPlugin
{
	protected $savepath;
	protected $baseUrl;
	protected $sizes;

	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage('plg_content_econa', JPATH_PLUGINS . '/content/econa/language');
		$this->loadLanguage('plg_content_econa.sys', JPATH_PLUGINS . '/content/econa/language');

		$this->savepath = JPATH_SITE . '/images/econa/content/article';
		$this->baseUrl = JUri::root(true) . '/images/econa/content/article';
		$this->sizes = $this->getSizes('sizes');

	}

	public function onContentPrepareForm($form, $data)
	{
		if (!($form instanceof JForm))
		{
			$this->_subject->setError('JERROR_NOT_A_FORM');

			return false;
		}
		$name = $form->getName();

		// Global switch from plugin options
		if ($this->params->get('enable_image_tab', 0))
		{

			// Category form
			if ($name == 'com_categories.categorycom_content')
			{
				JForm::addFormPath(__DIR__ . '/forms');
				$form->loadFile('category', false);
			}
			// Article form
			elseif ($name == 'com_content.article')
			{
				$categoryIds = null;

				if (is_object($data) && isset($data->catid))
				{
					$categoryIds = $data->catid;
				}
				elseif (is_array($data) && isset($data['catid']))
				{
					$categoryIds = $data['catid'];
				}

				if ($categoryIds)
				{
					if (!is_array($categoryIds))
					{
						$categoryIds = array($categoryIds);
					}

					foreach ($categoryIds as $categoryId)
					{
						$category = JTable::getInstance('Category');
						$category->load($categoryId);
						$categoryParams = new Registry();
						$categoryParams->loadString($category->params);

						if ($categoryParams->get('enable_image_tab', 0))
						{
							JForm::addFormPath(__DIR__ . '/forms');
							$form->loadFile('econa', false);

							break;
						}
					}
				}
				else
				{
					JForm::addFormPath(__DIR__ . '/forms');
					$form->loadFile('econa', false);
				}
			}
		}
	}

	public function onContentPrepareData($context, $data)
	{
		if ($context == 'com_content.article')
		{
			$image = JTable::getInstance('Image', 'EconaContentTable');
			$image->load(array('resourceId' => is_object($data) ? $data->id : $data['id'], 'resourceType' => 'com_content.article'));
			$econa = array();
			$fields = $image->getFields();

			foreach ($fields as $key => $field)
			{
				$econa[$key] = $image->$key;
			}

			if (is_object($data))
			{
				$data->econa = $econa;
			}
			else
			{
				$data['econa'] = $econa;
			}
		}
	}


	public function onContentAfterSave($context, $article, $isNew)
	{
		// Econa image tab
		if ($this->params->get('enable_image_tab', 0) && ($context == 'com_content.article' || $context == 'com_content.form'))
		{

						// Get application
			$application = JFactory::getApplication();

			// Get input
			$jform = new JInput($application->input->get('jform', '', 'array'));
			$data = new JInput($jform->get('econa', '', 'array'));
			$delete = $data->getBool('delete');
			$upload = $data->getCmd('upload');
			$filename = $data->getString('filename');
			$caption = $data->getString('caption');
			$credits = $data->getString('credits');
			$alt = $data->getString('alt');

			// Get table
			$image = JTable::getInstance('Image', 'EconaContentTable');

			// Load article image
			$image->load(array('resourceId' => $article->id, 'resourceType' => 'com_content.article'));

			// Check for category sizes overrides
			if ($article->catid)
			{
				$category = JTable::getInstance('Category');
				$category->load($article->catid);
				$categoryParams = new Registry();
				$categoryParams->loadString($category->params);

				if ($categoryParams->get('enable_image_tab', 0) == 2)
				{
					$this->sizes = $this->getSizes('sizes', $categoryParams);
				}
			}

			// Handle delete ( also when the image is going to be replaced by a new one )
			if ($delete || $upload)
			{
				if ($image->filename)
				{
					// Delete files
					$this->deleteFromFilesystem($image);

					// Delete database record
					$image->delete();
				}
			}

			// Validate filename
			if (!$delete)
			{
				$filename = $this->validateFilename($filename, $article->id);
			}

			// Handle upload
			if ($upload)
			{
				// Generate images
				$this->saveToFilesystem($upload, $filename);

				// Detect extension
				$extension = $this->params->get('jpeg', true) ? 'jpg' : JFile::getExt($upload);
			}

			// Handle rename
			if (!$delete && !$upload && $filename && $image->filename && $filename != $image->filename)
			{
				foreach ($image->sizes as $identifier)
				{
					$source = $this->savepath . '/' . $image->filename . '_' . $identifier . '.' . $image->extension;
					$target = $this->savepath . '/' . $filename . '_' . $identifier . '.' . $image->extension;

					if (JFile::exists($source))
					{
						JFile::move($source, $target);
					}
				}
			}

			// Save to database
			if (!$delete)
			{
				$input = array();
				$input['resourceId'] = $article->id;
				$input['filename'] = $filename;

				if (isset($extension))
				{
					$input['extension'] = $extension;
				}
				$input['caption'] = $caption;
				$input['credits'] = $credits;
				$input['alt'] = $alt;
				$identifiers = array();

				foreach ($this->sizes as $size)
				{
					$identifiers[] = $size->identifier;
				}
				$input['sizes'] = json_encode($identifiers);

				// If no image is set, empty some values
				if (!$upload && !$image->filename)
				{
					$input['filename'] = '';
					$input['sizes'] = '';
				}

				// Save only if required
				if ($input['filename'] || $input['caption'] || $input['credits'] || $input['alt'])
				{
					$image->save($input);
				}
			}

			return true;
		}
	}

	public function onContentAfterDelete($context, $data)
	{
		if ($context == 'com_content.article')
		{
			// Get table
			$image = JTable::getInstance('Image', 'EconaContentTable');

			// Load article image
			$image->load(array('resourceId' => $data->id, 'resourceType' => 'com_content.article'));

			// Delete files
			if ($image->filename)
			{
				$this->deleteFromFilesystem($image);
			}

			// Delete database record
			if ($image->resourceId)
			{
				$image->delete();
			}
		}

		return true;
	}

	public function onContentAfterClose($context)
	{
		if ($context == 'com_content.article')
		{
			// Get input
			$application = JFactory::getApplication();
			$jform = new JInput($application->input->get('jform', '', 'array'));
			$data = new JInput($jform->get('econa', '', 'array'));
			$upload = $data->getCmd('upload');

			if ($upload)
			{
				$application = JFactory::getApplication();
				$application->input->set('econaUpload', $upload);
				$this->clean();
			}
		}

		return true;
	}

	public function onContentBeforeDisplay($context, &$article, &$params, $page = 0)
	{
		// Get document
		$document = JFactory::getDocument();

		// Initialize response variable
		$response = '';

		// Valid plugin contexts
		$contexts = array('com_content.article', 'com_content.category', 'com_content.archive', 'com_content.featured');

		// Load responsive images polyfill if required
		if (in_array($context, $contexts) && $this->params->get('enable_image_tab', 0))
		{
			$document->addScript(JUri::root(true) . '/plugins/content/econa/js/picturefill.min.js');
		}

		// Econa tab image
		if (in_array($context, $contexts) && $this->params->get('enable_image_tab', 0))
		{

			// Load
			$table = JTable::getInstance('Image', 'EconaContentTable');
			$table->load(array('resourceId' => $article->id, 'resourceType' => 'com_content.article'));

			// Proceed only if filename is provided
			if ($table->filename)
			{
				// Check for category overrides
				if ($article->catid)
				{
					$categories = JCategories::getInstance('content');
					$category = $categories->get($article->catid);
					$categoryParams = new Registry();
					$categoryParams->loadString($category->params);

					if (!$categoryParams->get('enable_image_tab', 0))
					{
						return '';
					}

					if ($categoryParams->get('enable_image_tab', 0) == 2)
					{
						$this->sizes = $this->getSizes('sizes', $categoryParams);
						$this->params->merge($categoryParams);
					}
				}

				// First detect the available images based on the plugin configuration and the existing generated files
				$images = array();

				foreach ($this->sizes as $size)
				{
					if (in_array($size->identifier, $table->sizes))
					{
						$size->src = $this->baseUrl . '/' . $table->filename . '_' . $size->identifier . '.' . $table->extension;
						$images[$size->identifier] = $size;
					}
				}

				// Build image object
				$image = (object) array('src' => null, 'srcset' => null, 'sizes' => null, 'modal' => null);

				// Get sizes configuration depending on context
				$contextSizes = $context == 'com_content.article' ? $this->params->get('article_image') : $this->params->get('list_image');
				$contextSizes = (array) $contextSizes;

				// If no sizes are defined return now, we should hide the image
				if (count($contextSizes) === 0)
				{
					return '';
				}

				// Ensure we have at least one valid image
				if (isset($contextSizes[0]) && isset($images[$contextSizes[0]]))
				{
					$image->src = $images[$contextSizes[0]]->src;
				}

				// Set responsive attributes
				if (count($contextSizes) > 1)
				{
					$srcset = array();

					foreach ($contextSizes as $identifier)
					{
						if (isset($images[$identifier]))
						{
							$srcset[] = $images[$identifier]->src . ' ' . $images[$identifier]->width . 'w';
						}
					}
					$image->srcset = implode(', ', $srcset);
					$image->sizes = $context == 'com_content.article' ? $this->params->get('article_image_sizes_attribute', '100vw') : $this->params->get('list_image_sizes_attribute', '100vw');
				}

				// Set modal image
				if ($context == 'com_content.article' && $this->params->get('article_modal') && array_key_exists($this->params->get('article_modal'), $images))
				{
					$image->modal = $images[$this->params->get('article_modal')]->src;
				}

				// Caption, credits and alt text
				$caption = trim($table->caption);
				$credits = trim($table->credits);
				$alt = trim($table->alt);

				if (!$alt)
				{
					$alt = $caption ? $caption : $table->filename;
				}

				// Load modal
				if ($context == 'com_content.article' && $this->params->get('article_modal', 1))
				{
					JHtml::_('jquery.framework');
					$document->addStyleSheet(JUri::root(true) . '/plugins/content/econa/css/featherlight.min.css');
					$document->addScript(JUri::root(true) . '/plugins/content/econa/js/featherlight.min.js');
					$document->addScriptDeclaration('jQuery(document).ready(function(){ jQuery(\'.econaModal\').featherlight(\'image\', {targetAttr: \'href\'}); });');
				}

				// Load plugin CSS
				if ($this->params->get('css', 1))
				{
					$document->addStyleSheet(JUri::root(true) . '/plugins/content/econa/css/style.css');
				}

				// Detect layout and overrides
				$layout = $context == 'com_content.article' ? 'article' : 'list';
				$path = JPluginHelper::getLayoutPath('content', 'econa', $layout);

				// Render
				ob_start();
				include $path;
				$response = ob_get_clean();
			}
		}

		return $response;
	}

	public function onAjaxEcona()
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
		$files = $application->input->files->get('jform');
		$file = $files['econa']['file'];
		$path = $application->input->getPath('path');

		// Key is required
		if (!$key)
		{
			throw new Exception(JText::_('PLG_CONTENT_ECONA_NO_UPLOAD_KEY_PROVIDED'));
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
			throw new Exception(JText::_('PLG_CONTENT_ECONA_COULD_NOT_UPLOAD_IMAGE'));
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
			throw new Exception(JText::_('PLG_CONTENT_ECONA_NO_IMAGE_PROVIDED'));
		}

		$file = null;

		if ($upload)
		{
			$file = JPATH_SITE . '/media/econa/tmp/' . $upload;
		}
		else
		{
			$table = JTable::getInstance('Image', 'EconaContentTable');
			$table->load(array('resourceId' => $id, 'resourceType' => 'com_content.article'));

			if ($table->filename)
			{
				$source = $table->filename . '_L.' . $table->extension;
				$upload = $key . '.' . $table->extension;
				JFile::copy($this->savepath . '/' . $source, JPATH_SITE . '/media/econa/tmp/' . $upload);
				$file = JPATH_SITE . '/media/econa/tmp/' . $upload;
			}
		}

		if (!$file || !JFile::exists($file))
		{
			throw new Exception(JText::_('PLG_CONTENT_ECONA_IMAGE_FILE_NOT_FOUND'));
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

		$image->toFile(JPATH_SITE . '/media/econa/tmp/' . $upload, $source->type);
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

	private function saveToFilesystem($upload, $filename)
	{
		$path = JPATH_SITE . '/media/econa/tmp/' . $upload;

		if ($upload && JFile::exists($path))
		{
			if (!JFolder::exists($this->savepath))
			{
				JFolder::create($this->savepath);
			}

			$source = $this->getSourceProperties($path);

			foreach ($this->sizes as $size)
			{
				$target = $this->getTargetProperties('econa.tab', $source, $size, $filename);
				$image = new JImage($source->path);
				$resized = $image->resize($target->width, 0);
				$resized->toFile($target->path, $target->type, array('quality' => $target->quality));
				$resized->destroy();
				$image->destroy();
			}

			JFile::delete($path);

			return true;
		}

		return false;
	}

	private function deleteFromFilesystem($image)
	{
		foreach ($image->sizes as $identifier)
		{
			$path = $this->savepath . '/' . $image->filename . '_' . $identifier . '.' . $image->extension;

			if (JFile::exists($path))
			{
				JFile::delete($path);
			}
		}
	}

	private function validateFilename($filename, $articleId)
	{
		$filename = JFilterOutput::stringURLUnicodeSlug($filename);

		if (!$filename)
		{
			$filename = uniqid('plg_content_econa_');
		}
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('resourceId'));
		$query->from($db->quoteName('#__econa'));
		$query->where($db->quoteName('resourceType') . ' = ' . $db->quote('com_content.article'));
		$query->where($db->quoteName('filename') . ' = ' . $db->quote($filename));
		$query->where($db->quoteName('resourceId') . ' != ' . $db->quote($articleId));
		$db->setQuery($query);
		$result = $db->loadResult();

		if ($result)
		{
			$filename .= '_' . uniqid();
		}

		return $filename;
	}

	private function getSizes($option, $params = null)
	{
		$array = array();

		if (is_null($params))
		{
			$params = $this->params;
		}

		$value = $params->get($option);

		if(is_string($value))
		{
			$sizes = json_decode($params->get($option));

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
		elseif(is_object($value))
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

		return ($a->width > $b->width) ? -1 : 1;
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

	private function getTargetProperties($context, $source, $size = null, $filename = null)
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
		if (($context == 'econa.tab' && $this->params->get('jpeg', true)) || (in_array($context, array('econa.content.simple', 'econa.content.responsive')) && $this->params->get('content_images_jpeg', true)))
		{
			$type = IMAGETYPE_JPEG;
			$extension = 'jpg';
		}

		// Detect width and quality based on context
		if ($context == 'econa.tab')
		{
			$width = $size->width;
			$quality = $size->quality;
		}
		elseif ($context == 'econa.content.responsive')
		{
			$width = $size->width;
			$quality = $size->quality;
		}
		elseif ($context == 'econa.content.simple')
		{
			$quality = (int) $this->params->get('content_images_quality', 100);
			$width = 0;
		}

		// Detect path
		$path = $extension != $source->extension ? JFile::stripExt($source->path) . '.' . $extension : $source->path;

		if ($context == 'econa.tab')
		{
			$path = $this->savepath . '/' . $filename . '_' . $size->identifier . '.' . $extension;
			$url = $this->baseUrl . '/' . $filename . '_' . $size->identifier . '.' . $extension;
		}
		elseif ($context == 'econa.content.responsive')
		{
			$path = dirname($source->path) . '/' . JFile::stripExt(basename($source->path)) . '_' . $size->identifier . '.' . $extension;
		}
		elseif ($context == 'econa.content.simple')
		{
			$path = JFile::stripExt($source->path) . '.' . $extension;
		}

		// URL
		if (!isset($url))
		{
			$url = JUri::root(true) . '/' . substr($path, strlen(JPATH_SITE . '/'));
		}
		$url = str_replace('\\', '/', $url);

		// Normalise quality for PNG processing
		if ($type === IMAGETYPE_PNG)
		{
			$pngQuality = ($quality - 100) / 11.111111;
			$quality = round(abs($pngQuality));
			$quality = (int) $quality;
		}

		return (object) array('type' => $type, 'extension' => $extension, 'width' => $width, 'quality' => $quality, 'path' => $path, 'url' => $url);
	}
}
