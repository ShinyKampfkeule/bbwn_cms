<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
defined('_JEXEC') or die;

class PlgInstallerEcona extends JPlugin
{
	public function onInstallerBeforePackageDownload(&$url, &$headers)
	{
		if (strpos($url, 'firecoders.com') && strpos($url, 'econa'))
		{
			$separator = strpos($url, '?') !== false ? '&' : '?';
			$url .= $separator . 'dlid=' . $this->params->get('downloadId');
		}

		return true;
	}
}
