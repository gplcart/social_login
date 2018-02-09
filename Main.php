<?php

/**
 * @package Social Login
 * @author Iurii Makukh
 * @copyright Copyright (c) 2018, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\social_login;

use Exception;
use gplcart\core\Controller;
use gplcart\core\Module;
use InvalidArgumentException;

/**
 * Main class for Social Login module
 */
class Main
{

    /**
     * Module class instance
     * @var \gplcart\core\Module $module
     */
    protected $module;

    /**
     * @param Module $module
     */
    public function __construct(Module $module)
    {
        $this->module = $module;
    }

    /**
     * Implements hook "construct.controller.frontend"
     * @param \gplcart\core\controllers\frontend\Controller $controller
     */
    public function hookConstructControllerFrontend($controller)
    {
        if (!$controller->isInternalRoute() && !$controller->isLoggedIn()) {

            $controller->setData('print_social_login_buttons', function ($controller) {
                return $this->getButtonsSafely($controller);
            });

            if (in_array($controller->path(), array('login', 'register'))) {
                $controller->setData('social_login_buttons', $this->getButtonsSafely($controller));
            }
        }
    }

    /**
     * Implements hook "route.list"
     * @param array $routes
     */
    public function hookRouteList(array &$routes)
    {
        $routes['admin/module/settings/social_login'] = array(
            'access' => 'module_edit',
            'handlers' => array(
                'controller' => array('gplcart\\modules\\social_login\\controllers\\Settings', 'editSettings')
            )
        );
    }

    /**
     * Implements hook "module.oauth.providers"
     * @param array $providers
     */
    public function hookModuleOauthProviders(array &$providers)
    {
        $providers = array_merge($providers, $this->getProviders());
    }

    /**
     * Returns an array of rendered buttons keyed by a provider ID
     * @param \gplcart\core\controllers\frontend\Controller $controller
     * @param array $params
     * @return array
     * @throws InvalidArgumentException
     */
    public function getButtons($controller, array $params = array())
    {
        if (!$controller instanceof Controller) {
            throw new InvalidArgumentException('First argument must be instance of \gplcart\core\Controller');
        }

        /** @var \gplcart\modules\oauth\Main $module */
        $module = $this->module->getInstance('oauth');
        $model = $module->getModel();

        $buttons = array();

        foreach ($module->getProviders(array('type' => 'login')) as $id => $provider) {

            if (empty($provider['status'])
                || empty($provider['settings']['client_id'])
                || empty($provider['settings']['client_secret'])) {
                continue;
            }

            $data = array('provider' => $provider, 'url' => $model->getAuthUrl($provider, $params));
            $buttons[$id] = $controller->render($provider['template']['button'], $data);
        }

        return $buttons;
    }

    /**
     * Returns an array of rendered buttons or caught exception messages
     * @param \gplcart\core\controllers\frontend\Controller $controller
     * @return array
     */
    protected function getButtonsSafely($controller)
    {
        try {
            return $this->getButtons($controller);
        } catch (Exception $ex) {
            return array($ex->getMessage());
        }
    }

    /**
     * Returns an array of providers
     * @return array
     */
    protected function getProviders()
    {
        $providers = array(
            'facebook' => array(
                'name' => 'Facebook', // @text
                'scope' => 'email',
                'url' => array(
                    'auth' => 'https://www.facebook.com/v2.8/dialog/oauth',
                    'token' => 'https://graph.facebook.com/v2.8/oauth/access_token'
                )
            ),
            'google' => array(
                'name' => 'Google+', // @text
                'scope' => 'email',
                'url' => array(
                    'auth' => 'https://accounts.google.com/o/oauth2/auth',
                    'token' => 'https://accounts.google.com/o/oauth2/token'
                )
            )
        );

        return $this->prepareProviders($providers);
    }

    /**
     * Prepare an array of providers
     * @param array $providers
     * @return array
     */
    protected function prepareProviders(array $providers)
    {
        $settings = $this->module->getSettings('social_login');

        foreach ($providers as $provider_id => &$provider) {
            $provider += array(
                'id' => $provider_id,
                'type' => 'login',
                'status' => !empty($settings['status'][$provider_id]),
                'settings' => array(
                    'register' => $settings['register'],
                    'register_login' => $settings['register_login'],
                    'register_status' => $settings['register_status'],
                    'client_id' => isset($settings['client_id'][$provider_id]) ? $settings['client_id'][$provider_id] : '',
                    'client_secret' => isset($settings['client_secret'][$provider_id]) ? $settings['client_secret'][$provider_id] : '',
                ),
                'template' => array('button' => "social_login|buttons/$provider_id"),
                'handlers' => array(
                    'authorize' => array("gplcart\\modules\\social_login\\handlers\\$provider_id", 'authorize'),
                )
            );
        }

        return $providers;
    }


}
