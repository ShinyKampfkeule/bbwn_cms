<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2022 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Version;

defined('_JEXEC') or die;

/**
 * PWT ACL Diagnostics Controller
 *
 * @since   3.0
 */
class PwtAclControllerDiagnostics extends BaseController
{
	/**
	 * Rebuild the assets table
	 *
	 * @return  void
	 * @since   3.0
	 */
	public function rebuild()
	{
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		/** @var PwtAclModelDiagnostics $model */
		$model = $this->getModel('diagnostics');
		$model->rebuildAssetsTable();

		// Redirect and show message
		$this->setMessage(Text::_('COM_PWTACL_DIAGNOSTICS_STEP_REBUILD_SUCCESS'));
		$this->setRedirect(Route::_('index.php?option=com_pwtacl&view=diagnostics', false));
	}

	/**
	 * Run the diagnostics checks
	 *
	 * @return  void
	 * @since   3.0
	 * @throws  Exception on errors
	 */
	public function runDiagnostics()
	{
		// Initialise variables.
		$step = $this->input->getInt('step', 1);

		/** @var PwtAclModelDiagnostics $model */
		$model   = $this->getModel('diagnostics');
		$changes = $model->runDiagnostics($step);

		$nextStep = $step + 1;

		if (Version::MAJOR_VERSION === 3 && $nextStep === 12)
		{
			$nextStep = 15;
		}

		echo new JsonResponse($changes, $nextStep);

		Factory::getApplication()->close();
	}
}
