<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<a href="http://localhost/index.php?option=com_content&view=article&id=7" class="about-us__heading heading-800"><?php echo $this -> about -> title ?></a>
<section class="about-us">
    <?php 
        $id = json_decode( $this -> about -> jcfields[12] -> rawvalue ) -> itemId;
        $name = json_decode( $this -> about -> jcfields[12] -> rawvalue ) -> filename;
        $bild8 = "images/econa/fields/12/com_content_article/{$id}/{$name}_L.jpg";
        $bild8 = "https://p620711.mittwaldserver.info/kevin/images/econa/fields/12/com_content_article/7/csm_srh_panorama_bearb_d489566360_L.webp?1657286723926"
    ?>
    <picture class="grid-element-1-1">
        <img class="about-us__image" src="<?php echo $bild8 ?>" />
    </picture>
    <p class="grid-element-1-2 text-200 about-us__text">
        <?php echo $this -> about -> jcfields[2] -> rawvalue;  ?>
    </p>
    <div class="grid-element-1-3 about-us__facts">
        <span class="heading-800 grid-element-1-1_2 about-us__keyhead">
            Unsere Keyfacts
        </span>
        <span class="heading2 about-us__fact grid-element-2-1">
            ca. 750 Mitarbeitende
        </span>
        <span class="heading2 about-us__fact grid-element-2-2">
            ca. 900 Teilnehmende
        </span>
        <span class="heading2 about-us__fact grid-element-3-1">
            120 Außenwohngruppen
        </span>
        <span class="heading2 about-us__fact grid-element-3-2">
            200 Teilnehmende in Internaten
        </span>
        <span class="heading-800 about-us__fact grid-element-4-1_2">
            über 40 Ausbildungsberufe
        </span>
    </div>
</section>
<iframe class="about-us__map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d6176.805787530712!2d8.798649029095495!3d49.389482726308636!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4797ebb7568021d1%3A0x437c258b6294c4ee!2sSRH%20Berufsbildungswerk%20Neckargem%C3%BCnd!5e0!3m2!1sde!2sde!4v1657368146701!5m2!1sde!2sde"style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>