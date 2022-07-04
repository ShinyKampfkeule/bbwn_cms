<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
defined('_JEXEC') or die;

ini_set('memory_limit', '512M');
jimport('joomla.image.image');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class PlgContentEcona_Inline_Images extends JPlugin
{
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage('plg_content_econa_inline_images', JPATH_PLUGINS . '/content/econa_inline_images/language');
		$this->loadLanguage('plg_content_econa_inline_images.sys', JPATH_PLUGINS . '/content/econa_inline_images/language');
		if ($this->params->get('rezize_content_images') == '1')
		{
			$this->params->set('rezize_content_images', 'simple');
		}
		$this->contentImagesSizes = $this->getSizes('content_images_sizes');
	}

	public function onContentBeforeSave($context, $article, $isNew)
	{
		// Responsive images should be triggered before save since it will modify the markup
		if ($this->params->get('rezize_content_images') == 'responsive' && ($context == 'com_content.article' || $context == 'com_content.form'))
		{
			$article->introtext = $this->responsiveContentImages($article->introtext);
			$article->fulltext = $this->responsiveContentImages($article->fulltext);
		}
	}

	public function onK2BeforeSave($item, $isNew)
	{
		// Responsive images should be triggered before save since it will modify the markup
		if ($this->params->get('rezize_content_images') == 'responsive')
		{
			$item->introtext = $this->responsiveContentImages($item->introtext);
			$item->fulltext = $this->responsiveContentImages($item->fulltext);
		}
	}

	public function onContentAfterSave($context, $article, $isNew)
	{
		// Content images
		if ($this->params->get('rezize_content_images', 'simple') == 'simple' && ($context == 'com_content.article' || $context == 'com_content.form' || $context == 'com_k2.item'))
		{
			$this->resizeContentImages($article, $context);
		}
	}

	public function onContentBeforeDisplay($context, &$article, &$params, $page = 0)
	{
		// Valid plugin contexts
		$contexts = array('com_content.article', 'com_content.category', 'com_content.archive', 'com_content.featured');

		// Load responsive images polyfill if required
		if (in_array($context, $contexts) && $this->params->get('rezize_content_images') == 'responsive')
		{
			$document = JFactory::getDocument();
			$document->addScript(JUri::root(true) . '/plugins/content/econa/js/picturefill.min.js');
		}
	}

	private function resizeContentImages($row, $context)
	{
		$texts = array('introtext' => $row->introtext, 'fulltext' => $row->fulltext);

		foreach ($texts as $property => $text)
		{
			$html = trim($text);

			if ($html)
			{
				$dom = new DOMDocument();
				$loaded = $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);

				if ($loaded)
				{
					$images = $dom->getElementsByTagName('img');

					if ($images->length > 0)
					{
						foreach ($images as $image)
						{
							$src = $image->getAttribute('src');
							$width = (int) $image->getAttribute('width');
							$height = (int) $image->getAttribute('height');
							$path = JPath::clean(JPATH_SITE . '/' . $src);

							if (JFile::exists($path))
							{
								try
								{
									$source = $this->getSourceProperties($path);
									$target = $this->getTargetProperties('econa.content.simple', $source);

									if (!$width)
									{
										$maximumWidth = (int) $this->params->get('content_images_max_width', 1000);

										if ($source->width > $maximumWidth)
										{
											$width = $maximumWidth;
										}
									}

									if ($width)
									{
										$processor = new JImage($path);
										$scaleMethod = $height > 0 ? JImage::SCALE_FILL : JImage::SCALE_INSIDE;
										$processor->resize($width, $height, false, $scaleMethod);
										$processor->toFile($target->path, $target->type, array('quality' => $target->quality));
										$processor->destroy();

										if ($source->path != $target->path)
										{
											$text = str_replace($src, $target->url, $text);
											$db = JFactory::getDbo();
											$query = $db->getQuery(true);
											$tableName = $context == 'com_k2.item' ? '#__k2_items' : '#__content';
											$query->update($db->quoteName($tableName))->set($db->quoteName($property) . ' = ' . $db->quote($text))->where($db->quoteName('id') . ' = ' . $row->id);
											$db->setQuery($query);
											$db->execute();
										}
									}
								}
								catch (Exception $e)
								{
									JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
								}
							}
						}
					}
				}
			}
		}
	}

	private function responsiveContentImages($html)
	{
		$html = trim($html);

		if ($html)
		{
			$dom = new DOMDocument();
			$loaded = $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);

			if ($loaded)
			{
				$images = $dom->getElementsByTagName('img');

				if ($images->length > 0)
				{
					$contentChanged = false;

					foreach ($images as $image)
					{
						$src = $image->getAttribute('src');
						$srcset = trim((string) $image->getAttribute('srcset'));
						$sizes = trim((string) $image->getAttribute('sizes'));
						$path = JPath::clean(JPATH_SITE . '/' . $src);

						if (JFile::exists($path) && $srcset == '' && $sizes == '')
						{
							try
							{
								$srcset = array();
								$source = $this->getSourceProperties($path);

								foreach ($this->contentImagesSizes as $contentImageSize)
								{
									$target = $this->getTargetProperties('econa.content.responsive', $source, $contentImageSize);
									$processor = new JImage($source->path);
									$resizeWidth = $source->width <= $target->width ? $source->width : $target->width;
									$scaled = $processor->resize($resizeWidth, 0, true, JImage::SCALE_INSIDE);
									$scaled->toFile($target->path, $target->type, array('quality' => $target->quality));
									$scaled->destroy();
									$processor->destroy();
									$srcset[] = $target->url . ' ' . $resizeWidth . 'w';
								}
								$image->setAttribute('srcset', implode(', ', $srcset));
								$sizes = trim($this->params->get('content_images_default_sizes_attribute', '100vw'));

								if ($sizes == '')
								{
									$sizes = '100vw';
								}
								$image->setAttribute('sizes', $sizes);
								$width = $image->getAttribute('width');

								if (!$width)
								{
									$image->setAttribute('width', $source->width);
								}
								$contentChanged = true;
							}
							catch (Exception $e)
							{
								JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
							}
						}
					}

					if ($contentChanged)
					{
						$body = $dom->getElementsByTagName('body')->item(0);
						$output = new DOMDocument();

						foreach ($body->childNodes as $child)
						{
							$output->appendChild($output->importNode($child, true));
						}
						$html = $output->saveHTML();
					}
				}
			}
		}

		return $html;
	}

	private function getSizes($option, $params = null)
	{
		$array = array();

		if (is_null($params))
		{
			$params = $this->params;
		}

		$value = $params->get($option);

		if (is_string($value))
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
		if (in_array($context, array('econa.content.simple', 'econa.content.responsive')) && $this->params->get('content_images_jpeg', true))
		{
			$type = IMAGETYPE_JPEG;
			$extension = 'jpg';
		}

		// Detect width and quality based on context
		if ($context == 'econa.content.responsive')
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

		if ($context == 'econa.content.responsive')
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
