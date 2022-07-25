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

$currentDate_raw = Factory::getDate();
$currentDate = $currentDate_raw->format('Y-m-d H:i:s');

$raw_events = $this -> items;
$raw_dates = [$currentDate];
$sorted_events = [];
$firstElement = TRUE;

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
<section class="event-cards">
	<ul class="nav nav-tabs event-tabs" id="myTab" role="tablist">
		<?php foreach ($sorted_events as $key => $event) : ?>
			<?php
				$start_date = $event -> jcfields[8] -> value;
				$end_date = $event -> jcfields[21] -> value;
				$name = $event -> jcfields[7] -> rawvalue;				
				$img_id = json_decode($event -> jcfields[4] -> rawvalue) -> itemId;
				$img_name = json_decode($event -> jcfields[4] -> rawvalue) -> filename;
				$img_path = "images/econa/fields/4/com_content_article/{$img_id}/{$img_name}_L.jpg";
				$start_month = date("m", strtotime($start_date));
				$start_mont_name = DateTime::createFromFormat('!m', $start_month)->format('F');
				$start_day = date("d", strtotime($start_date));
				$start_year = date("y", strtotime($start_date));
				$start_time = date("H:i", strtotime($start_date));
				$end_month = date("m", strtotime($end_date));
				$end_mont_name = DateTime::createFromFormat('!m', $end_month)->format('F');
				$end_day = date("d", strtotime($end_date));
				$end_year = date("y", strtotime($end_date));
				$end_time = date("H:i", strtotime($end_date));

				if (strlen($name) > 50) {
					$name = substr($name, 0, 35) . "...";
				}
			?>
			<li class="nav-item event-card" role="presentation">
				<button class="event-card__container nav-link <?php echo ($firstElement) ? 'active' : '' ?>" id="<?php echo $name ?>" data-bs-toggle="tab" data-bs-target="#<?php echo str_replace(array(" ", ".", "1", "-"), "", $name) ?>" type="button" role="tab" aria-controls="<?php echo str_replace(array(" ", ".", "1", "-"), "", $name) ?>" aria-selected="<?php echo ($firstElement) ? 'true' : 'false' ?>">
					<picture class="event-card__picture">
						<img src="<?php echo $img_path ?>" alt="Event Bild">
					</picture>
					<div class="flex">
						<div class="event-card__date">
							<p class="event-card__date__month"><?php echo $start_mont_name ?></p>
							<p class="event-card__date__day"><?php echo $start_day ?></p>
						</div>
						<div class="event-card__info flex">
							<p><?php echo $start_time . " Uhr - " . $end_time . " Uhr" ?></p>
							<p><?php echo $name ?></p>
						</div>
					</div>
				</button>
			</li>
			<?php if ($firstElement) : ?>
				<?php $firstElement = FALSE; ?>
			<?php endif; ?>
		<? endforeach; ?>
	</ul>
	<section class="event-pane">
		<?php
			$firstElement = TRUE;
		?>
		<?php foreach ($sorted_events as $key => $event) : ?>
			<?php 
				$name = $event -> jcfields[7] -> rawvalue;
				$text = $event -> jcfields[2] -> rawvalue;
				$place = $event -> jcfields[9] -> rawvalue;
				$page = $event -> jcfields[10] -> rawvalue;
				$img_id = json_decode($event -> jcfields[11] -> rawvalue) -> itemId;
				$img_name = json_decode($event -> jcfields[11] -> rawvalue) -> filename;
				$img_path = "images/econa/fields/11/com_content_article/{$img_id}/{$img_name}_L.jpg";
				$start_date = $event -> jcfields[8] -> value;
				$end_date = $event -> jcfields[21] -> value;
			?>
			<div class="event-pane__info tab-pane fade <?php echo ($firstElement) ? 'active show' : '' ?>" id="<?php echo str_replace(array(" ", ".", "1", "-"), "", $name) ?>" role="tabpanel" aria-labelledby="<?php echo str_replace(array(" ", "."), "", $name) ?>-tab">
				<h1 class="event-pane__info__heading__<?php echo ( strlen ( $name ) > 20 ) ? 'small' : 'big' ?>"><?php echo $name ?></h1>	
				<picture class="event-pane__info__picture">
					<img src="<?php echo $img_path ?>" alt="Event Bild">
				</picture>	
				<p class="event-pane__info__text"><?php echo $text ?></p>
				<h2 class="event-pane__info__details-heading">Event-Details</h2>
				<p class="event-pane__info__place"><b>Ort:</b> <?php echo $place ?></p>
				<p class="event-pane__info__start"><b>Beginn:</b> <?php echo $start_date ?></p>
				<p class="event-pane__info__end"><b>Ende:</b> <?php echo $end_date ?></p>
				<?php if ($page !== "") : ?>
					<p class="event-pane__info__page"><b>Eventseite:</b> <a href="<?php echo $page ?>" target="_blank"><?php echo $page ?></a></p>
				<?php endif; ?>
			</div>
			<?php $firstElement = FALSE ?>
		<? endforeach; ?>
	</section>
</section>