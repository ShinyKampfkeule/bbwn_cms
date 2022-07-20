<?php
    /**
     * @package     Joomla.Site
     * @subpackage  com_content
     *
     * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
     * @license     GNU General Public License version 2 or later; see LICENSE.txt
     */

    defined('_JEXEC') or die;

    use Joomla\CMS\Layout\LayoutHelper;
    use Joomla\CMS\Factory;

    $categoryPath = $this -> category -> path;
?>
<div class="com-content-category category-list">
    <?php include "default_{$categoryPath}.php" ?>
</div>
