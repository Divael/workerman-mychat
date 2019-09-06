<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Display Controller for global configuration
 *
 * @since  3.2
 */
class ConfigControllerConfigDisplay extends ConfigControllerDisplay
{
    /**
     * Method to display global configuration.
     *
     * @return  boolean	True on success, false on failure.
     *
     * @since   3.2
     */
    public function execute()
    {
        // Get the application
        $app = $this->getApplication();

        // Get the document object.
        $document     = JFactory::getDocument();

        $viewName     = $this->input->getWord('view', 'config');
        $viewFormat   = $document->getType();
        $layoutName   = $this->input->getWord('layout', 'default');

        // Access backend com_config
        JLoader::registerPrefix(ucfirst($viewName), JPATH_ADMINISTRATOR . '/components/com_config');
        $displayClass = new ConfigControllerApplicationDisplay;

        // Set backend required params
        $document->setType('json');
        $app->input->set('view', 'application');

        // Execute backend controller
        $serviceData = json_decode($displayClass->execute(), true);

        // Reset params back after requesting from service
        $document->setType('html');
        $app->input->set('view', $viewName);

        // Register the layout paths for the view
        $paths = new SplPriorityQueue;
        $paths->insert(JPATH_COMPONENT . '/view/' . $viewName . '/tmpl', 'normal');

        $viewClass  = 'ConfigView' . ucfirst($viewName) . ucfirst($viewFormat);
        $modelClass = 'ConfigModel' . ucfirst($viewName);

        if (class_exists($viewClass)) {
            if ($viewName !== 'close') {
                $model = new $modelClass;

                // Access check.
                if (!JFactory::getUser()->authorise('core.admin', $model->getState('component.option'))) {
                    return;
                }
            }

            $view = new $viewClass($model, $paths);

            $view->setLayout($layoutName);

            // Push document object into the view.
            $view->document = $document;

            // Load form and bind data
            $form = $model->getForm();

            if ($form) {
                $form->bind($serviceData);
            }

            // Set form and data to the view
            $view->form = &$form;
            $view->data = &$serviceData;

            // Render view.
            echo $view->render();
        }

        return true;
    }
}
