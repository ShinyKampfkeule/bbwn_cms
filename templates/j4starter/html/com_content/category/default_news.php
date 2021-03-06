<?php
	/**
	 * @package     Joomla.Site
	 * @subpackage  com_content
	 *
	 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
	 * @license     GNU General Public License version 2 or later; see LICENSE.txt
	 */

	defined('_JEXEC') or die;

	use Joomla\CMS\Component\ComponentHelper;
	use Joomla\CMS\Factory;
	use Joomla\CMS\HTML\HTMLHelper;
	use Joomla\CMS\Language\Associations;
	use Joomla\CMS\Language\Multilanguage;
	use Joomla\CMS\Language\Text;
	use Joomla\CMS\Router\Route;
	use Joomla\CMS\Uri\Uri;
	use Joomla\Component\Content\Administrator\Extension\ContentComponent;
	use Joomla\Component\Content\Site\Helper\AssociationHelper;
	use Joomla\Component\Content\Site\Helper\RouteHelper;

	/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
	$wa = $this->document->getWebAssetManager();
	$wa->useScript('com_content.articles-list');

	$catList = array();
	$n          = count($this->items);
	$ranNumber = rand(0, $n - 1);

	foreach ( $this -> items as $key => $article ) {
		$category = array($article -> category_alias, $article -> category_title);
		if (in_array($category, $catList)) {
		} else {
			array_push($catList, $category);
		}
	}
?>
<?php 
	$bigArticle = $this -> items [ $ranNumber ];
	$url_name = $_SERVER['SERVER_NAME']; 
	if ( $url_name !== "localhost" ) {
	$url_name = "{$url_name}/kevin";
	}
	$url_id = $bigArticle -> id;
	$chl = "index.php?option=com_content&view=article&id={$url_id}";
?>

<section class="article-list__nav__fixed flex">
	<h1 class="article-list__nav__header"><?php echo $this -> escape( $this -> params -> get ( 'page_heading' ) ) ; ?></h1>	
	<section class="article-list__nav__container flex">
	<ul class="article-list__nav nav" id="myTab" role="tablist">
		<li class="nav-item" role="presentation">
			<button class="nav-link active article-list__nav__link" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">Alle</button>
		</li>
		<?php foreach ($catList as $catKey => $catEntry) : ?>
			<li class="nav-item" role="presentation">
				<button class="nav-link article-list__nav__link" id="<?php echo $catEntry[0] ?>-tab" data-bs-toggle="tab" data-bs-target="#<?php echo $catEntry[0] ?>" type="button" role="tab" aria-controls="<?php echo $catEntry[0] ?>" aria-selected="false"><?php echo $catEntry[1] ?></button>
			</li>	
		<?php endforeach; ?>
	</ul>
</section>
</section>
<?php if ( json_decode ( $bigArticle -> jcfields[4] -> rawvalue ) -> filename !== "" ): ?>
		<?php 
			$id = json_decode( $bigArticle -> jcfields[4] -> rawvalue ) -> itemId;
			$name = json_decode( $bigArticle -> jcfields[4] -> rawvalue ) -> filename;
			$bigBild = "images/econa/fields/4/com_content_article/{$id}/{$name}_L.jpg";
		?>
	<?php else: ?>
		<?php
			$colorArray = array(array(223, 72, 7), array(0, 22, 36), array(0, 204, 0), array(255, 77, 77), array(46, 184, 184));
			$randomColour = array_rand($colorArray, 1);
			$r = $colorArray[$randomColour][0];
			$g = $colorArray[$randomColour][1];
			$b = $colorArray[$randomColour][2]; 
		?>
	<?php endif; ?>
<section class="article-list__top-article" onClick="location.href='<?php echo $chl ?>'" style="background-color:rgba(<?php echo $r ?>, <?php echo $g ?>, <?php echo $b ?>)">
	<picture class="article-list__top-article__image">
		<img src="<?php echo $bigBild ?>" />
	</picture>
	<section class="article-list__top-article__content<?php echo ( json_decode ( $bigArticle -> jcfields[4] -> rawvalue ) -> filename !== '' ) ? '' : '__alternate' ?>">
		<h2 class="article-list__top-article__content__heading<?php echo ( json_decode ( $bigArticle -> jcfields[4] -> rawvalue ) -> filename !== '' ) ? '' : '-alternate' ?>">
			<?php echo ( $bigArticle -> title ) ?>
		</h2>
		<p class="article-list__top-article__content__text<?php echo ( json_decode ( $bigArticle -> jcfields[4] -> rawvalue ) -> filename !== '' ) ? '' : '-alternate' ?>">
			<?php if ( strlen ( $bigArticle -> jcfields[3] -> rawvalue ) > 390 ) : ?>
				<?php echo substr ( $bigArticle -> jcfields[3] -> rawvalue, 0, 390 ); ?> ...
			<?php else : ?>
				<?php echo $bigArticle -> jcfields[3] -> rawvalue ?>
			<?php endif ?>
		</p>
	</section>
</section>
<div class="tab-content article-list__tab-content" id="myTabContent">
	<div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
		<?php 
			$catEntry = array("alle", "Alle");
			include "default_news-container.php"; 
		?>
	</div>
	<?php foreach ($catList as $catKey => $catEntry) : ?>
		<div class="tab-pane fade show" id="<?php echo $catEntry[0]?>" role="tabpanel" aria-labelledby="<?php echo $catEntry[0] ?>-tab">
			<?php include "default_news-container.php" ?>
		</div>
	<?php endforeach; ?>
</div>