<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

defined('_JEXEC') or die; $image = $field->image; ?>

<?php if ($field->fieldparams->get('image', 1) && $image->src): ?>
<div class="econaArticleImageBlock">

	<figure class="econaImage">

		<?php if ($image->link): ?>
		<a href="<?php echo $image->link; ?>">
		<?php elseif ($image->modal) :?>
		<a href="<?php echo $image->modal; ?>" title="<?php echo JText::_('PLG_FIELDS_ECONA_CLICK_TO_PREVIEW_IMAGE'); ?>" class="econaFieldModal">
		<?php endif; ?>

		<picture>
			<?php if ($image->srcsetWebp): ?>
			<source <?php if ($image->sizes): ?> sizes="<?php echo $image->sizes; ?>" <?php endif; ?> srcset="<?php echo $image->srcsetWebp; ?>" type="image/webp">
			<?php endif; ?>
			<?php if ($image->srcset): ?>
			<source <?php if ($image->sizes): ?> sizes="<?php echo $image->sizes; ?>" <?php endif; ?> srcset="<?php echo $image->srcset; ?>" type="image/jpeg">
			<?php endif; ?>
			<img loading="lazy" <?php if ($image->width): ?> width="<?php echo $image->width; ?>" <?php endif; ?> <?php if ($image->height): ?> height="<?php echo $image->height; ?>" <?php endif; ?> src="<?php echo $image->src; ?>" alt="<?php echo htmlspecialchars($image->alt, ENT_QUOTES, 'UTF-8'); ?>" />
		</picture>

		<?php if ($image->link || $image->modal): ?>
		</a>
		<?php endif; ?>

		<?php if (($field->fieldparams->get('caption', 1) && $image->caption) || ($field->fieldparams->get('credits', 1) && $image->credits)): ?>
		<figcaption>
			<?php if ($field->fieldparams->get('caption', 1) && $image->caption): ?>
			<span class="econaImageCaption"><?php echo $image->caption; ?></span>
			<?php endif; ?>

			<?php if ($field->fieldparams->get('credits', 1) && $image->credits): ?>
			<span class="econaImageCredits"><?php echo $image->credits; ?></span>
			<?php endif; ?>
		</figcaption>
		<?php endif; ?>

	</figure>

</div>
<?php endif; ?>
