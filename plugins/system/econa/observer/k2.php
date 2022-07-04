<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
defined('_JEXEC') or die;

class JTableObserverK2 extends JTableObserver
{
	protected $dispatcher;

	public static function createObserver(JObservableInterface $observableObject, $params = array())
	{
		$observer = new self($observableObject);
		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('k2', 'econa');
		$observer->dispatcher = $dispatcher;

		return $observer;
	}

	public function onAfterDelete($data)
	{
		$id = (int) $data['id'];

		if ($id)
		{
			$this->dispatcher->trigger('onAfterK2Delete', array($id));
		}
	}
}
