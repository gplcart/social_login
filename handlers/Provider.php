<?php

/**
 * @package Social Login
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\social_login\handlers;

use gplcart\core\Container;
use OutOfRangeException;
use UnexpectedValueException;

/**
 * Base class for other Oauth 2.0 providers
 */
class Provider
{

    /**
     * Http model instance instance
     * @var \gplcart\core\models\Http $http
     */
    protected $http;

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
        $this->http = Container::get('gplcart\\core\\models\\Http');
        $this->user = Container::get('gplcart\\core\\models\\User');
        $this->store = Container::get('gplcart\\core\\models\\Store');
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
     * @return array
     * @throws OutOfRangeException
     */
    protected function submitUser(array $user, array $provider)
    {
        if (empty($user['email'])) {
            throw new OutOfRangeException("Empty user ID in the submitting user data");
        }

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
     * @return array
     */
    protected function registerUser(array $user, array $provider)
    {
        if (empty($provider['settings']['register'])) {
            return array();
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
     * @return array
     * @throws UnexpectedValueException
     */
    protected function request(array $params, $url, $query = array())
    {
        if (!isset($query['access_token']) && isset($params['token']['access_token'])) {
            $query['access_token'] = $params['token']['access_token'];
        }

        $response = $this->http->request($url, array('query' => array_filter($query)));

        if ($response['status']['code'] != 200) {
            throw new UnexpectedValueException("Expected response code - 200, received - {$response['status']['code']}");
        }

        $decoded = json_decode($response['data'], true);

        if (empty($decoded) || !is_array($decoded)) {
            throw new UnexpectedValueException('Failed to decode response data');
        }

        return $decoded;
    }

}
