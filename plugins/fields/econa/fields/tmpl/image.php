<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
defined('_JEXEC') or die; ?>

<div class="econa-container <?php echo version_compare(JVERSION, '4.0', 'ge') ? 'j4':'j3'; ?>">

	<script type="application/json" class="econa-params"><?php echo json_encode($data->params); ?></script>

	<img src="<?php echo ($image) ? $image : JUri::root(true) . '/media/econa/images/placeholder.jpg'; ?>" class="econa-preview-image" />

	<div class="econa-toolbar">
		<button class="btn btn-secondary" data-action="upload"><?php echo JText::_('PLG_FIELDS_ECONA_UPLOAD'); ?></button>
		<?php if($data->params->media): ?>
		<button class="btn btn-secondary" data-action="media-manager"><?php echo JText::_('PLG_FIELDS_ECONA_MEDIA'); ?></button>
		<?php endif; ?>
		<button class="btn btn-secondary" data-action="edit"><?php echo JText::_('PLG_FIELDS_ECONA_EDIT'); ?></button>
		<button class="btn btn-danger" data-action="delete"><?php echo JText::_('PLG_FIELDS_ECONA_DELETE'); ?></button>
	</div>

	<div class="modal hide econa-modal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-fullscreen	">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="modal-title"><?php echo JText::_('PLG_FIELDS_ECONA_EDIT_IMAGE'); ?></h3>
				</div>
				<div class="modal-body">
					<div class="econa-cropper-container">
						<img src="<?php echo $image; ?>" class="econa-cropper-image" />
					</div>
				</div>
				<div class="modal-footer">
					<div class="econa-cropper-buttons">
						<select name="econa-aspect-ratio-field" class="form-select input-small econa-aspect-ratio-field">
							<?php foreach ($data->ratios as $aspect_ratio): ?>
							<option value="<?php echo $aspect_ratio->value; ?>"><?php echo $aspect_ratio->label; ?></option>
							<?php endforeach; ?>
							<?php if ($data->free_ratio): ?>
							<option value="NaN"><?php echo JText::_('PLG_FIELDS_ECONA_FREE'); ?></option>
							<?php endif; ?>
						</select>
						<div class="btn-group">
							<a class="btn btn-secondary" data-method="zoom" data-option="0.1" title="<?php echo JText::_('PLG_FIELDS_ECONA_ZOOM_IN'); ?>"> <span class="icon-zoom-in"></span> </a>
							<a class="btn btn-secondary" data-method="zoom" data-option="-0.1" title="<?php echo JText::_('PLG_FIELDS_ECONA_ZOOM_OUT'); ?>"> <span class="icon-zoom-out"></span> </a>
						</div>
						<div class="btn-group">
							<div class="input-prepend input-group input-append">
							<a class="btn btn-secondary" data-method="rotate" data-direction="left" title="<?php echo JText::_('PLG_FIELDS_ECONA_ROTATE_LEFT'); ?>"> <span class="icon-undo-2"></span> </a>
							<input type="number" min="1" max="360" step="1" class="form-control input-mini" value="10" maxlength="3" size="3" name="econaRotateDegree" />
							<a class="btn btn-secondary" data-method="rotate" data-direction="right" title="<?php echo JText::_('PLG_FIELDS_ECONA_ROTATE_RIGHT'); ?>"> <span class="icon-redo-2"></span> </a>
							</div>
						</div>
						<?php if (function_exists('imageflip')): ?>
						<div class="btn-group">
							<a class="btn btn-secondary" data-method="scaleX" data-option="-1" title="<?php echo JText::_('PLG_FIELDS_ECONA_FLIP_HORIZONTAL'); ?>"> <span class="econa-icon-uniF07E"></span> </a>
							<a class="btn btn-secondary" data-method="scaleY" data-option="-1" title="<?php echo JText::_('PLG_FIELDS_ECONA_FLIP_VERTICAL'); ?>"> <span class="econa-icon-uniF07D"></span> </a>
						</div>
						<?php endif; ?>
						<div>
							<a class="btn btn-success" data-action="apply"><?php echo JText::_('PLG_FIELDS_ECONA_APPLY'); ?></a>
							<a class="btn btn-danger" data-action="cancel"><?php echo JText::_('PLG_FIELDS_ECONA_CANCEL'); ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
