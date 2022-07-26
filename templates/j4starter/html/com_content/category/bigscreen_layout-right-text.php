<?php
  $url_name = $_SERVER['SERVER_NAME']; 

  if ( $url_name !== "localhost" ) {
    $url_name = "{$url_name}/kevin";
  }

  $bgimg_id = json_decode($article->jcfields[4]->rawvalue)->itemId;
  $bgimg_name = json_decode($article->jcfields[4]->rawvalue)->filename;
  $bgimg = "images/econa/fields/4/com_content_article/{$bgimg_id}/{$bgimg_name}_L.jpg";
  $phimg = "images/placeholder/1575466871112.jfif";
  $personimg_id = json_decode($article->jcfields[17]->rawvalue)->itemId;
  $personimg_name = json_decode($article->jcfields[17]->rawvalue)->filename;
  $personimg = "images/econa/fields/17/com_content_article/{$personimg_id}/{$personimg_name}_L.jpg";
  $url_id = $article -> id;
  $cht = "qr";
  $chs = "500x500";
  $chl = urlencode("{$url_name}/index.php?option=com_content&view=article&view=article&id={$url_id}");
  $choe = "UTF-8";
  $qrcode = "https://chart.googleapis.com/chart?cht={$cht}&chs={$chs}&chl={$chl}&choe={$choe}";
?>

<img class="bigscreen__layoutwp__bgimg" src="<?php echo $bgimg ?>" alt="Slide <?php echo $key + 1 ?>">
<section class="flex bigscreen__footer width-100">
  <h1 class="bigscreen__footer__heading <?php echo( strlen( $article -> title ) <= 25 ) ? 'heading-2000' : 'heading-1500' ?>"><?php echo $article -> title ?></h1>
  <img class="bigscreen__footer__qrcode" src="<?php echo $qrcode ?>" alt="QR Code">
</section>
<section class="flex bigscreen__info bigscreen__info__right">
  <span class="bigscreen__info__article-text"><?php echo $article -> jcfields[3] -> rawvalue ?></span>
  <span class="bigscreen__info__qrcode-text">Für mehr Informationen QR-Code scannen</span>
</section>
