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

// Create some shortcuts.
$n          = count($this->items);
$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$langFilter = false;

// Tags filtering based on language filter
if (($this->params->get('filter_field') === 'tag') && (Multilanguage::isEnabled()))
{
	$tagfilter = ComponentHelper::getParams('com_tags')->get('tag_list_language_filter');

	switch ($tagfilter)
	{
		case 'current_language':
			$langFilter = Factory::getApplication()->getLanguage()->getTag();
			break;

		case 'all':
			$langFilter = false;
			break;

		default:
			$langFilter = $tagfilter;
	}
}

// Check for at least one editable article
$isEditable = false;

if (!empty($this->items))
{
	foreach ($this->items as $article)
	{
		if ($article->params->get('access-edit'))
		{
			$isEditable = true;
			break;
		}
	}
}

$currentDate = Factory::getDate()->format('Y-m-d H:i:s');
$ranNumber = rand(0, $n - 1)
?>

<h1 class="article-list__header"><?php echo $this -> escape( $this -> params -> get ( 'page_heading' ) ) ; ?></h1>
<section class="article-list__top-article">
	<?php 
		$bigArticle = $this -> items [ $ranNumber ];
	?>
	<?php if ( json_decode ( $bigArticle -> jcfields[4] -> rawvalue ) -> filename !== "" ): ?>
		<?php 
			$id = json_decode( $bigArticle -> jcfields[4] -> rawvalue ) -> itemId;
			$name = json_decode( $bigArticle -> jcfields[4] -> rawvalue ) -> filename;
			$bigBild = "images/econa/fields/4/com_content_article/{$id}/{$name}_L.jpg";
		?>
	<?php else: ?>
		<?php 
			$bigBild = "/images/placeholder/1575466871112.jfif";
		?>
	<?php endif; ?>
	<picture class="article-list__top-article__image">
		<img src="<?php echo $bigBild ?>" />
	</picture>
	<section class="article-list__top-article__content">
		<h2 class="heading1">
			<?php echo ( $bigArticle -> title ) ?>
		</h2>
		<p class="text1">
			<?php echo ( $bigArticle -> jcfields[3] -> rawvalue ) ?>
		</p>
	</section>
</section>
<h2 class="article-list__heading">Alle Neuigkeiten</h2>
<section class="flex article-list__list">
	<?php foreach ( $this -> items as $key => $article ) : ?>
		<?php if ( json_decode ( $article -> jcfields[4] -> rawvalue ) -> filename !== "" ): ?>
			<?php 
				$id = json_decode( $article -> jcfields[4] -> rawvalue ) -> itemId;
				$name = json_decode( $article -> jcfields[4] -> rawvalue ) -> filename;
				$bild = "images/econa/fields/4/com_content_article/{$id}/{$name}_L.jpg";
			?>
		<?php else: ?>
			<?php 
				$bild = "/images/placeholder/1575466871112.jfif";
			?>
		<?php endif; ?>
		<section class="article-list__small-article">
			<picture class="article-list__small-article__image">
				<img src="<?php echo $bild ?>" />
			</picture>
			<h2 class="heading2 article-list__small-article__heading">
				<?php if ( strlen ( $article -> title ) > 45 ) : ?>
					<?php echo substr( $article -> title, 0, 45 ); ?> ...
				<?php else : ?>
					<?php echo $article -> title ?>
				<?php endif ?>
			</h2>
		</section>
	<?php endforeach; ?>
</section>