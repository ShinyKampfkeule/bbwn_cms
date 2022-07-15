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
<section class="grid-element-3-1 news-container">
    <?php 
        $videoId = $this -> news_1 -> jcfields[5] -> rawvalue;
        $videoLink = "https://www.youtube.com/embed/{$videoId}?autoplay=1&mute=1&loop=1&controls=0";
        $id = json_decode( $this -> news_1 -> jcfields[13] -> rawvalue ) -> itemId;
        $name = json_decode( $this -> news_1 -> jcfields[13] -> rawvalue ) -> filename;
        $bild6 = "images/econa/fields/13/com_content_article/{$id}/{$name}_L.jpg";
    ?>
    <h2 class="news-container__heading grid-element-1-1 <?php echo( strlen( $this -> news_1 -> title ) <= 40 ) ? 'heading-400' : 'heading-300' ?>"><?php echo substr( $this -> news_1 -> title, 0, 50 ); ?></h2>
    <?php if ( $videoId !== "" ) : ?>
        <iframe class="news-container__video grid-element-1-1" src="<?php echo $videoLink ?>"></iframe>
    <?php else : ?>
        <picture class="news-container__picture grid-element-1-1">
            <img class="news-container__image"  src="<?php echo $bild6 ?>" />
        </picture>
    <?php endif; ?>
    <section class="news-container__popup grid-element-1-1">
        <p class="news-container__text text-100">
            <?php echo substr( $this -> news_1 -> jcfields[3] -> rawvalue, 0, 170 );  ?>...
        </p>
    </section>
</section>
<section class="grid-element-3-2 news-container">
    <?php 
        $id = json_decode( $this -> news_2 -> jcfields[4] -> rawvalue ) -> itemId;
        $name = json_decode( $this -> news_2 -> jcfields[4] -> rawvalue ) -> filename;
        $bild7 = "images/econa/fields/4/com_content_article/{$id}/{$name}_L.jpg";
    ?>
    <h2 class="news-container__heading grid-element-1-1 heading-400"><?php echo substr( $this -> news_2 -> title, 0, 50 ); ?></h2>
    <picture class="news-container__picture grid-element-1-1">
        <img class="news-container__image"  src="<?php echo $bild7 ?>" />
    </picture>
    <section class="news-container__popup grid-element-1-1">
        <p class="news-container__text text-100">
            <?php echo substr( $this -> news_2 -> jcfields[3] -> rawvalue, 0, 170 );  ?>...
        </p>
    </section>
</section>