<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_newsfeeds
 *
 * @copyright   (C) 2021 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>
<form action="<?php echo Route::_('index.php?option=com_newsfeeds&view=newsfeed'); ?>" method="post" name="adminForm" id="adminForm">

	<div class="px-4 py-5 my-5 text-center">
		<span class="fa-8x icon-rss newsfeeds mb-4" aria-hidden="true"></span>
		<h1 class="display-5 fw-bold"><?php echo Text::_('COM_NEWSFEEDS_BLANKSTATE_TITLE'); ?></h1>
		<div class="col-lg-6 mx-auto">
			<p class="lead mb-4">
				<?php echo Text::_('COM_NEWSFEEDS_BLANKSTATE_NEWSFEEDS'); ?>
			</p>
			<div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
				<a href="<?php echo Route::_('index.php?option=com_newsfeeds&view=newsfeed&layout=edit'); ?>" class="btn btn-primary btn-lg px-4 me-sm-3"><?php echo Text::_('COM_NEWSFEEDS_BLANKSTATE_BUTTON_ADD'); ?></a>
				<a href="https://docs.joomla.org/Special:MyLanguage/Help4.x:News_Feeds" class="btn btn-outline-secondary btn-lg px-4"><?php echo Text::_('COM_NEWSFEEDS_BLANKSTATE_BUTTON_LEARNMORE'); ?></a>
			</div>
		</div>
	</div>

	<input type="hidden" name="task" value="">
	<input type="hidden" name="boxchecked" value="0">
</form>
