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

$currentDate = Factory::getDate()->format('Y-m-d H:i:s');

$raw_events = $this -> items;
$raw_dates = [$currentDate];
$sorted_events = [];

foreach ($raw_events as $key => $raw_event) {
	$raw_date = $raw_event -> jcfields[8] -> value;
	array_push($raw_dates, $raw_date);
}

asort($raw_dates);

foreach ($raw_dates as $key => $raw_date) {
	if ($raw_date !== $currentDate) {
		foreach ($raw_events as $key => $raw_event) {
			if ($raw_event -> jcfields[8] -> value === $raw_date) {
				array_push($sorted_events, $raw_event);
				break;
			}
		}
	}
}
?>
<h1 class="event-cards__header"><?php echo $this -> escape( $this -> params -> get ( 'page_heading' ) ) ; ?></h1>
<section class="flex event-cards">
	<?php foreach ($sorted_events as $key => $event) : ?>
		<?php
			$date = $event -> jcfields[8] -> value;
			$name = $event -> jcfields[7] -> rawvalue;
			$text = $event -> jcfields[3] -> rawvalue;
			$place = $event -> jcfields[9] -> rawvalue;
			$page = $event -> jcfields[10] -> rawvalue;
			$img_id = json_decode($event -> jcfields[4] -> rawvalue) -> itemId;
			$img_name = json_decode($event -> jcfields[4] -> rawvalue) -> filename;
			$img_path = "images/econa/fields/4/com_content_article/{$img_id}/{$img_name}_L.jpg";
		?>
		<div class="card" style="width: 18rem;">
		<img src="<?php echo $img_path ?>" class="card-img-top" alt="Event Bild">
		<div class="card-body">
			<h5 class="card-title"><?php echo $name ?></h5>
			<p class="card-text"><?php echo $text ?><br /><br /><?php echo $date ?><br /><?php echo $place ?></p>
			<?php if ($page !== "") : ?>
				<a href="$page" class="card-link">Eventseite besuchen</a>
			<?php endif; ?>
		</div>
		</div>
	<? endforeach; ?>
</section>