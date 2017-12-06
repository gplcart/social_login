<?php

/**
 * @package Social Login
 * @author Iurii Makukh
 * @copyright Copyright (c) 2017, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\social_login;

use gplcart\core\Module;

/**
 * Main class for Social Login module
 */
class SocialLogin
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
     * Implements hook "oauth.providers"
     * @param array $providers
     */
    public function hookOauthProviders(array &$providers)
    {
        $settings = $this->module->getSettings('social_login');

        $providers['facebook'] = array(
            'name' => 'Facebook',
            'status' => !empty($settings['status']['facebook']),
            'type' => 'login',
            'scope' => 'email',
            'url' => array(
                'auth' => 'https://www.facebook.com/v2.8/dialog/oauth',
                'token' => 'https://graph.facebook.com/v2.8/oauth/access_token'
            ),
            'settings' => array(
                'register' => $settings['register'],
                'register_login' => $settings['register_login'],
                'register_status' => $settings['register_status'],
                'client_id' => isset($settings['client_id']['facebook']) ? $settings['client_id']['facebook'] : '',
                'client_secret' => isset($settings['client_secret']['facebook']) ? $settings['client_secret']['facebook'] : '',
            ),
            'template' => array('button' => 'social_login|buttons/facebook'),
            'handlers' => array(
                'process' => array('gplcart\\modules\\social_login\\handlers\\Facebook', 'process'),
            )
        );

        $providers['google'] = array(
            'name' => 'Google+',
            'type' => 'login',
            'scope' => 'email',
            'status' => !empty($settings['status']['google']),
            'url' => array(
                'auth' => 'https://accounts.google.com/o/oauth2/auth',
                'token' => 'https://accounts.google.com/o/oauth2/token'
            ),
            'settings' => array(
                'register' => $settings['register'],
                'register_login' => $settings['register_login'],
                'register_status' => $settings['register_status'],
                'client_id' => isset($settings['client_id']['google']) ? $settings['client_id']['google'] : '',
                'client_secret' => isset($settings['client_secret']['google']) ? $settings['client_secret']['google'] : '',
            ),
            'template' => array('button' => 'social_login|buttons/google'),
            'handlers' => array(
                'process' => array('gplcart\\modules\\social_login\\handlers\\Google', 'process'),
            )
        );
    }

}
