<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
defined('_JEXEC') or die; ?>

<?php if ($this->params->get('list_image', 1) && $image->src): ?>
<div class="econaListImageBlock">

	<div class="econaImage">
		<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catid, $article->language)); ?>" itemprop="url">
			<img src="<?php echo $image->src; ?>" alt="<?php echo htmlspecialchars($alt, ENT_QUOTES, 'UTF-8'); ?>" <?php if($image->srcset): ?> srcset="<?php echo $image->srcset; ?>" <?php endif; ?> <?php if($image->srcset): ?> sizes="<?php echo $image->sizes; ?>" <?php endif; ?> />
		</a>
	</div>

	<?php if ($this->params->get('list_caption', 0) && $caption): ?>
	<span class="econaImageCaption"><?php echo $caption; ?></span>
	<?php endif; ?>

	<?php if ($this->params->get('list_credits', 0) && $credits): ?>
	<span class="econaImageCredits"><?php echo $credits; ?></span>
	<?php endif; ?>

</div>
<?php endif; ?>
