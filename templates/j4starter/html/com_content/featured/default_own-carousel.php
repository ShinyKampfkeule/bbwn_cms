<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<div class="own-carousel" aria-label="Gallery">
    <ol class="own-carousel__viewport">
        <?php 
            $counter = 0;
        ?>
        <?php foreach ($this -> news_arr as $key => &$item) : ?>
            <?php $counter += 1 ?>
            <li id='own-carousel__slide<?php echo $counter?>'
            tabindex="0"
            class="own-carousel__slide">
                <?php 
                    $id = json_decode( $item -> jcfields[4] -> rawvalue ) -> itemId;
                    $name = json_decode( $item -> jcfields[4] -> rawvalue ) -> filename;
                ?>
                <?php if ($name !== "") : ?>
                    <?php $bild = "images/econa/fields/4/com_content_article/{$id}/{$name}_L.jpg"; ?>
                <?php else : ?>
                    <?php $bild = "images/placeholder/gelände.png"; ?>
                <?php endif; ?>
                <div class="own-carousel__content">
                    <h2 class="own-carousel__heading heading2"><?php echo( $item -> title ) ?></h2>
                    <picture class="own-carousel__picture">
                        <img class="own-carousel__image" src="<?php echo $bild ?>" />
                    </picture>
                    <section class="own-carousel__popup">
                        <p class="own-carousel__text text2">
                            <?php echo substr( $item -> jcfields[3] -> rawvalue, 0, 250 );  ?>...
                        </p>
                    </section>
                </div>
                <div class="own-carousel__snapper">
                    <?php if ($counter == 1) : ?>
                        <?php $lastKey = count($this -> news_arr) ?>
                        <?php $ref = "#own-carousel__slide{$lastKey}" ?>
                    <?php else : ?>
                        <?php $prevCounter = $counter - 1; ?>
                        <?php $ref = "#own-carousel__slide{$prevCounter}" ?>
                    <? endif; ?>
                    <?php if ($counter === count($this -> news_arr)) : ?>
                        <?php $refNext = "#own-carousel__slide1" ?>
                    <?php else : ?>
                        <?php $nextCounter = $counter + 1; ?>
                        <?php $refNext = "#own-carousel__slide{$nextCounter}" ?>
                    <? endif; ?>
                    <a href="<?php echo $ref ?>" class="own-carousel__prev">Go to last slide</a>
                    <a href="<?php echo $refNext ?>" class="own-carousel__next">Go to next slide</a>
                </div>
            </li>
        <? endforeach; ?>
    </ol>
    <aside class="own-carousel__navigation">
        <ol class="own-carousel__navigation-list">
            <?php foreach ($this -> news_arr as $key => &$item) : ?>
                <?php $key += 1; ?>
                <li class="own-carousel__navigation-item">
                    <a href="#own-carousel__slide<?php echo $key ?> " class="own-carousel__navigation-button">Go to slide <?php echo $key ?></a>
                </li>
            <? endforeach; ?>
        </ol>
    </aside>
</div>