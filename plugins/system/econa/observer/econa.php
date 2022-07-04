<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
defined('_JEXEC') or die;

class JTableObserverEcona extends JTableObserver
{
	protected $dispatcher;

	public static function createObserver(JObservableInterface $observableObject, $params = array())
	{
		$observer = new self($observableObject);

		return $observer;
	}

	public function onAfterLoad(&$result, $row)
	{
		$this->table->onAfterLoad();
	}
}
