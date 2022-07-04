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

?>
<pre>
    <?php
        $id = json_decode($this->item->jcfields[4]->rawvalue)->itemId;
        $name = json_decode($this->item->jcfields[4]->rawvalue)->filename;
        $bild1s = "/images/econa/fields/4/com_content_article/{$id}/{$name}_S.jpg";
        $bild1m = "/images/econa/fields/4/com_content_article/{$id}/{$name}_M.jpg";
        $bild1l = "/images/econa/fields/4/com_content_article/{$id}/{$name}_L.jpg";
    ?>
</pre>
<article>
    <div class="blog-grid-container">
        <h2 class="grid_element-1-2" <?php echo (strlen($this->item->title) < 20) ? 'class="big"' : 'class="small"' ?>><?php echo $this->item->title; ?></h2>
        <?php if ($name !== "") : ?>
            <figure class="grid_element-1_2-1">
                <picture>
                    <img class="bigger_image"  src="<?php echo $bild1s ?>" />
                </picture>
            </figure>
        <?php endif; ?>
        <?php if (isset($this->item->jcfields[3]->rawvalue)): ?>
            <p class="grid-element-2-2"><?php echo $this->item->jcfields[3]->rawvalue; ?></p>
        <?php endif; ?>
    </div>
</article>
