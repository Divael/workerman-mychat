<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_joomlaupdate
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/** @var JoomlaupdateViewDefault $this */

JHtml::_('jquery.framework');
JHtml::_('bootstrap.tooltip');
JHtml::_('formbehavior.chosen', 'select');
JHtml::script('com_joomlaupdate/default.js', false, true, false);

JFactory::getDocument()->addScriptDeclaration("
jQuery(document).ready(function($) {
	$('#extraction_method').change(function(e){
		extractionMethodHandler('#extraction_method', 'row_ftp');
	});
	$('#upload_method').change(function(e){
		extractionMethodHandler('#upload_method', 'upload_ftp');
	});

	$('button.submit').on('click', function() {
		$('div.download_message').show();
	});
});");
?>

<div id="joomlaupdate-wrapper">
	<form enctype="multipart/form-data" action="index.php" method="post" id="adminForm" class="form-horizontal">
		<?php echo  JHtml::_('sliders.start', 'joomlaupdate-slider'); ?>
		<?php if ($this->showUploadAndUpdate) : ?>
			<?php echo JHtml::_('sliders.panel', JText::_('COM_JOOMLAUPDATE_VIEW_DEFAULT_TAB_ONLINE'), 'online-update'); ?>
		<?php endif; ?>

		<?php if ($this->selfUpdate) : ?>
			<?php // If we have a self update notice to install it first!?>
			<?php JFactory::getApplication()->enqueueMessage(JText::_('COM_JOOMLAUPDATE_VIEW_DEFAULT_INSTALL_SELF_UPDATE_FIRST'), 'error'); ?>
			<?php echo $this->loadTemplate('updatemefirst'); ?>
		<?php else : ?>
			<?php if (!isset($this->updateInfo['object']->downloadurl->_data) && $this->updateInfo['installed'] < $this->updateInfo['latest']) : ?>
				<?php // If we have no download URL we can't reinstall or update?>
				<?php echo $this->loadTemplate('nodownload'); ?>
			<?php elseif (!$this->updateInfo['hasUpdate']) : ?>
				<?php // If we have no update we can reinstall the core?>
				<?php echo $this->loadTemplate('reinstall'); ?>
			<?php else : ?>
				<?php // Ok let's show the update template?>
				<?php echo $this->loadTemplate('update'); ?>
			<?php endif; ?>
		<?php endif; ?>

		<input type="hidden" name="task" value="update.download" />
		<input type="hidden" name="option" value="com_joomlaupdate" />

		<?php echo JHtml::_('form.token'); ?>
	</form>

	<?php // Only Super Users have access to the Update & Install for obvious security reasons?>
	<?php if ($this->showUploadAndUpdate) : ?>
		<?php echo JHtml::_('sliders.panel', JText::_('COM_JOOMLAUPDATE_VIEW_DEFAULT_TAB_UPLOAD'), 'upload-update'); ?>
		<?php echo $this->loadTemplate('upload'); ?>
		<?php echo JHtml::_('sliders.end'); ?>
	<?php endif; ?>

	<div class="download_message" style="display: none">
		<p></p>
		<p class="nowarning">
			<?php echo JText::_('COM_JOOMLAUPDATE_VIEW_DEFAULT_DOWNLOAD_IN_PROGRESS'); ?>
		</p>
		<div class="joomlaupdate_spinner"></div>
	</div>
	<div id="loading"></div>
</div>
