<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$params             = $this->params;

$displayGroups      = $params->get('show_user_custom_fields');
$userFieldGroups    = array();
?>

<?php if (!$displayGroups || !$this->contactUser) : ?>
	<?php return; ?>
<?php endif; ?>

<?php foreach ($this->contactUser->jcfields as $field) :?>
	<?php if (!in_array('-1', $displayGroups) && (!$field->group_id || !in_array($field->group_id, $displayGroups))) : ?>
		<?php continue; ?>
	<?php endif; ?>
	<?php if (!key_exists($field->group_title, $userFieldGroups)) : ?>
		<?php $userFieldGroups[$field->group_title] = array();?>
	<?php endif; ?>
	<?php $userFieldGroups[$field->group_title][] = $field;?>
<?php endforeach; ?>

<?php foreach ($userFieldGroups as $groupTitle => $fields) :?>
	<?php $id = JApplicationHelper::stringURLSafe($groupTitle); ?>
	<?php if ($this->params->get('presentation_style') === 'sliders') :
        echo JHtml::_('sliders.panel', $groupTitle ?: JText::_('COM_CONTACT_USER_FIELDS'), 'display-' . $id); ?>
	<?php endif; ?>
	<?php if ($this->params->get('presentation_style') === 'tabs') : ?>
		<?php echo JHtmlTabs::panel($groupTitle ?: JText::_('COM_CONTACT_USER_FIELDS'), 'display-' . $id); ?>
	<?php endif; ?>
	<?php if ($this->params->get('presentation_style') === 'plain'):?>
		<?php echo '<h3>' . ($groupTitle ?: JText::_('COM_CONTACT_USER_FIELDS')) . '</h3>'; ?>
	<?php endif; ?>

	<div class="contact-profile" id="user-custom-fields-<?php echo $id; ?>">
		<dl class="dl-horizontal">
		<?php foreach ($fields as $field) :?>
			<?php if (!$field->value) : ?>
				<?php continue; ?>
			<?php endif; ?>

			<?php echo '<dt>' . $field->label . '</dt>'; ?>
			<?php echo '<dd>' . $field->value . '</dd>'; ?>
		<?php endforeach; ?>
		</dl>
	</div>

<?php endforeach; ?>
