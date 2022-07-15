<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Layout\LayoutHelper;

$app = Factory::getApplication();

$this->category->text = $this->category->description;
$app->triggerEvent('onContentPrepare', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$this->category->description = $this->category->text;

$results = $app->triggerEvent('onContentAfterTitle', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$afterDisplayTitle = trim(implode("\n", $results));

$results = $app->triggerEvent('onContentBeforeDisplay', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$beforeDisplayContent = trim(implode("\n", $results));

$results = $app->triggerEvent('onContentAfterDisplay', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$afterDisplayContent = trim(implode("\n", $results));

$htag    = $this->params->get('show_page_heading') ? 'h2' : 'h1';

$firstElement = FALSE;
$counter = 1;

?>

<h1 class="blog-content__header heading-800"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<section class="blog-content__test">
	<section class="blog-content__leading blog-content__bc"></section>
</section>
<section class="blog-content__test2">
	<div class="grid-element-1-1 blog-content__bc"></div>
	<div class="grid-element-1-2 blog-content__bc"></div>
	<div class="grid-element-1-3 blog-content__bc"></div>
	<div class="grid-element-1-4 blog-content__bc"></div>
	<div class="grid-element-2-1 blog-content__bc"></div>
	<div class="grid-element-2-2_3 blog-content__bc"></div>
	<div class="grid-element-2-4 blog-content__bc"></div>
	<div class="grid-element-3-1 blog-content__bc"></div>
	<div class="grid-element-3-2 blog-content__bc"></div>
	<div class="grid-element-3-3 blog-content__bc"></div>
	<div class="grid-element-3-4 blog-content__bc"></div>
</section>
<?php dump($this) ?>