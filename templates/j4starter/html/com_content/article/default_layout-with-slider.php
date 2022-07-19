<picture>
    <img src="<?php echo $main_img_src ?>" />
</picture>
<?php foreach ($text_arr as $key => $text) : ?>
    <h2><?php echo $heading_arr[$key] ?></h2>
    <p><?php echo $text ?></p>
<?php endforeach; ?>
<?php foreach ($slider_image_url_arr as $key => $single_img_url) : ?>
    <picture>
        <img src="<?php echo $single_img_url ?>" />
    </picture>
<?php endforeach; ?>