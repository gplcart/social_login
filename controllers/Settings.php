<?php

/**
 * @package Social Login
 * @author Iurii Makukh
 * @copyright Copyright (c) 2017, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\social_login\controllers;

use gplcart\core\controllers\backend\Controller;
use gplcart\modules\oauth\models\Oauth;

/**
 * Handles incoming requests and outputs data related to Social Login module
 */
class Settings extends Controller
{

    /**
     * Oauth model instance
     * @var \gplcart\modules\oauth\models\Oauth $oauth
     */
    protected $oauth;

    /**
     * Settings constructor.
     * @param Oauth $oauth
     */
    public function __construct(Oauth $oauth)
    {
        parent::__construct();

        $this->oauth = $oauth;
    }

    /**
     * Route page callback to display the module settings page
     */
    public function editSettings()
    {
        $this->setTitleEditSettings();
        $this->setBreadcrumbEditSettings();

        $this->setData('settings', $this->module->getSettings('social_login'));
        $this->setData('providers', $this->oauth->getProviders(array('type' => 'login')));

        $this->submitSettings();
        $this->outputEditSettings();
    }

    /**
     * Set title on the module settings page
     */
    protected function setTitleEditSettings()
    {
        $title = $this->text('Edit %name settings', array(
            '%name' => $this->text('Social Login')));

        $this->setTitle($title);
    }

    /**
     * Set breadcrumbs on the module settings page
     */
    protected function setBreadcrumbEditSettings()
    {
        $breadcrumbs = array();

        $breadcrumbs[] = array(
            'text' => $this->text('Dashboard'),
            'url' => $this->url('admin')
        );

        $breadcrumbs[] = array(
            'text' => $this->text('Modules'),
            'url' => $this->url('admin/module/list')
        );

        $this->setBreadcrumbs($breadcrumbs);
    }

    /**
     * Saves the submitted settings
     */
    protected function submitSettings()
    {
        if ($this->isPosted('save') && $this->validateSettings()) {
            $this->updateSettings();
        }
    }

    /**
     * Validate submitted module settings
     */
    protected function validateSettings()
    {
        $this->setSubmitted('settings');

        $this->setSubmittedBool('register');
        $this->setSubmittedBool('register_login');
        $this->setSubmittedBool('register_status');

        if ($this->getSubmitted('register_login')) {
            $this->setSubmitted('register_status', true);
        }

        return !$this->hasErrors();
    }

    /**
     * Update module settings
     */
    protected function updateSettings()
    {
        $this->controlAccess('module_edit');
        $this->module->setSettings('social_login', $this->getSubmitted());
        $this->redirect('', $this->text('Settings have been updated'), 'success');
    }

    /**
     * Render and output the module settings page
     */
    protected function outputEditSettings()
    {
        $this->output('social_login|settings');
    }

}
