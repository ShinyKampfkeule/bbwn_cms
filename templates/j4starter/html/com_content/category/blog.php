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

<h1 class="blog-content__header"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<section class="blog-content__content">
	<?php foreach ($this->items as $key => &$item) : ?>
		<?php if ( $firstElement === FALSE ) : ?>
			<section>
				<?php
				$this->item = & $item;
				echo $this->loadTemplate("item_layout1");
				$firstElement = TRUE;
				?>
			</section>
		<?php else : ?>
			<?php if ( $counter === 1 ) : ?>
				<section class="triple-content grid-element-1-1">
					<article class="grid-element-1-1">
						<?php
						$this->item = & $item;
						echo $this->loadTemplate("item_layout2");
						?>
					</article>
			<?php elseif ( $counter === 2 ) : ?>
					<article class="grid-element-1-2">
						<?php
						$this->item = & $item;
						echo $this->loadTemplate("item_layout2");
						?>
					</article>
			<?php elseif ( $counter === 3 ) : ?>
					<article class="grid-element-1-3">
						<?php
						$this->item = & $item;
						echo $this->loadTemplate("item_layout2");
						?>
					</article>
				</section>
				<?php $counter = 0 ?>
			<?php endif; ?>
			<?php $counter = $counter + 1 ?>
		<?php endif; ?>
	<?php endforeach; ?>
</section>