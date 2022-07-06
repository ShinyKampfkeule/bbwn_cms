<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$news_arr = [];
$news_1 = FALSE;
$news_2 = FALSE;
// $path = dirname(__FILE__, 2);

?>
<?php foreach ($this->lead_items as &$item) : ?>
	<?php if ($item->category_alias === 'news') : ?>
		<?php if ($news_1 === FALSE) :?>
			<?php $news_1 = $item ?>
		<?php elseif ($news_2 === FALSE) : ?>
			<?php $news_2 = $item ?>
		<?php else : ?>
			<?php array_push($news_arr, $item) ?>
		<?php endif; ?>
	<?php elseif ($item->parent_route === 'events') : ?>
		<?php $event = $item ?>
	<?php elseif ($item->category_alias === 'about-us') : ?>
		<?php $about = $item ?>
	<?php endif; ?>
<?php endforeach; ?>

<section class="featured-content__content">
	<h1 class="featured-content__news">Neuigkeiten</h1>
	<h1 class="featured-content__upc-events">Nächste Veranstaltung</h1>
	<section class="featured-content__news-grid">
		<section class="grid-element-1-1_2"> 
			<div class="carousel" aria-label="Gallery">
				<ol class="carousel__viewport">
					<?php 
						$counter = 0;
					?>
					<?php foreach ($news_arr as $key => &$item) : ?>
						<?php $counter += 1 ?>
						<li id='carousel__slide<?php echo $counter?>'
						tabindex="0"
						class="carousel__slide">
							<?php 
								$id = json_decode( $item -> jcfields[4] -> rawvalue ) -> itemId;
								$name = json_decode( $item -> jcfields[4] -> rawvalue ) -> filename;
							?>
							<?php if ($name !== "") : ?>
								<?php $bild = "images/econa/fields/4/com_content_article/{$id}/{$name}_L.jpg"; ?>
							<?php else : ?>
								<?php $bild = "images/placeholder/gelände.png"; ?>
							<?php endif; ?>
							<div class="carousel__content">
								<h2 class="carousel__heading"><?php echo( $item -> title ) ?></h2>
								<picture class="carousel__picture">
									<img class="carousel__image" src="<?php echo $bild ?>" />
								</picture>
								<section class="carousel__popup">
									<p class="carousel__text">
										<?php echo substr( $item -> jcfields[3] -> rawvalue, 0, 250 );  ?>...
									</p>
								</section>
							</div>
							<div class="carousel__snapper">
								<?php if ($counter == 1) : ?>
									<?php $lastKey = count($news_arr) ?>
									<?php $ref = "#carousel__slide{$lastKey}" ?>
								<?php else : ?>
									<?php $prevCounter = $counter - 1; ?>
									<?php $ref = "#carousel__slide{$prevCounter}" ?>
								<? endif; ?>
								<?php if ($counter === count($news_arr)) : ?>
									<?php $refNext = "#carousel__slide1" ?>
								<?php else : ?>
									<?php $nextCounter = $counter + 1; ?>
									<?php $refNext = "#carousel__slide{$nextCounter}" ?>
								<? endif; ?>
								<a href="<?php echo $ref ?>" class="carousel__prev">Go to last slide</a>
								<a href="<?php echo $refNext ?>" class="carousel__next">Go to next slide</a>
							</div>
						</li>
					<? endforeach; ?>
				</ol>
				<aside class="carousel__navigation">
					<ol class="carousel__navigation-list">
						<?php foreach ($news_arr as $key => &$item) : ?>
							<?php $key += 1; ?>
							<li class="carousel__navigation-item">
								<a href="#carousel__slide<?php echo $key ?> " class="carousel__navigation-button">Go to slide <?php echo $key ?></a>
							</li>
						<? endforeach; ?>
					</ol>
				</aside>
			</div>
		</section>
		<section class="grid-element-1_2-3 event">
			<div class="event__text-container grid-element-1-1">
				<h2 class="event__heading">Unsere nächste Veranstaltung:</h2>
				<h2 class="event__title"><?php echo $event -> title ?></h2>
			</div>
			<?php 
				$id = json_decode( $event -> jcfields[11] -> rawvalue ) -> itemId;
				$name = json_decode( $event -> jcfields[11] -> rawvalue ) -> filename;
				$bild5 = "images/econa/fields/11/com_content_article/{$id}/{$name}_L.jpg";
			?>
			<picture class="event__picture grid-element-1-1">
                <img class="layout2__image"  src="<?php echo $bild5 ?>" />
            </picture>
		</section>
		<section class="grid-element-2-1 news-container">
			<?php 
				$videoId = $news_1 -> jcfields[5] -> rawvalue;
				$videoLink = "https://www.youtube.com/embed/{$videoId}?autoplay=1&mute=1&loop=1&controls=0";
				$id = json_decode( $news_1 -> jcfields[4] -> rawvalue ) -> itemId;
				$name = json_decode( $news_1 -> jcfields[4] -> rawvalue ) -> filename;
				$bild6 = "images/econa/fields/4/com_content_article/{$id}/{$name}_L.jpg";
			?>
			<h2 class="news-container__heading grid-element-1-1"><?php echo substr( $news_1 -> title, 0, 50 ); ?></h2>
			<?php if ($videoId !== "") : ?>
				<iframe class="news-container__video grid-element-1-1" src="<?php echo $videoLink ?>"></iframe>
			<?php else : ?>
				<picture class="news-container__picture grid-element-1-1">
                <img class="news-container__image"  src="<?php echo $bild6 ?>" />
            </picture>
			<?php endif; ?>
			<section class="news-container__popup grid-element-1-1">
				<p class="news-container__text">
				<?php echo substr( $news_1 -> jcfields[3] -> rawvalue, 0, 200 );  ?>...
				</p>
			</section>
		</section>
		<section class="grid-element-2-2 news-container">
			<?php 
				$id = json_decode($news_2 -> jcfields[4] -> rawvalue ) -> itemId;
				$name = json_decode( $news_2 -> jcfields[4] -> rawvalue ) -> filename;
				$bild7 = "images/econa/fields/4/com_content_article/{$id}/{$name}_L.jpg";
			?>
			<h2 class="news-container__heading grid-element-1-1"><?php echo substr( $news_2 -> title, 0, 50 ); ?></h2>
			<picture class="news-container__picture grid-element-1-1">
                <img class="news-container__image"  src="<?php echo $bild7 ?>" />
            </picture>
			<section class="news-container__popup grid-element-1-1">
				<p class="news-container__text">
					<?php echo substr( $news_2 -> jcfields[3] -> rawvalue, 0, 200 );  ?>...
				</p>
			</section>
		</section>
	</section>
	<section class="featured-content__about-us">
		<?php 
			$id = json_decode($about -> jcfields[4] -> rawvalue ) -> itemId;
			$name = json_decode( $about -> jcfields[4] -> rawvalue ) -> filename;
			$bild8 = "images/econa/fields/4/com_content_article/{$id}/{$name}_L.jpg";
		?>
		<h2><?php echo $about -> title ?></h2>
		<picture class="grid-element-1-1">
			<img class="layout2__image"  src="<?php echo $bild8 ?>" />
		</picture>
		<p>
			<?php echo $about -> jcfields[2] -> rawvalue;  ?>
		</p>
	</section>
</section>