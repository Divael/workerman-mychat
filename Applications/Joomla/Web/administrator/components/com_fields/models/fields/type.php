<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_fields
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

/**
 * Fields Type
 *
 * @since  3.7.0
 */
class JFormFieldType extends JFormFieldList
{
    public $type = 'Type';

    /**
     * Method to attach a JForm object to the field.
     *
     * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
     * @param   mixed             $value    The form field value to validate.
     * @param   string            $group    The field name group control value. This acts as as an array container for the field.
     *                                      For example if the field has name="foo" and the group value is set to "bar" then the
     *                                      full field name would end up being "bar[foo]".
     *
     * @return  boolean  True on success.
     *
     * @since   3.7.0
     */
    public function setup(SimpleXMLElement $element, $value, $group = null)
    {
        $return = parent::setup($element, $value, $group);

        $this->onchange = 'typeHasChanged(this);';

        return $return;
    }

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     *
     * @since   3.7.0
     */
    protected function getOptions()
    {
        $options = parent::getOptions();

        $fieldTypes = FieldsHelper::getFieldTypes();

        foreach ($fieldTypes as $fieldType) {
            $options[] = JHtml::_('select.option', $fieldType['type'], $fieldType['label']);
        }

        // Sorting the fields based on the text which is displayed
        usort(
            $options,
            function ($a, $b) {
                return strcmp($a->text, $b->text);
            }
        );

        // Reload the page when the type changes
        $uri = clone JUri::getInstance('index.php');

        // Removing the catid parameter from the actual URL and set it as
        // return
        $returnUri = clone JUri::getInstance();
        $returnUri->setVar('catid', null);
        $uri->setVar('return', base64_encode($returnUri->toString()));

        // Setting the options
        $uri->setVar('option', 'com_fields');
        $uri->setVar('task', 'field.storeform');
        $uri->setVar('context', 'com_fields.field');
        $uri->setVar('formcontrol', $this->form->getFormControl());
        $uri->setVar('userstatevariable', 'com_fields.edit.field.data');
        $uri->setVar('view', null);
        $uri->setVar('layout', null);


        JFactory::getDocument()->addScriptDeclaration("
			jQuery( document ).ready(function() {
				Joomla.loadingLayer('load');
			});
			function typeHasChanged(element){
				Joomla.loadingLayer('show');
				var cat = jQuery(element);
				jQuery('input[name=task]').val('field.storeform');
				element.form.action='" . $uri . "';
				element.form.submit();
			}
		");

        return $options;
    }
}
