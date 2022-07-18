<section class="flex article-list__list">
    <?php foreach ( $this -> items as $key => $article ) : ?>
        <?php if ($catEntry[0] === "alle" || $catEntry[0] === $article -> category_alias) : ?>
            <?php if ( json_decode ( $article -> jcfields[4] -> rawvalue ) -> filename !== "" ): ?>
                <?php 
                    $id = json_decode( $article -> jcfields[4] -> rawvalue ) -> itemId;
                    $name = json_decode( $article -> jcfields[4] -> rawvalue ) -> filename;
                    $bild = "images/econa/fields/4/com_content_article/{$id}/{$name}_L.jpg";
                ?>
            <?php else: ?>
                <?php 
                    $bild = "images/placeholder/1575466871112.jfif";
                ?>
            <?php endif; ?>
            <?php 
                $url_name = $_SERVER['SERVER_NAME']; 
                if ( $url_name !== "localhost" ) {
                $url_name = "{$url_name}/kevin";
                }
                $url_id = $article -> id;
                $chl = urlencode("index.php?option=com_content&view=article&id={$url_id}");
            ?>
            <section class="article-list__small-article" onClick="location.href='<?php echo $chl ?>'">
                <picture class="article-list__small-article__image">
                    <img src="<?php echo $bild ?>" />
                </picture>
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