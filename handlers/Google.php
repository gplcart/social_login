<?php

/**
 * @package Social Login
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2015, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\social_login\handlers;

use gplcart\modules\social_login\handlers\Base as BaseHandler;

/**
 * Contains methods for authorization with Google+
 */
class Google extends BaseHandler
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Process Google+ authorization
     * @param array $params
     * @param array $provider
     * @return mixed
     */
    public function authorize(array $params, array $provider)
    {
        $user = $this->request($params, 'https://www.googleapis.com/oauth2/v2/userinfo');

        if (!empty($user['verified_email'])) {
            return $this->submitUser($user, $provider);
        }

        return array();
    }

}
