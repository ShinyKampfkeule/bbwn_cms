<?php
	/**
	 * @package     Joomla.Site
	 * @subpackage  com_content
	 *
	 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
	 * @license     GNU General Public License version 2 or later; see LICENSE.txt
	 */

	defined('_JEXEC') or die;

	use Joomla\CMS\Factory;
	use Joomla\CMS\HTML\HTMLHelper;
	use Joomla\CMS\Language\Associations;
	use Joomla\CMS\Language\Text;
	use Joomla\CMS\Layout\FileLayout;
	use Joomla\CMS\Layout\LayoutHelper;
	use Joomla\CMS\Router\Route;
	use Joomla\CMS\Uri\Uri;
	use Joomla\Component\Content\Administrator\Extension\ContentComponent;
	use Joomla\Component\Content\Site\Helper\RouteHelper;

	// Create shortcuts to some parameters.
	$params   = $this->item->params;
	$canEdit  = $params->get('access-edit');
	$user     = Factory::getUser();
	$info     = $params->get('info_block_position', 0);
	$htag     = $this->params->get('show_page_heading') ? 'h2' : 'h1';
	$item 	  = $this->item;
	$jcfields = $item->jcfields;
	$layout   = $jcfields[6]->rawvalue[0];
	$main_img = json_decode( $jcfields[4] -> rawvalue );
	$main_img_id = $main_img -> itemId;
	$main_img_name = $main_img -> filename;
	$main_heading = $item -> title;
	$created = (new DateTime($item -> created))->format('d.m.Y H:m');
	$last_edit = (new DateTime($item -> modified))->format('d.m.Y H:m');
	$category = $item -> category_title;
	$author = $item -> author;

	if ($main_img_name !== "") {
		$main_img_src = "images/econa/fields/4/com_content_article/{$main_img_id}/{$main_img_name}_L.webp";
	} else {
		$main_img_src = "images/placeholder/1575466871112.jfif";
	}

	$text_arr     = explode("\r\n\r\n", $item -> jcfields[2] -> rawvalue);
	$heading_arr     = explode("\r\n", $item -> jcfields[22] -> rawvalue);
	$deleted_text = 0;
	$deleted_heading = 0;
	$slider_field_arr = [23, 24, 25, 26, 27];
	$slider_image_url_arr = [];
	$videoId = $item -> jcfields[5] -> rawvalue;
	$videoLink = "https://www.youtube.com/embed/{$videoId}";
	$img_rawvalue = json_decode( $jcfields[23] -> rawvalue );
	$caption = $img_rawvalue -> caption;
	$alt = $img_rawvalue -> alt;

	foreach ($slider_field_arr as $key) {
		$img = json_decode( $jcfields[$key] -> rawvalue );
		$img_id = $img -> itemId;
		$img_name = $img -> filename;
		if ($img_name !== "") {
			$img_url = "images/econa/fields/{$key}/com_content_article/{$img_id}/{$img_name}_L.webp";
			array_push($slider_image_url_arr, $img_url);
		}
	} 

	foreach ($text_arr as $key => $text) {
		if ($text === "") {
			array_splice($text_arr, $key - $deleted_text, 1);
			$deleted_text++;
		} else {
			$text_arr[$key] = str_replace('\r\n', '<br>', $text);
		}
	}

	foreach ($heading_arr as $key => $heading) {
		if ($heading === "") {
			array_splice($heading_arr, $key - $deleted_heading, 1);
			$deleted_heading++;
		}
	}

	// Check if associations are implemented. If they are, define the parameter.
	$assocParam        = (Associations::isEnabled() && $params->get('show_associations'));
	$currentDate       = Factory::getDate()->format('Y-m-d H:i:s');
	$isNotPublishedYet = $this->item->publish_up > $currentDate;
	$isExpired         = !is_null($this->item->publish_down) && $this->item->publish_down < $currentDate;
?>

<section class="article-layout flex">
	<h1 class="article-layout__heading"><?php echo $main_heading ?></h1>
	<?php include "default_{$layout}.php" ?>
	<section class="article-layout__infobox">
    <p>Kategorie: <?php echo $category ?></p>
    <p>Autor: <?php echo $author ?></p>
    <p>Erstellt am: <?php echo $created ?></p>
    <p>Zuletzt bearbeitet am: <?php echo $last_edit ?></p>
</section>
</section>