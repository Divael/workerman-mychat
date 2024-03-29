<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Template.system
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/** @var JDocumentError $this */

?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta charset="utf-8" />
	<title><?php echo $this->error->getCode(); ?> - <?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></title>
	<link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/error.css" rel="stylesheet" />
	<!--[if lt IE 9]><script src="<?php echo JUri::root(true); ?>/media/jui/js/html5.js"></script><![endif]-->
</head>
<body>
	<table class="outline" style="margin: 0 auto; width: 550px;">
		<tr>
			<td style="text-align: center;">
				<h1><?php echo $this->error->getCode() ?> - <?php echo JText::_('JERROR_AN_ERROR_HAS_OCCURRED'); ?></h1>
			</td>
		</tr>
		<tr>
			<td style="text-align: center;">
				<p><?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></p>
				<p><a href="<?php echo JRoute::_('index.php'); ?>"><?php echo JText::_('JGLOBAL_TPL_CPANEL_LINK_TEXT'); ?></a></p>
				<?php if ($this->debug) : ?>
					<div>
						<?php echo $this->renderBacktrace(); ?>
						<?php // Check if there are more Exceptions and render their data as well?>
						<?php if ($this->error->getPrevious()) : ?>
							<?php $loop = true; ?>
							<?php // Reference $this->_error here and in the loop as setError() assigns errors to this property and we need this for the backtrace to work correctly?>
							<?php // Make the first assignment to setError() outside the loop so the loop does not skip Exceptions?>
							<?php $this->setError($this->_error->getPrevious()); ?>
							<?php while ($loop === true) : ?>
								<p><strong><?php echo JText::_('JERROR_LAYOUT_PREVIOUS_ERROR'); ?></strong></p>
								<p><?php echo htmlspecialchars($this->_error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></p>
								<?php echo $this->renderBacktrace(); ?>
								<?php $loop = $this->setError($this->_error->getPrevious()); ?>
							<?php endwhile; ?>
							<?php // Reset the main error object to the base error?>
							<?php $this->setError($this->error); ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</td>
		</tr>
	</table>
</body>
</html>
