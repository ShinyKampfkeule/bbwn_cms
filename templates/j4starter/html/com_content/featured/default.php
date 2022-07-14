<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$this -> news_arr = [];
$this -> news_1 = FALSE;
$this -> news_2 = FALSE;

?>
<?php foreach ($this->lead_items as &$item) : ?>
	<?php if ($item->category_alias === 'news') : ?>
		<?php if ($this -> news_1 === FALSE) :?>
			<?php $this -> news_1 = $item ?>
		<?php elseif ($this -> news_2 === FALSE) : ?>
			<?php $this -> news_2 = $item ?>
		<?php else : ?>
			<?php array_push($this -> news_arr, $item) ?>
		<?php endif; ?>
	<?php elseif ($item -> parent_route === 'events') : ?>
		<?php $this -> event = $item ?>
	<?php elseif ($item->category_alias === 'about-us') : ?>
		<?php $this -> about = $item ?>
	<?php endif; ?>
<?php endforeach; ?>

<section class="featured-content__content">
	<section class="featured-content__news-grid">
		<a href="/index.php?option=com_content&view=category&id=8&Itemid=102" class="featured-content__news heading1 grid-element-1-1">Neuigkeiten</a>	
		<a href="/index.php?option=com_content&view=category&id=9&Itemid=103" class="featured-content__upc-events heading1 grid-element-1-3">Nächste Veranstaltung</a>
		<section class="grid-element-2-1_2"> 
			<?php echo $this -> loadTemplate("own-carousel"); ?>
		</section>
		<section class="grid-element-2_3-3 event">
		<?php echo $this -> loadTemplate("event"); ?>
		</section>
		<?php echo $this -> loadTemplate("news-container"); ?>
	</section>
	<?php echo $this -> loadTemplate("about-us"); ?>
</section>