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
     * Socket client helper instance
     * @var \gplcart\core\helpers\Socket $socket
     */
    protected $socket;

    /**
     * User model instance
     * @var \gplcart\core\models\User $user
     */
    protected $user;

    /**
     * User access model instance
     * @var \gplcart\core\models\UserAction $user_action
     */
    protected $user_action;

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
        $this->store = Container::get('gplcart\\core\\models\\Store');
        $this->socket = Container::get('gplcart\\core\\helpers\\Socket');
        $this->user_action = Container::get('gplcart\\core\\models\\UserAction');
    }

    /**
     * Sets a property
     * @param string $property
     * @param mixed $value
     */
    public function setProperty($property, $value)
    {
        $this->{$property} = $value;
    }

    /**
     * Login/register a new user
     * @param array $user
     * @param array $provider
     * @return mixed
     */
    protected function submitUser(array $user, array $provider)
    {
        $existing = $this->user->getByEmail($user['email']);

        if (empty($existing)) {
            return $this->registerUser($user, $provider);
        }

        return $this->user_action->login($existing, false);
    }

    /**
     * Register a new user
     * @param array $user
     * @param array $provider
     * @return mixed
     */
    protected function registerUser(array $user, array $provider)
    {
        if (empty($provider['settings']['register'])) {
            return false;
        }

        $store = $this->store->get();

        $user['store_id'] = $store['store_id'];
        $user['password'] = $this->user->generatePassword();
        $user['login'] = !empty($provider['settings']['register_login']);
        $user['status'] = !empty($provider['settings']['register_status']);

        return $this->user_action->register($user);
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
        try {
            $query += array('access_token' => $params['token']);
            $response = $this->socket->request($url, array('query' => array_filter($query)));
            return json_decode($response['data'], true);
        } catch (\Exception $ex) {
            trigger_error($ex->getMessage());
            return array();
        }
    }

}
