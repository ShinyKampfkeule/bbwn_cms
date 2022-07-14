<?php
  $url_name = $_SERVER['SERVER_NAME']; 

  if ( $url_name !== "localhost" ) {
    $url_name = "{$url_name}/kevin";
  } else {
    $url_name = "";
  }
  $bgimg_id = json_decode($article->jcfields[15]->rawvalue)->itemId;
  $bgimg_name = json_decode($article->jcfields[15]->rawvalue)->filename;
  $bgimg = "{$url_name}/images/econa/fields/15/com_content_article/{$bgimg_id}/{$bgimg_name}_L.jpg";
  $phimg = "{$url_name}/images/placeholder/1575466871112.jfif";
  $personimg_id = json_decode($article->jcfields[17]->rawvalue)->itemId;
  $personimg_name = json_decode($article->jcfields[17]->rawvalue)->filename;
  $personimg = "{$url_name}/images/econa/fields/17/com_content_article/{$personimg_id}/{$personimg_name}_L.jpg";
  $url_id = $article -> id;
  $cht = "qr";
  $chs = "300x300";
  $chl = urlencode("{$url_name}/index.php?option=com_content&view=article&view=article&id={$url_id}");
  $choe = "UTF-8";
  $qrcode = "https://chart.googleapis.com/chart?cht={$cht}&chs={$chs}&chl={$chl}&choe={$choe}";
?>

<?php dump($_SERVER) ?>
<img class="d-block w-100 bigscreen__layoutwp__bgimg" src="<?php echo $bgimg ?>" alt="Slide <?php echo $key + 1 ?>">
<img src="<?php echo $personimg ?>" alt="Person">
<h1><?php echo $article -> jcfields[16] -> rawvalue ?></h1>
<section>
  <span>Wir begrüßen am Campus des BBWN:</span>
  <span><?php echo $article -> jcfields[18] -> rawvalue ?></span>
  <img src="<?php echo $qrcode ?>" alt="QR Code">
</section>
