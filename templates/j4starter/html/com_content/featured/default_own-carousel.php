<?php
    /**
     * @package     Joomla.Site
     * @subpackage  com_content
     *
     * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
     * @license     GNU General Public License version 2 or later; see LICENSE.txt
     */

    defined('_JEXEC') or die;

    $firstElement = FALSE;
    $counter = 1;
    $icFirstElement = FALSE;
    $icCounter = 0;
?>
<div id="carouselExampleIndicators" class="news__carousel carousel slide fullscreen" data-bs-ride="carousel" data-bs-interval="5000">
    <div class="carousel-indicators">
        <?php foreach ($this -> news_arr as $key => $item) : ?>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="<?php echo $icCounter ?>" <?php if ( $icFirstElement === FALSE ) : ?>class="active" aria-current="true"<?php endif; ?> aria-label="Slide <?php echo $icCounter += 1; ?>"></button>
            <?php 
                $icFirstElement = TRUE;
            ?>
        <?php endforeach; ?>
    </div>
    <div class="carousel-inner">
        <?php foreach ($this -> news_arr as $key => $item) : ?>
            <?php 
                $id = json_decode( $item -> jcfields[4] -> rawvalue ) -> itemId;
                $name = json_decode( $item -> jcfields[4] -> rawvalue ) -> filename;
                $url_id = $item -> id;
	            $chl = "index.php?option=com_content&view=article&id={$url_id}";
            ?>
            <?php if ($name !== "") : ?>
                    <?php $bild = "images/econa/fields/4/com_content_article/{$id}/{$name}_L.jpg"; ?>
                <?php else : ?>
                    <?php $bild = "images/placeholder/gelände.png"; ?>
                <?php endif; ?>
            <div class="carousel-item<?php if ( $firstElement === FALSE ) : ?><?php echo " active" ?><?php endif; ?>" onClick="location.href='<?php echo $chl ?>'">
                <img class="news__carousel__bgimg" src="<?php echo $bild ?>" alt="Slide <?php echo $counter ?>">
                <div class="news__carousel__content">
                    <h2 class="<?php echo( strlen( $item -> title ) <= 60 ) ? 'news__carousel__content__heading__big' : 'news__carousel__content__heading__small' ?>"><?php echo $item -> title ?></h2>
                    <section class="news__carousel__content__popup">
                        <p class="news__carousel__content__popup__text text-200">
                            <?php echo substr( $item -> jcfields[3] -> rawvalue, 0, 250 );  ?>...
                        </p>
                    </section>
                </div>
            </div>
            <?php 
                $firstElement = TRUE; 
                $counter += 1;
            ?>
        <?php endforeach; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>

</div>