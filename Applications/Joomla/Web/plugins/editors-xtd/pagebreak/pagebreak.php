<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Editors-xtd.pagebreak
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Editor Pagebreak buton
 *
 * @since  1.5
 */
class PlgButtonPagebreak extends JPlugin
{
    /**
     * Load the language file on instantiation.
     *
     * @var    boolean
     * @since  3.1
     */
    protected $autoloadLanguage = true;

    /**
     * Display the button
     *
     * @param   string  $name  The name of the button to add
     *
     * @return  JObject  The button options as JObject
     *
     * @since   1.5
     */
    public function onDisplay($name)
    {
        $user  = JFactory::getUser();

        if ($user->authorise('core.create', 'com_content')
            || $user->authorise('core.edit', 'com_content')
            || $user->authorise('core.edit.own', 'com_content')) {
            JFactory::getDocument()->addScriptOptions('xtd-pagebreak', array('editor' => $name));
            $link = 'index.php?option=com_content&amp;view=article&amp;layout=pagebreak&amp;tmpl=component&amp;e_name=' . $name;

            $button          = new JObject;
            $button->modal   = true;
            $button->class   = 'btn';
            $button->link    = $link;
            $button->text    = JText::_('PLG_EDITORSXTD_PAGEBREAK_BUTTON_PAGEBREAK');
            $button->name    = 'copy';
            $button->options = "{handler: 'iframe', size: {x: 500, y: 300}}";

            return $button;
        }
    }
}
