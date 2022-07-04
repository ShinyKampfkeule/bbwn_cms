<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
defined('_JEXEC') or die;

class Pkg_EconaInstallerScript
{
	public function install($parent)
	{
		$installer = $parent->getParent();
		$db = JFactory::getDbo();
		$sql = $installer->getPath('source') . '/install.sql';
		$queries = JDatabaseDriver::splitSql(file_get_contents($sql));

		foreach ($queries as $query)
		{
			$query = trim($query);

			if ($query != '' && $query[0] != '#')
			{
				$db->setQuery($query);

				if (!$db->execute())
				{
					$installer->abort(JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));

					return false;
				}
			}
		}
	}

	public function postflight($type, $parent)
	{
		$db = JFactory::getDbo();

		if ($type == 'install')
		{
			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__extensions'));
			$query->set($db->quoteName('enabled') . ' = 1');
			$query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
			$query->where('(' . $db->quoteName('element') . ' = ' . $db->quote('econa') . ' OR ' . $db->quoteName('element') . ' = ' . $db->quote('econa_article_images') . ' OR ' . $db->quoteName('element') . ' = ' . $db->quote('econa_inline_images') . ')');
			$db->setQuery($query);
			$db->execute();
		}
		elseif ($type == 'update')
		{
			$fields = $db->getTableColumns('#__econa');

			if (!array_key_exists('sizes', $fields))
			{
				$query = 'ALTER TABLE ' . $db->quoteName('#__econa') . ' ADD ' . $db->quoteName('sizes') . ' VARCHAR(255) NOT NULL';
				$db->setQuery($query);
				$db->execute();
				$query = $db->getQuery(true);
				$query->update($db->quoteName('#__econa'));
				$query->set($db->quoteName('sizes') . ' = ' . $db->quote(json_encode(array('S', 'M', 'L'))));
				$db->setQuery($query);
				$db->execute();
				$query = $db->getQuery(true);
				$query->select($db->quoteName('params'));
				$query->from($db->quoteName('#__extensions'));
				$query->where($db->quoteName('element') . ' = ' . $db->quote('econa'));
				$query->where($db->quoteName('folder') . ' = ' . $db->quote('content'));
				$db->setQuery($query);
				$params = json_decode($db->loadResult());
				$sizes = new stdClass();
				$sizes->label = array('Small', 'Medium', 'Large');
				$sizes->identifier = array('S', 'M', 'L');
				$sizes->width = array(300, 600, 900);
				$sizes->quality = array(65, 80, 95);
				$params->sizes = json_encode($sizes);
				$params->article_image = array('M');
				$params->article_modal = 'L';
				$params->list_image = array('S');
				$params->rezize_content_images = 'simple';
				$query = $db->getQuery(true);
				$query->update($db->quoteName('#__extensions'));
				$query->set($db->quoteName('params') . ' = ' . $db->quote(json_encode($params)));
				$query->where($db->quoteName('element') . ' = ' . $db->quote('econa'));
				$query->where($db->quoteName('folder') . ' = ' . $db->quote('content'));
				$db->setQuery($query);
				$db->execute();
			}

			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__extensions'));
			$query->set($db->quoteName('enabled') . ' = 1');
			$query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
			$query->where('(' . $db->quoteName('element') . ' = ' . $db->quote('econa') . ' OR ' . $db->quoteName('element') . ' = ' . $db->quote('econa_article_images') . ' OR ' . $db->quoteName('element') . ' = ' . $db->quote('econa_inline_images') . ')');
			$db->setQuery($query);
			$db->execute();

			$this->convertSizesOptions();
		}
	}

	public function uninstall($parent)
	{
		$db = JFactory::getDbo();
		$db->dropTable('#__econa');
	}

	private function convertSizesOptions()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')->from($db->qn('#__fields'))->where($db->qn('type') . ' = ' . $db->q('econa'));
		$db->setQuery($query);
		$fields = $db->loadObjectList();

		foreach ($fields as $field)
		{
			$params = json_decode($field->fieldparams);

			if (isset($params->sizes) && is_string($params->sizes))
			{
				$sizes = json_decode($params->sizes);

				if (is_object($sizes) && isset($sizes->identifier))
				{
					$array = array();
					$counter = 0;

					foreach ($sizes->identifier as $key => $identifier)
					{
						$entry = new stdClass();
						$entry->label = $sizes->label[$key];
						$entry->identifier = $sizes->identifier[$key];
						$entry->width = (int) $sizes->width[$key];
						$entry->quality = (int) $sizes->quality[$key];
						$array['sizes' . $counter] = $entry;
						$counter++;
					}
					$params->sizes = (object) $array;

					$query = $db->getQuery(true);
					$query->update($db->qn('#__fields'))->set($db->qn('fieldparams') . ' = ' . $db->q(json_encode($params)))->where($db->qn('id') . ' = ' . $db->q($field->id));
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
	}
}
