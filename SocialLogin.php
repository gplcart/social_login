<?php

/**
 * @package Social Login
 * @author Iurii Makukh
 * @copyright Copyright (c) 2017, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */

namespace gplcart\modules\social_login;

use gplcart\core\Module;
use gplcart\core\models\Language as LanguageModel;

/**
 * Main class for Social Login module
 */
class SocialLogin extends Module
{

    /**
     * Language model instance
     * @var \gplcart\core\models\Language $language
     */
    protected $language;

    /**
     * @param LanguageModel $language
     */
    public function __construct(LanguageModel $language)
    {
        parent::__construct();

        $this->language = $language;
    }

    /**
     * Module info
     * @return array
     */
    public function info()
    {
        return array(
            'name' => 'Social Login',
            'version' => '1.0.0-dev',
            'description' => 'Allows users to register and login to your GPL Cart site with their existing accounts from social networks',
            'author' => 'Iurii Makukh ',
            'core' => '1.x',
            'license' => 'GPL-3.0+',
            'configure' => 'admin/module/settings/social_login',
            'settings' => array(
                'status' => array(),
                'client_id' => array(),
                'client_secret' => array(),
                'register' => true,
                'register_login' => true,
                'register_status' => true
            )
        );
    }

    /**
     * Implements hook "route.list"
     * @param array $routes
     */
    public function hookRouteList(array &$routes)
    {
        // Module settings page
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
        $settings = $this->config->module('social_login');

        // Facebook
        $providers['facebook'] = array(
            'name' => $this->language->text('Facebook'),
            'description' => '',
            'status' => !empty($settings['status']['facebook']),
            'type' => 'login',
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
            'query' => array('scope' => 'email'),
            'template' => array('button' => 'social_login|buttons/facebook'),
            'handlers' => array(
                'process' => array('gplcart\\modules\\social_login\\handlers\\Facebook', 'process'),
            )
        );

        // Google+
        $providers['google'] = array(
            'name' => $this->language->text('Google+'),
            'description' => '',
            'type' => 'login',
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
            'query' => array('scope' => 'email'),
            'template' => array('button' => 'social_login|buttons/google'),
            'handlers' => array(
                'process' => array('gplcart\\modules\\social_login\\handlers\\Google', 'process'),
            )
        );
    }

}
