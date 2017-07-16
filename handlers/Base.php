<?php

/**
 * @package Social Login
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\social_login\handlers;

use gplcart\core\Container;

/**
 * Base class for other Oauth 2.0 providers
 */
class Base
{

    /**
     * Curl helper instance
     * @var \gplcart\core\helpers\Curl $curl
     */
    protected $curl;

    /**
     * User model instance
     * @var \gplcart\core\models\User $user
     */
    protected $user;

    /**
     * Store model instance
     * @var \gplcart\core\models\Store $store
     */
    protected $store;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->user = Container::get('gplcart\\core\\models\\User');
        $this->curl = Container::get('gplcart\\core\\helpers\\Curl');
        $this->store = Container::get('gplcart\\core\\models\\Store');
    }

    /**
     * Login/register a new user
     * @param array $user
     * @return mixed
     */
    protected function submitUser(array $user, array $provider)
    {
        $existing = $this->user->getByEmail($user['email']);

        if (empty($existing)) {
            return $this->registerUser($user, $provider);
        }

        return $this->user->login($existing, false);
    }

    /**
     * Register a new user
     * @return mixed
     */
    protected function registerUser(array $user, array $provider)
    {
        if (empty($provider['settings']['register'])) {
            return false;
        }

        $store = $this->store->getCurrent();

        $user['store_id'] = $store['store_id'];
        $user['password'] = $this->user->generatePassword();

        $user['login'] = !empty($provider['settings']['register_login']);
        $user['status'] = !empty($provider['settings']['register_status']);

        return $this->user->register($user);
    }

    /**
     * Request user data
     * @param array $params
     * @param string $url
     * @param array $query
     * @return mixed
     */
    protected function requestData(array $params, $url, $query = array())
    {
        $query += array(
            'access_token' => $params['token']
        );

        $response = $this->curl->get($url, array('query' => array_filter($query)));
        return json_decode($response, true);
    }

}
