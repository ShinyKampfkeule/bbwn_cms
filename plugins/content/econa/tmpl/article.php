<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
defined('_JEXEC') or die; ?>

<?php if ($this->params->get('article_image', 1) && $image->src): ?>
<div class="econaArticleImageBlock">

	<div class="econaImage">
		<?php if($image->modal): ?>
		<a href="<?php echo $image->modal; ?>" title="<?php echo JText::_('PLG_CONTENT_ECONA_CLICK_TO_PREVIEW_IMAGE'); ?>" class="econaModal">
		<?php endif; ?>
			<img src="<?php echo $image->src; ?>" alt="<?php echo htmlspecialchars($alt, ENT_QUOTES, 'UTF-8'); ?>" <?php if($image->srcset): ?> srcset="<?php echo $image->srcset; ?>" <?php endif; ?> <?php if($image->srcset): ?> sizes="<?php echo $image->sizes; ?>" <?php endif; ?> />
		<?php if($image->modal): ?>
		</a>
		<?php endif; ?>
	</div>

	<?php if ($this->params->get('article_caption', 1) && $caption): ?>
	<span class="econaImageCaption"><?php echo $caption; ?></span>
	<?php endif; ?>

	<?php if ($this->params->get('article_credits', 1) && $credits): ?>
	<span class="econaImageCredits"><?php echo $credits; ?></span>
	<?php endif; ?>

</div>
<?php endif; ?>
