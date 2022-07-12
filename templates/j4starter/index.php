<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.j4starter
 *
 * @copyright   (C) YEAR Your Name
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * This is a heavily stripped down/modified version of the default Cassiopeia template, designed to build new templates off of.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/** @var Joomla\CMS\Document\HtmlDocument $this */

$app = Factory::getApplication();
$wa  = $this->getWebAssetManager();

// Add Favicon from images folder
$this->addHeadLink(HTMLHelper::_('image', 'favicon.ico', '', [], true, 1), 'icon', 'rel', ['type' => 'image/x-icon']);


// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$sitename = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');
$menu     = $app->getMenu()->getActive();
$pageclass = $menu !== null ? $menu->getParams()->get('pageclass_sfx', '') : '';

//Get params from template styling
//If you want to add your own parameters you may do so in templateDetails.xml
$testparam =  $this->params->get('testparam');

//uncomment to see how this works on site... it just shows 1 or 0 depending on option selected in style config.
//You can use this style to get/set any param according to instructions at
//echo('the value of testparam is: '.$testparam);

// Get this template's path
$templatePath = 'templates/' . $this->template;

// Load our frameworks
JHtml::_('bootstrap.framework');
JHtml::_('jquery.framework');

//Register our web assets (Css/JS)
$wa->useStyle('template.j4starter.mainstyles');
$wa->useScript('template.j4starter.scripts');

//Set viewport
$this->setMetaData('viewport', 'width=device-width, initial-scale=1');

$own_menu = $app->getMenu()->getMenu();
$user = Factory::getUser();

?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="metas" />
	<jdoc:include type="styles" />
	<jdoc:include type="scripts" />
    <link rel="stylesheet" type="text/css" href="<?php echo $templatePath; ?>/sass/main.css">
</head>

<?php if ( $this -> title === "Screen" ) : ?>
    <?php include "screen.php"; ?>
<?php else : ?>
    <?php include "page.php"; ?>
<?php endif; ?>

</html>
