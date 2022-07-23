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
                $r = rand(0, 255);
                $g = rand(0, 255);
                $b = rand(0, 255);
                $url_id = $article -> id;
                $chl = "index.php?option=com_content&view=article&id={$url_id}";
            ?>
            <section class="article-list__small-article" onClick="location.href='<?php echo $chl ?>'" style="background-color:rgba(<?php echo $r ?>, <?php echo $g ?>, <?php echo $b ?>)">
                <?php if ($imageExists !== FALSE) : ?>
                    <picture class="article-list__small-article__image">
                        <img src="<?php echo $bild ?>" />
                    </picture>
                <?php endif; ?>
                <h2 class="heading-400 article-list__small-article__heading">
                    <?php if ( strlen ( $article -> title ) > 45 ) : ?>
                        <?php echo substr ( $article -> title, 0, 45 ); ?> ...
                    <?php else : ?>
                        <?php echo $article -> title ?>
                    <?php endif; ?>
                </h2>
            </section>
        <?php endif; ?>
    <?php endforeach; ?>
</section>