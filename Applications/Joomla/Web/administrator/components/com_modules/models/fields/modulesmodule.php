<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_modules
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

JLoader::register('ModulesHelper', JPATH_ADMINISTRATOR . '/components/com_modules/helpers/modules.php');

JFormHelper::loadFieldClass('list');

/**
 * Modules Module field.
 *
 * @since  3.4.2
 */
class JFormFieldModulesModule extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  3.4.2
     */
    protected $type = 'ModulesModule';

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     *
     * @since   3.4.2
     */
    public function getOptions()
    {
        $clientId = JFactory::getApplication()->input->get('client_id', 0, 'int');
        $options  = ModulesHelper::getModules($clientId);

        return array_merge(parent::getOptions(), $options);
    }
}
