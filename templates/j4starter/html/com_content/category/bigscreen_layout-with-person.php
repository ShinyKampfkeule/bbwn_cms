<?php
  $url_name = $_SERVER['SERVER_NAME']; 

  if ( $url_name !== "localhost" ) {
    $url_name = "{$url_name}/kevin";
  }

  $bgimg_id = json_decode($article->jcfields[15]->rawvalue)->itemId;
  $bgimg_name = json_decode($article->jcfields[15]->rawvalue)->filename;
  $bgimg = "images/econa/fields/15/com_content_article/{$bgimg_id}/{$bgimg_name}_L.jpg";
  $phimg = "images/placeholder/1575466871112.jfif";
  $personimg_id = json_decode($article->jcfields[17]->rawvalue)->itemId;
  $personimg_name = json_decode($article->jcfields[17]->rawvalue)->filename;
  $personimg = "images/econa/fields/17/com_content_article/{$personimg_id}/{$personimg_name}_L.jpg";
  $url_id = $article -> id;
  $cht = "qr";
  $chs = "300x300";
  $chl = urlencode("{$url_name}/index.php?option=com_content&view=article&view=article&id={$url_id}");
  $choe = "UTF-8";
  $qrcode = "https://chart.googleapis.com/chart?cht={$cht}&chs={$chs}&chl={$chl}&choe={$choe}";
?>

<img class="d-block w-100 bigscreen__layoutwp__bgimg" src="<?php echo $bgimg ?>" alt="Slide <?php echo $key + 1 ?>">

<section class="flex bigscreen__footer width-100">
  <img class="bigscreen__footer__image" src="<?php echo $personimg ?>" alt="Person">
  <span class="bigscreen__footer__welcome">Wir begrüßen am Campus des BBWN:</span>
  <h1 class="bigscreen__footer__name"><?php echo $article -> jcfields[16] -> rawvalue ?></h1>
  <img class="bigscreen__footer__qrcode" src="<?php echo $qrcode ?>" alt="QR Code">
</section>
<section>
  <span><?php echo $article -> jcfields[18] -> rawvalue ?></span>
</section>
