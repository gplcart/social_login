<?php

/**
 * @package Social Login
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2018, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\social_login\handlers;

use gplcart\modules\social_login\handlers\Base as BaseHandler;

/**
 * Contains methods for authorization with Facebook
 */
class Facebook extends BaseHandler
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Process Facebook authorization
     * @param array $params
     * @param array $provider
     * @return mixed
     */
    public function authorize(array $params, array $provider)
    {
        $user = $this->request($params, 'https://graph.facebook.com/me', array('fields' => 'name,email'));
        return $this->submitUser($user, $provider);
    }

}
