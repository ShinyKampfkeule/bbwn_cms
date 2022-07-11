
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