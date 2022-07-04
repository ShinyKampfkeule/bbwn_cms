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
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Administrator\Extension\ContentComponent;
use Joomla\Component\Content\Site\Helper\RouteHelper;

// Create a shortcut for params.
$params = $this->item->params;
$canEdit = $this->item->params->get('access-edit');
$info    = $params->get('info_block_position', 0);

// Check if associations are implemented. If they are, define the parameter.
$assocParam = (Associations::isEnabled() && $params->get('show_associations'));

$currentDate   = Factory::getDate()->format('Y-m-d H:i:s');
$isUnpublished = ($this->item->state == ContentComponent::CONDITION_UNPUBLISHED || $this->item->publish_up > $currentDate)
	|| ($this->item->publish_down < $currentDate && $this->item->publish_down !== null);

$id = json_decode($this->item->jcfields[4]->rawvalue)->itemId;
$name = json_decode($this->item->jcfields[4]->rawvalue)->filename;
$bild1s = "/images/econa/fields/4/com_content_article/{$id}/{$name}_S.jpg";
$bild1m = "/images/econa/fields/4/com_content_article/{$id}/{$name}_M.jpg";
$bild1l = "/images/econa/fields/4/com_content_article/{$id}/{$name}_L.jpg";
$placeholder_image = "/images/placeholder/1575466871112.jfif"

?>
<article class="layout1">
  <div class="layout1__left">
      <picture>
          <img src="<?php echo $bild1l ?>" />
      </picture>
      <h2 class="layout1__heading"><?php echo $this->item->title; ?></h2>
  </div>
  <section class="layout1__preview-section">
    <h2 class="layout1__preview-heading"><?php echo $this->item->title; ?></h2>
    <?php if (isset($this->item->jcfields[3]->rawvalue)): ?>
      <p class="layout1__preview-text"><?php echo substr($this->item->jcfields[3]->rawvalue, 0, 500); ?>...</p>
    <?php endif; ?>
    <button class="layout1__preview-button">&#171; Weiterlesen &#187;</button>
  </section>
</article>
