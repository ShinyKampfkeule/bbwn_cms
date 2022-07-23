<?php
    $firstElement = FALSE;
    $icFirstElement = FALSE;
    $icCounter = 0;
?>
<section class="article-layout__intro">
    <picture class="article-layout__intro__image">
        <img src="<?php echo $main_img_src ?>" />
    </picture>
    <?php if (count($heading_arr) > 0) : ?>
        <h2 class="article-layout__intro__heading"><?php echo $heading_arr[0] ?></h2>
    <?php endif; ?>
    <p class="article-layout__intro__paragraph__<?php echo (strlen($text_arr[0]) <= 600) ? 'big' : 'small' ?>"><?php echo $text_arr[0] ?></p>
</section>
<section class="article-layout__main">
    <?php foreach ($text_arr as $key => $text) : ?>
        <?php if ($key !== 0) :?>
            <section class="article-layout__main__paragraph">
                <?php if (count($heading_arr) > $key) : ?>
                    <h2 class="article-layout__main__paragraph__heading"><?php echo $heading_arr[$key] ?></h2>
                <?php endif; ?>
                <p class="article-layout__main__paragraph__text"><?php echo $text ?></p>
            </section>
        <?php endif; ?>
    <?php endforeach; ?>
</section>
<section class="article-layout__slider">
    <div id="carouselExampleIndicators" class="news__carousel carousel slide fullscreen" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators">
            <?php foreach ($slider_image_url_arr as $key => $single_img_url) : ?>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="<?php echo $icCounter ?>" <?php if ( $icFirstElement === FALSE ) : ?>class="active" aria-current="true"<?php endif; ?> aria-label="Slide <?php echo $icCounter += 1; ?>"></button>
                <?php 
                    $icFirstElement = TRUE;
                ?>
            <?php endforeach; ?>
        </div>
        <div class="carousel-inner">
            <?php foreach ($slider_image_url_arr as $key => $single_img_url) : ?>
                <div class="carousel-item<?php if ( $firstElement === FALSE ) : ?><?php echo " active" ?><?php endif; ?>">
                    <picture>
                        <img src="<?php echo $single_img_url ?>" />
                    </picture>
                </div>
                <?php 
                    $firstElement = TRUE; 
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
</section>
<section class="article-layout__slider__addition">
    <h2 class="article-layout__slider__addition__heading"><?php echo $caption ?></h2>
    <p class="article-layout__slider__addition__text"><?php echo $alt ?></p>
</section>
