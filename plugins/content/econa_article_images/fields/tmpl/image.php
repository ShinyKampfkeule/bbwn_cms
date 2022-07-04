<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2022 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
defined('_JEXEC') or die; ?>

<div class="econa-article-images-container">
	<div class="econa-container <?php echo version_compare(JVERSION, '4.0', 'ge') ? 'j4':'j3'; ?>">

		<script type="application/json" class="econa-params"><?php echo json_encode($params); ?></script>

		<img src="<?php echo ($image) ? $image : JUri::root(true) . '/media/econa/images/placeholder.jpg'; ?>" class="econa-preview-image" />

		<div class="econa-toolbar">
			<button class="btn btn-secondary" data-action="upload"><?php echo JText::_('PLG_CONTENT_ECONA_ARTICLE_IMAGES_UPLOAD'); ?></button>
			<?php if ($params->get('media', 1)): ?>
			<button class="btn btn-secondary" data-action="media-manager"><?php echo JText::_('PLG_CONTENT_ECONA_ARTICLE_IMAGES_MEDIA'); ?></button>
			<?php endif; ?>
			<button class="btn btn-secondary" data-action="edit"><?php echo JText::_('PLG_CONTENT_ECONA_ARTICLE_IMAGES_EDIT'); ?></button>
			<button class="btn btn-danger" data-action="delete"><?php echo JText::_('PLG_CONTENT_ECONA_ARTICLE_IMAGES_DELETE'); ?></button>
		</div>

		<div>
			<?php echo $this->name === 'jform[images][image_intro]' ? $form->getInput('file_intro') : $form->getInput('file_full'); ?>
		</div>
		<div>
			<?php echo $this->name === 'jform[images][image_intro]' ? $form->getInput('path_intro') : $form->getInput('path_full'); ?>
		</div>

		<input type="hidden" class="econa-current-field" name="<?php echo $this->name; ?>" id="<?php echo $this->id; ?>" value="<?php echo $this->value; ?>"  />
		<input type="hidden" class="econa-key-field" name="econa[<?php echo $this->fieldname; ?>][tmp]" id="<?php echo $this->id; ?>_econa_tmp" value="<?php echo $tmp; ?>" />
		<input type="hidden" class="econa-delete-field" name="econa[<?php echo $this->fieldname; ?>][delete]" id="<?php echo $this->id; ?>_econa_delete"  />
		<input type="hidden" class="econa-upload-field" name="econa[<?php echo $this->fieldname; ?>][upload]" id="<?php echo $this->id; ?>_econa_upload"  />
		<input type="hidden" class="econa-filename-field" name="econa[<?php echo $this->fieldname; ?>][filename]" id="<?php echo $this->id; ?>_econa_filename" value="<?php echo $filename; ?>"  />
		<input type="hidden" class="econa-width-field" name="econa[<?php echo $this->fieldname; ?>][width]" id="<?php echo $this->id; ?>_econa_width" value="<?php echo $width; ?>"  />
		<input type="hidden" class="econa-height-field" name="econa[<?php echo $this->fieldname; ?>][height]" id="<?php echo $this->id; ?>_econa_height" value="<?php echo $height; ?>"  />

		<div class="modal hide econa-modal" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-fullscreen">
				<div class="modal-content">
					<div class="modal-header">
						<h3 class="modal-title"><?php echo JText::_('PLG_CONTENT_ECONA_ARTICLE_IMAGES_EDIT_IMAGE'); ?></h3>
					</div>
					<div class="modal-body">
						<div class="econa-cropper-container">
							<img src="<?php echo $image; ?>" class="econa-cropper-image" />
						</div>
					</div>
					<div class="modal-footer">
						<div class="econa-cropper-buttons">
							<select name="econa-aspect-ratio-field" class="form-select input-small econa-aspect-ratio-field">
								<?php foreach ($ratios as $aspect_ratio): ?>
								<option value="<?php echo $aspect_ratio->value; ?>"><?php echo $aspect_ratio->label; ?></option>
								<?php endforeach; ?>
								<?php if ($params->get('free_aspect_ratio', 1)): ?>
								<option value="NaN"><?php echo JText::_('PLG_CONTENT_ECONA_ARTICLE_IMAGES_FREE'); ?></option>
								<?php endif; ?>
							</select>
							<div class="btn-group">
								<a class="btn btn-secondary" data-method="zoom" data-option="0.1" title="<?php echo JText::_('PLG_CONTENT_ECONA_ARTICLE_IMAGES_ZOOM_IN'); ?>"> <span class="icon-zoom-in"></span> </a>
								<a class="btn btn-secondary" data-method="zoom" data-option="-0.1" title="<?php echo JText::_('PLG_CONTENT_ECONA_ARTICLE_IMAGES_ZOOM_OUT'); ?>"> <span class="icon-zoom-out"></span> </a>
							</div>
							<div class="btn-group">
								<div class="input-prepend input-group input-append">
								<a class="btn btn-secondary" data-method="rotate" data-direction="left" title="<?php echo JText::_('PLG_CONTENT_ECONA_ARTICLE_IMAGES_ROTATE_LEFT'); ?>"> <span class="icon-undo-2"></span> </a>
								<input type="number" min="1" max="360" step="1" class="form-control input-mini" value="10" maxlength="3" size="3" name="econaRotateDegree" />
								<a class="btn btn-secondary" data-method="rotate" data-direction="right" title="<?php echo JText::_('PLG_CONTENT_ECONA_ARTICLE_IMAGES_ROTATE_RIGHT'); ?>"> <span class="icon-redo-2"></span> </a>
								</div>
							</div>
							<?php if (function_exists('imageflip')): ?>
							<div class="btn-group">
								<a class="btn btn-secondary" data-method="scaleX" data-option="-1" title="<?php echo JText::_('PLG_CONTENT_ECONA_ARTICLE_IMAGES_FLIP_HORIZONTAL'); ?>"> <span class="econa-icon-uniF07E"></span> </a>
								<a class="btn btn-secondary" data-method="scaleY" data-option="-1" title="<?php echo JText::_('PLG_CONTENT_ECONA_ARTICLE_IMAGES_FLIP_VERTICAL'); ?>"> <span class="econa-icon-uniF07D"></span> </a>
							</div>
							<?php endif; ?>
							<div>
								<a class="btn btn-success" data-action="apply"><?php echo JText::_('PLG_CONTENT_ECONA_ARTICLE_IMAGES_APPLY'); ?></a>
								<a class="btn btn-danger" data-action="cancel"><?php echo JText::_('PLG_CONTENT_ECONA_ARTICLE_IMAGES_CANCEL'); ?></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
