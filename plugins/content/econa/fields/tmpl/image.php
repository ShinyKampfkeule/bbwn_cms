<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
defined('_JEXEC') or die; ?>

<div id="econaContainer" <?php if (!$image) { echo ' style="display: none;"';} ?>>

	<script type="application/json" class="econa-params"><?php echo json_encode($params); ?></script>

	<div id="econaImage">
		<img src="<?php echo $image; ?>" class="econaImage" />
	</div>

	<div id="econaToolbar">
		<a class="btn btn-secondary" data-action="preview"><?php echo JText::_('PLG_CONTENT_ECONA_PREVIEW'); ?></a>
		<a class="btn btn-secondary" data-action="edit"><?php echo JText::_('PLG_CONTENT_ECONA_EDIT'); ?></a>
		<a class="btn btn-danger" data-action="delete"><?php echo JText::_('PLG_CONTENT_ECONA_DELETE'); ?></a>
	</div>

	<div id="econaCropperButtons">
		<div class="btn-group">
			<a class="btn btn-secondary" data-method="setDragMode" data-option="move" title="<?php echo JText::_('PLG_CONTENT_ECONA_MOVE'); ?>"> <span class="icon-move"></span> </a>
			<a class="btn active" data-method="setDragMode" data-option="crop" title="<?php echo JText::_('PLG_CONTENT_ECONA_CROP'); ?>"> <span class="icon-contract-2"></span> </a>
		</div>
		<select name="econaAspectRatio" class="form-select input-small">
			<?php foreach ($ratios as $aspect_ratio): ?>
			<option value="<?php echo $aspect_ratio->value; ?>"><?php echo $aspect_ratio->label; ?></option>
			<?php endforeach; ?>
			<?php if($params->get('free_aspect_ratio', 1)): ?>
			<option value="NaN"><?php echo JText::_('PLG_CONTENT_ECONA_FREE'); ?></option>
			<?php endif; ?>
		</select>
		<div class="btn-group">
			<a class="btn btn-secondary" data-method="zoom" data-option="0.1" title="<?php echo JText::_('PLG_CONTENT_ECONA_ZOOM_IN'); ?>"> <span class="icon-zoom-in"></span> </a>
			<a class="btn btn-secondary" data-method="zoom" data-option="-0.1" title="<?php echo JText::_('PLG_CONTENT_ECONA_ZOOM_OUT'); ?>"> <span class="icon-zoom-out"></span> </a>
		</div>
		<div class="btn-group input-group input-prepend input-append">
			<a class="btn btn-secondary" data-method="rotate" data-direction="left" title="<?php echo JText::_('PLG_CONTENT_ECONA_ROTATE_LEFT'); ?>"> <span class="icon-undo-2"></span> </a>
			<input type="number" min="1" max="360" step="1" class="form-control input-mini" value="10" maxlength="3" size="3" name="econaRotateDegree" />
			<a class="btn btn-secondary" data-method="rotate" data-direction="right" title="<?php echo JText::_('PLG_CONTENT_ECONA_ROTATE_RIGHT'); ?>"> <span class="icon-redo-2"></span> </a>
		</div>
		<?php if (function_exists('imageflip')): ?>
		<div class="btn-group">
			<a class="btn btn-secondary" data-method="scaleX" data-option="-1" title="<?php echo JText::_('PLG_CONTENT_ECONA_FLIP_HORIZONTAL'); ?>"> <span class="econa-icon-uniF07E"></span> </a>
			<a class="btn btn-secondary" data-method="scaleY" data-option="-1" title="<?php echo JText::_('PLG_CONTENT_ECONA_FLIP_VERTICAL'); ?>"> <span class="econa-icon-uniF07D"></span> </a>
		</div>
		<?php endif; ?>
		<div class="btn-group">
			<a class="btn btn-success" data-action="apply"><?php echo JText::_('PLG_CONTENT_ECONA_APPLY'); ?></a>
			<a class="btn btn-danger" data-action="cancel"><?php echo JText::_('PLG_CONTENT_ECONA_CANCEL'); ?></a>
		</div>
	</div>

</div>
