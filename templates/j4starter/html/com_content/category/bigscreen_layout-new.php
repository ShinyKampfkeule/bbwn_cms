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
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 798.2 168" class="bigscreen__logo bigscreen__logo__right">
                <path fill="white" d="M141.5,136.5h20V.5h-20Zm44.1-91.9a75.55,75.55,0,0,0-13.1.6V63.9c17.6,0,24,2.6,24,19.1v53.5h20v-56c0-20.7-11.6-35.2-30.9-35.9Zm-137.7,1C26.5,46.7,17,60.9,17,83.6v45.8c0,13.3-2.7,19.1-16.5,19.4v18.5a27.88,27.88,0,0,0,5.7.2c21.5-1.3,30.9-15.7,30.9-38.1V83.6c0-13.4,2.5-19.1,16.4-19.4V45.7a35.31,35.31,0,0,0-5.6-.1Zm62,0C89,46.7,79.5,61,79.5,83.6v53H99.3v-53c0-13.3,2.6-19,16.2-19.3V45.7A35.49,35.49,0,0,0,109.9,45.6Z"></path>
                <path fill="white" d="M 313.15 63.1 q 6.92 1.65 6.92 8.67 a 8.79 8.79 0 0 1 -3.64 7.51 q -3.65 2.66 -10.76 2.65 H 291.58 V 45.81 h 13.73 q 6.45 0 9.7 2.45 a 8.3 8.3 0 0 1 3.25 7.05 a 8.2 8.2 0 0 1 -1.37 4.77 A 8.55 8.55 0 0 1 313.15 63.1 Z M 305 52.52 h -5.62 v 7.79 h 5.72 q 5.16 0 5.16 -3.87 T 305 52.52 Z m 1.55 14.24 h -7.17 v 8.47 h 6.76 a 7 7 0 0 0 4.36 -1.14 A 3.7 3.7 0 0 0 312 71 a 3.84 3.84 0 0 0 -1.35 -3.1 A 6.16 6.16 0 0 0 306.55 66.76 Z m 30.6 16 A 14.31 14.31 0 0 1 329.92 81 a 13.37 13.37 0 0 1 -5 -5 a 14.11 14.11 0 0 1 -1.83 -7.19 a 14 14 0 0 1 20.79 -12 a 12.33 12.33 0 0 1 4.67 4.78 a 13.8 13.8 0 0 1 1.68 6.83 V 71.1 H 330.7 A 6.42 6.42 0 0 0 333 75 a 7.66 7.66 0 0 0 7.85 0.57 a 7.64 7.64 0 0 0 2.71 -2.74 l 5.88 3.82 a 12.85 12.85 0 0 1 -4.93 4.57 A 15.72 15.72 0 0 1 337.15 82.81 Z m -0.31 -21.41 a 5.91 5.91 0 0 0 -3.77 1.21 a 6.68 6.68 0 0 0 -2.22 3.28 h 11.77 a 5.59 5.59 0 0 0 -5.78 -4.49 Z m 17 20.53 V 67.07 q 0 -5.77 3.28 -8.72 a 12.22 12.22 0 0 1 8.49 -2.94 a 17.8 17.8 0 0 1 2.06 0.13 a 16 16 0 0 1 1.86 0.34 v 6.5 c -0.41 -0.07 -0.85 -0.12 -1.32 -0.16 s -0.95 0 -1.47 0 a 5.82 5.82 0 0 0 -4 1.39 a 5 5 0 0 0 -1.52 3.82 V 81.93 Z m 30.14 0.88 q -5.73 0 -8.93 -3.1 t -3.2 -8.77 V 55.88 h 7.33 v 15 a 5.32 5.32 0 0 0 1.31 3.87 A 4.69 4.69 0 0 0 384 76.05 a 4.51 4.51 0 0 0 3.4 -1.34 a 5.35 5.35 0 0 0 1.29 -3.87 v -15 H 396 V 70.94 q 0 5.69 -3.17 8.77 T 384 82.81 Z m 16.41 -0.88 V 54.38 q 0 -4.91 2.65 -7.61 a 9.61 9.61 0 0 1 7.2 -2.71 a 16.48 16.48 0 0 1 2.37 0.15 c 0.69 0.11 1.26 0.21 1.71 0.31 v 6.09 a 7.61 7.61 0 0 0 -1.14 -0.13 l -1.18 0 a 4.54 4.54 0 0 0 -3.26 1 a 4.39 4.39 0 0 0 -1 3.23 v 1.19 h 6.5 v 6.19 h -6.5 V 81.93 Z m 26.78 0.88 a 16.55 16.55 0 0 1 -6.94 -1.44 a 10.4 10.4 0 0 1 -4.67 -3.82 l 5.05 -4.08 a 8.5 8.5 0 0 0 6.66 3.25 a 5.1 5.1 0 0 0 2.79 -0.64 a 2 2 0 0 0 1 -1.78 c 0 -1.1 -0.84 -1.77 -2.52 -2 l -3.72 -0.56 q -8.25 -1.25 -8.25 -8.21 a 7.43 7.43 0 0 1 3 -6.19 a 12.68 12.68 0 0 1 8 -2.32 a 13.53 13.53 0 0 1 5.8 1.29 a 11.14 11.14 0 0 1 4.36 3.46 l -4.8 3.92 a 6.72 6.72 0 0 0 -5.36 -2.58 a 5 5 0 0 0 -2.58 0.59 a 1.82 1.82 0 0 0 -1 1.63 c 0 1.17 0.79 1.87 2.37 2.11 l 3.3 0.47 q 8.77 1.23 8.77 7.94 a 8 8 0 0 1 -3 6.61 Q 432.41 82.81 427.14 82.81 Z m 28.79 0 a 14.48 14.48 0 0 1 -7.2 -1.73 A 12 12 0 0 1 444 76.23 a 15.06 15.06 0 0 1 -1.71 -7.35 V 44.52 h 7.33 V 58.4 a 9.29 9.29 0 0 1 7.59 -3.4 a 11.62 11.62 0 0 1 6.24 1.75 A 12.81 12.81 0 0 1 468 61.63 a 14.85 14.85 0 0 1 1.7 7.25 a 14.65 14.65 0 0 1 -1.75 7.27 a 12.64 12.64 0 0 1 -4.82 4.91 A 14.16 14.16 0 0 1 455.93 82.81 Z m -6.35 -13.62 a 6.82 6.82 0 0 0 0.83 3.35 a 6.41 6.41 0 0 0 2.24 2.38 a 6 6 0 0 0 3.18 0.87 a 5.87 5.87 0 0 0 4.56 -2 a 7.06 7.06 0 0 0 1.78 -5 a 6.94 6.94 0 0 0 -1.78 -4.95 a 6.29 6.29 0 0 0 -9 0 a 6.47 6.47 0 0 0 -1.81 4.67 Z M 476.93 53.3 a 4.37 4.37 0 0 1 -3.2 -1.27 a 4.42 4.42 0 0 1 0 -6.24 a 4.67 4.67 0 0 1 6.4 0 a 4.42 4.42 0 0 1 0 6.24 A 4.37 4.37 0 0 1 476.93 53.3 Z m -3.66 28.63 V 55.88 h 7.32 V 81.93 Z m 20.22 0.36 a 8.11 8.11 0 0 1 -6 -2.24 a 8.68 8.68 0 0 1 -2.24 -6.42 V 44.52 h 7.32 V 73.11 a 2.93 2.93 0 0 0 0.7 2.17 a 2.78 2.78 0 0 0 2 0.67 l 0.8 0 a 7.89 7.89 0 0 0 0.85 -0.08 v 6.09 a 14.47 14.47 0 0 1 -1.65 0.29 C 494.76 82.27 494.18 82.29 493.49 82.29 Z m 18.94 0.52 a 14.18 14.18 0 0 1 -7.12 -1.75 a 12.51 12.51 0 0 1 -4.85 -4.91 a 14.65 14.65 0 0 1 -1.76 -7.27 a 14.85 14.85 0 0 1 1.71 -7.25 A 12.7 12.7 0 0 1 505 56.75 A 11.68 11.68 0 0 1 511.19 55 a 9.29 9.29 0 0 1 7.59 3.4 V 44.52 h 7.32 V 68.88 a 15.06 15.06 0 0 1 -1.7 7.35 a 12.16 12.16 0 0 1 -4.77 4.85 A 14.48 14.48 0 0 1 512.43 82.81 Z m -6.24 -13.93 a 7.1 7.1 0 0 0 1.77 5 a 5.89 5.89 0 0 0 4.57 2 a 6 6 0 0 0 3.18 -0.87 A 6.41 6.41 0 0 0 518 72.54 a 6.82 6.82 0 0 0 0.83 -3.35 v -0.62 A 6.51 6.51 0 0 0 517 63.9 a 6.3 6.3 0 0 0 -9 0 A 7 7 0 0 0 506.19 68.88 Z m 36.17 13.93 q -5.73 0 -8.93 -3.1 t -3.2 -8.77 V 55.88 h 7.33 v 15 a 5.32 5.32 0 0 0 1.31 3.87 a 4.69 4.69 0 0 0 3.49 1.34 a 4.51 4.51 0 0 0 3.4 -1.34 a 5.35 5.35 0 0 0 1.29 -3.87 v -15 h 7.33 V 70.94 q 0 5.69 -3.17 8.77 T 542.36 82.81 Z m 16.15 -0.88 V 66.87 q 0 -5.69 3.22 -8.78 T 570.68 55 q 5.73 0 9 3.09 t 3.22 8.78 V 81.93 h -7.32 V 67 a 5.23 5.23 0 0 0 -1.35 -3.84 a 5.17 5.17 0 0 0 -7 0 A 5.23 5.23 0 0 0 565.83 67 v 15 Z m 55.11 -13 V 79.1 q 0 7.37 -3.51 11.3 t -10.22 3.92 a 15.7 15.7 0 0 1 -7.25 -1.65 a 11.66 11.66 0 0 1 -4.88 -4.44 l 5.47 -4 a 9.17 9.17 0 0 0 2.89 2.58 a 7.69 7.69 0 0 0 3.77 0.88 q 6.4 0 6.4 -7.12 V 78.73 c -1.76 2.14 -4.28 3.2 -7.59 3.2 a 12 12 0 0 1 -10.86 -6.39 A 15.26 15.26 0 0 1 588 61.6 a 12.57 12.57 0 0 1 4.88 -4.85 A 14.2 14.2 0 0 1 599.94 55 a 14.48 14.48 0 0 1 7.2 1.73 a 12 12 0 0 1 4.77 4.85 A 15.06 15.06 0 0 1 613.62 68.93 Z m -19.92 -0.21 a 6.46 6.46 0 0 0 1.75 4.67 a 6.53 6.53 0 0 0 9.06 0 a 6.16 6.16 0 0 0 1.78 -4.49 v -0.31 a 6.82 6.82 0 0 0 -0.83 -3.35 a 6.41 6.41 0 0 0 -2.24 -2.38 a 6.35 6.35 0 0 0 -7.72 1 A 6.7 6.7 0 0 0 593.7 68.72 Z m 34.57 14.09 a 16.58 16.58 0 0 1 -6.94 -1.44 a 10.46 10.46 0 0 1 -4.67 -3.82 l 5.06 -4.08 a 8.46 8.46 0 0 0 6.65 3.25 a 5.08 5.08 0 0 0 2.79 -0.64 a 2 2 0 0 0 1 -1.78 c 0 -1.1 -0.84 -1.77 -2.53 -2 l -3.71 -0.56 q -8.26 -1.25 -8.26 -8.21 a 7.45 7.45 0 0 1 3 -6.19 a 12.7 12.7 0 0 1 8 -2.32 a 13.6 13.6 0 0 1 5.81 1.29 a 11.14 11.14 0 0 1 4.36 3.46 L 634 63.67 a 6.76 6.76 0 0 0 -5.37 -2.58 a 5 5 0 0 0 -2.58 0.59 a 1.84 1.84 0 0 0 -1 1.63 c 0 1.17 0.79 1.87 2.37 2.11 l 3.31 0.47 q 8.76 1.23 8.77 7.94 a 8 8 0 0 1 -3 6.61 Q 633.53 82.81 628.27 82.81 Z m 21 -0.88 l -8.41 -26.05 h 8 l 4.59 17.28 l 5.78 -17.28 h 7.07 l 5.88 17.23 l 4.64 -17.23 h 7.74 l -8.46 26.05 h -7.53 l -5.83 -17.64 l -5.94 17.64 Z m 49.64 0.88 A 14.31 14.31 0 0 1 691.63 81 a 13.37 13.37 0 0 1 -5 -5 a 14.11 14.11 0 0 1 -1.83 -7.19 a 14 14 0 0 1 20.8 -12 a 12.47 12.47 0 0 1 4.67 4.78 a 13.9 13.9 0 0 1 1.67 6.83 V 71.1 h -19.5 A 6.42 6.42 0 0 0 694.7 75 a 7.66 7.66 0 0 0 7.85 0.57 a 7.64 7.64 0 0 0 2.71 -2.74 l 5.88 3.82 a 12.78 12.78 0 0 1 -4.93 4.57 A 15.72 15.72 0 0 1 698.86 82.81 Z m -0.31 -21.41 a 5.91 5.91 0 0 0 -3.77 1.21 a 6.68 6.68 0 0 0 -2.22 3.28 h 11.77 a 5.59 5.59 0 0 0 -5.78 -4.49 Z m 17 20.53 V 67.07 c 0 -3.85 1.1 -6.76 3.28 -8.72 a 12.22 12.22 0 0 1 8.49 -2.94 a 17.8 17.8 0 0 1 2.06 0.13 a 16 16 0 0 1 1.86 0.34 v 6.5 c -0.41 -0.07 -0.85 -0.12 -1.32 -0.16 s -0.95 0 -1.47 0 a 5.82 5.82 0 0 0 -4 1.39 a 5 5 0 0 0 -1.52 3.82 V 81.93 Z m 18.27 0 V 44.52 h 7.33 V 64.6 h 0.82 a 10.27 10.27 0 0 0 4.11 -0.67 a 4.79 4.79 0 0 0 2.27 -2.2 a 14.52 14.52 0 0 0 1.26 -4 l 0.36 -1.85 h 7.43 l -0.31 2 A 24.12 24.12 0 0 1 755.33 64 a 11.18 11.18 0 0 1 -3.12 4.18 l 6.92 13.72 h -8.47 l -5.16 -11 a 25.16 25.16 0 0 1 -3.81 0.26 h -0.57 V 81.93 Z m -442.21 54.6 V 100.41 h 8.21 l 16.46 24.46 V 100.41 h 7.64 v 36.12 H 316 l -16.77 -23.89 v 23.89 Z m 51.19 0.88 a 14.27 14.27 0 0 1 -7.22 -1.83 a 13.3 13.3 0 0 1 -5 -5 a 14 14 0 0 1 -1.84 -7.19 a 13.66 13.66 0 0 1 6.74 -11.92 a 13.49 13.49 0 0 1 7 -1.86 a 13.65 13.65 0 0 1 7 1.75 a 12.47 12.47 0 0 1 4.67 4.78 a 13.8 13.8 0 0 1 1.68 6.83 v 2.74 H 336.32 a 6.39 6.39 0 0 0 2.3 3.92 a 7.64 7.64 0 0 0 7.84 0.57 a 7.48 7.48 0 0 0 2.71 -2.74 l 5.88 3.82 a 12.71 12.71 0 0 1 -4.93 4.57 A 15.72 15.72 0 0 1 342.77 137.41 Z M 342.46 116 a 5.93 5.93 0 0 0 -3.77 1.21 a 6.6 6.6 0 0 0 -2.21 3.28 h 11.76 a 5.71 5.71 0 0 0 -2 -3.25 A 5.77 5.77 0 0 0 342.46 116 Z m 30.08 21.41 a 14.08 14.08 0 0 1 -7.12 -1.8 a 13.15 13.15 0 0 1 -4.95 -5 a 14.26 14.26 0 0 1 -1.81 -7.15 a 13.81 13.81 0 0 1 1.84 -7.12 a 13.44 13.44 0 0 1 5 -5 a 14 14 0 0 1 7.07 -1.8 a 14.14 14.14 0 0 1 6.71 1.6 a 12.34 12.34 0 0 1 4.85 4.49 l -6 3.92 a 6.32 6.32 0 0 0 -5.52 -3 a 6.09 6.09 0 0 0 -4.65 1.91 a 6.91 6.91 0 0 0 -1.8 5 a 7 7 0 0 0 1.8 5 a 6.07 6.07 0 0 0 4.65 1.93 a 6.18 6.18 0 0 0 5.52 -3.14 l 6 3.92 a 12.17 12.17 0 0 1 -4.77 4.62 A 14.22 14.22 0 0 1 372.54 137.41 Z m 14.5 -0.88 V 99.12 h 7.33 V 119.2 h 0.83 a 10.25 10.25 0 0 0 4.1 -0.67 a 4.79 4.79 0 0 0 2.27 -2.2 a 14.52 14.52 0 0 0 1.26 -4 l 0.36 -1.85 h 7.43 l -0.31 2 a 24.25 24.25 0 0 1 -1.72 6.19 a 11.31 11.31 0 0 1 -3.13 4.18 l 6.92 13.72 h -8.46 l -5.16 -11 a 25.3 25.3 0 0 1 -3.82 0.26 h -0.57 v 10.73 Z m 38 0.88 a 11.68 11.68 0 0 1 -6.22 -1.75 a 12.65 12.65 0 0 1 -4.56 -4.91 a 14.85 14.85 0 0 1 -1.71 -7.22 a 14.64 14.64 0 0 1 1.76 -7.3 a 12.57 12.57 0 0 1 4.85 -4.88 a 14.18 14.18 0 0 1 7.12 -1.75 a 14.37 14.37 0 0 1 7.2 1.73 a 12.16 12.16 0 0 1 4.77 4.85 a 15.06 15.06 0 0 1 1.7 7.35 v 13 h -6.09 l -0.15 -4.07 a 9.66 9.66 0 0 1 -3.54 3.66 A 9.92 9.92 0 0 1 425 137.41 Z m -5 -13.88 a 7 7 0 0 0 1.78 4.93 a 6.28 6.28 0 0 0 9 0 a 6.51 6.51 0 0 0 1.81 -4.67 v -0.62 a 6.82 6.82 0 0 0 -0.83 -3.35 a 6.41 6.41 0 0 0 -2.24 -2.38 a 6.32 6.32 0 0 0 -7.75 1.06 A 7.1 7.1 0 0 0 420 123.53 Z m 24.05 13 V 121.67 q 0 -5.78 3.28 -8.72 a 12.22 12.22 0 0 1 8.49 -2.94 a 16.8 16.8 0 0 1 3.92 0.47 V 117 c -0.42 -0.07 -0.85 -0.12 -1.32 -0.16 s -0.95 0 -1.47 0 a 5.79 5.79 0 0 0 -4 1.39 a 4.91 4.91 0 0 0 -1.52 3.82 v 14.55 Z m 44.12 -13 V 133.7 q 0 7.38 -3.51 11.3 t -10.22 3.92 a 15.7 15.7 0 0 1 -7.25 -1.65 a 11.64 11.64 0 0 1 -4.87 -4.44 l 5.47 -4 a 9.06 9.06 0 0 0 2.89 2.58 a 7.63 7.63 0 0 0 3.76 0.88 q 6.41 0 6.4 -7.12 v -1.81 c -1.75 2.14 -4.28 3.2 -7.58 3.2 a 12 12 0 0 1 -10.87 -6.39 a 15.26 15.26 0 0 1 0.13 -13.94 a 12.64 12.64 0 0 1 4.88 -4.85 a 15.54 15.54 0 0 1 14.29 0 a 12.13 12.13 0 0 1 4.78 4.85 A 15.17 15.17 0 0 1 488.18 123.53 Z m -19.92 -0.21 A 6.46 6.46 0 0 0 470 128 a 6.52 6.52 0 0 0 9.05 0 a 6.12 6.12 0 0 0 1.78 -4.49 v -0.31 a 6.81 6.81 0 0 0 -0.82 -3.35 a 6.35 6.35 0 0 0 -2.25 -2.38 a 6.34 6.34 0 0 0 -7.71 1 A 6.66 6.66 0 0 0 468.26 123.32 Z m 37.36 14.09 a 14.34 14.34 0 0 1 -7.23 -1.83 a 13.37 13.37 0 0 1 -5 -5 a 14.11 14.11 0 0 1 -1.83 -7.19 a 14 14 0 0 1 20.8 -12 a 12.47 12.47 0 0 1 4.67 4.78 a 13.9 13.9 0 0 1 1.67 6.83 v 2.74 h -19.5 a 6.42 6.42 0 0 0 2.29 3.92 a 7.66 7.66 0 0 0 7.85 0.57 a 7.48 7.48 0 0 0 2.71 -2.74 l 5.88 3.82 a 12.78 12.78 0 0 1 -4.93 4.57 A 15.72 15.72 0 0 1 505.62 137.41 Z M 505.31 116 a 5.91 5.91 0 0 0 -3.77 1.21 a 6.68 6.68 0 0 0 -2.22 3.28 h 11.77 a 5.59 5.59 0 0 0 -5.78 -4.49 Z m 17 20.53 V 121.47 c 0 -3.79 1 -6.71 3.12 -8.78 s 5 -3.09 8.64 -3.09 a 11.52 11.52 0 0 1 5 1 a 10.59 10.59 0 0 1 3.74 2.94 a 11.13 11.13 0 0 1 3.79 -2.94 a 11.68 11.68 0 0 1 5 -1 c 3.69 0 6.55 1 8.6 3.09 s 3.07 5 3.07 8.78 v 15.06 h -7.33 v -15 a 5.3 5.3 0 0 0 -1.32 -3.84 a 4.56 4.56 0 0 0 -3.48 -1.37 a 4.45 4.45 0 0 0 -3.38 1.37 a 5.3 5.3 0 0 0 -1.32 3.84 v 15 h -7.32 v -15 a 5.3 5.3 0 0 0 -1.32 -3.84 a 4.59 4.59 0 0 0 -3.48 -1.37 a 4.45 4.45 0 0 0 -3.38 1.37 a 5.3 5.3 0 0 0 -1.32 3.84 v 15 Z m 57.22 0.88 q -5.73 0 -8.93 -3.09 c -2.13 -2.07 -3.2 -5 -3.2 -8.78 V 110.48 h 7.33 v 15 a 5.28 5.28 0 0 0 1.32 3.87 a 4.61 4.61 0 0 0 3.48 1.34 a 4.51 4.51 0 0 0 3.41 -1.34 a 5.4 5.4 0 0 0 1.29 -3.87 v -15 h 7.32 v 15.06 c 0 3.79 -1.06 6.71 -3.17 8.78 S 583.29 137.41 579.51 137.41 Z m -6.19 -29.72 a 4.25 4.25 0 0 1 -3.15 -1.24 a 4.36 4.36 0 0 1 0 -6.09 a 4.58 4.58 0 0 1 6.27 0 a 4.31 4.31 0 0 1 0 6.09 A 4.26 4.26 0 0 1 573.32 107.69 Z m 12.28 0 a 4.27 4.27 0 0 1 -3.15 -1.24 a 4.36 4.36 0 0 1 0 -6.09 a 4.58 4.58 0 0 1 6.27 0 a 4.31 4.31 0 0 1 0 6.09 A 4.26 4.26 0 0 1 585.6 107.69 Z m 10.06 28.84 V 121.47 q 0 -5.68 3.22 -8.78 t 9 -3.09 q 5.73 0 8.95 3.09 t 3.22 8.78 v 15.06 h -7.32 v -15 a 5.26 5.26 0 0 0 -1.34 -3.84 a 5.18 5.18 0 0 0 -7 0 a 5.22 5.22 0 0 0 -1.34 3.84 v 15 Z m 41.43 0.88 a 14.21 14.21 0 0 1 -7.12 -1.75 a 12.58 12.58 0 0 1 -4.85 -4.91 a 14.65 14.65 0 0 1 -1.75 -7.27 a 15 15 0 0 1 1.7 -7.25 a 12.8 12.8 0 0 1 4.57 -4.88 a 11.68 11.68 0 0 1 6.22 -1.75 a 9.28 9.28 0 0 1 7.58 3.4 V 99.12 h 7.33 v 24.36 a 15.06 15.06 0 0 1 -1.71 7.35 a 12 12 0 0 1 -4.77 4.85 A 14.42 14.42 0 0 1 637.09 137.41 Z m -6.24 -13.93 a 7.06 7.06 0 0 0 1.78 5 a 6.27 6.27 0 0 0 7.74 1.09 a 6.43 6.43 0 0 0 2.25 -2.38 a 6.79 6.79 0 0 0 0.82 -3.35 v -0.62 a 6.47 6.47 0 0 0 -1.81 -4.67 a 5.87 5.87 0 0 0 -4.43 -1.88 a 6 6 0 0 0 -4.57 1.91 A 6.94 6.94 0 0 0 630.85 123.48 Z"></path>
            </svg>
<section class="flex bigscreen__footer width-100">
  <section class="bigscreen__footer__heading">
    <h1><?php echo $article -> title ?></h1>
  </section>
  <span class="bigscreen__footer__article-text">
    <?php echo substr($article -> jcfields[3] -> rawvalue, 0, 345) ?>
    <?php if (strlen($article -> jcfields[3] -> rawvalue) > 345) : ?>
      ...
    <?php endif; ?>
  </span>
  <section class="bigscreen__footer__qrcode flex">
    <span class="bigscreen__footer__qrcode__text">Für mehr Informationen QR-Code scannen</span>
    <img class="bigscreen__footer__qrcode__image" src="<?php echo $qrcode ?>" alt="QR Code">
  </section>
</section>
