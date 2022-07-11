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
<h2 class="event__title grid-element-1-1 heading2"><?php echo $this -> event -> title ?></h2>
<?php 
$id = json_decode( $this -> event -> jcfields[11] -> rawvalue ) -> itemId;
$name = json_decode( $this -> event -> jcfields[11] -> rawvalue ) -> filename;
$bild5 = "images/econa/fields/11/com_content_article/{$id}/{$name}_L.jpg";
?>
<picture class="event__picture grid-element-1-1">
<img class="layout2__image"  src="<?php echo $bild5 ?>" />
</picture>