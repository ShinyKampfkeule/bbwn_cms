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
<iframe class="article-layout__main__video" src="<?php echo $videoLink ?>"></iframe>