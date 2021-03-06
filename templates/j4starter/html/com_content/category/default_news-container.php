<?php 
    $imageExists = TRUE; 
?>
<section class="flex article-list__list">
    <?php foreach ( $this -> items as $key => $article ) : ?>
        <?php if ($catEntry[0] === "alle" || $catEntry[0] === $article -> category_alias) : ?>
            <?php if ( json_decode ( $article -> jcfields[4] -> rawvalue ) -> filename !== "" ): ?>
                <?php 
                    $id = json_decode( $article -> jcfields[4] -> rawvalue ) -> itemId;
                    $name = json_decode( $article -> jcfields[4] -> rawvalue ) -> filename;
                    $bild = "images/econa/fields/4/com_content_article/{$id}/{$name}_L.jpg";
                    $imageExists = TRUE;
                ?>
            <?php else: ?>
                <?php
                    $imageExists = FALSE; 
                ?>
            <?php endif; ?>
            <?php
                $colorArray = array(array(223, 72, 7), array(0, 22, 36), array(0, 204, 0), array(255, 77, 77), array(46, 184, 184));
                $randomColour = array_rand($colorArray, 1);
                $r = $colorArray[$randomColour][0];
                $g = $colorArray[$randomColour][1];
                $b = $colorArray[$randomColour][2];
                $url_id = $article -> id;
                $chl = "index.php?option=com_content&view=article&id={$url_id}";
            ?>
            <section class="article-list__small-article" onClick="location.href='<?php echo $chl ?>'" style="background-color:rgba(<?php echo $r ?>, <?php echo $g ?>, <?php echo $b ?>)">
                <?php if ($imageExists) : ?>
                    <picture class="article-list__small-article__image">
                        <img src="<?php echo $bild ?>" />
                    </picture>
                <?php endif; ?>
                <h2 class="<?php echo ($imageExists) ? "article-list__small-article__heading" : "article-list__small-article__alternate__heading" ?>">
                    <?php if ( strlen ( $article -> title ) > 45 ) : ?>
                        <?php echo substr ( $article -> title, 0, 45 ); ?> ...
                    <?php else : ?>
                        <?php echo $article -> title ?>
                    <?php endif; ?>
                </h2>
                <?php if (!$imageExists) : ?>
                    <p class="article-list__small-article__alternate__text">
                        <?php echo substr ( $article -> jcfields [ 3 ] -> value, 0, 200 ) ?> ...
                    </p>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    <?php endforeach; ?>
</section>