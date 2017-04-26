<?php

/**
 * @author  Laurynas Karvelis <laurynas.karvelis@gmail.com>
 * @author  Explosive Brains Limited
 * @license http://sam.zoy.org/wtfpl/COPYING
 */
class Facebook_Api_Base
{
    /**
     * @var Facebook_Config
     */
    private $_config;

    /**
     * Self explanatory constants
     */
    const GRAPH_URL                = 'https://graph.facebook.com';
    const ACCESS_TOKEN_REDEEM_CALL = '/oauth/access_token';

    /**
     * Constructor
     *
     * @param void
     * @return Facebook_Api_Base
     */
    public function __construct()
    {
        $this->setConfig(new Facebook_Config());
    }

    /**
     * Returns Configuration object
     *
     * @param void
     * @return Facebook_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Sets new Configuration object
     *
     * @param Facebook_Config $config
     * @return Facebook_Api_Base
     */
    public function setConfig(Facebook_Config $config)
    {
        $this->_config = $config;
        return $this;
    }

    /**
     * Makes API call to Facebook Graph
     * - First parameter is a Graph call, like -> '/me'
     * - Second parameter is optional array of parameters
     * - Third parameter is optional boolean to indicate whether the request will be POST
     *
     * @return array
     * @throws Facebook_Api_Exception
     */
    public function apiCall()
    {
        $args                    = func_get_args();
        $args[1]['access_token'] = $this->getConfig()->getAccessToken();

        $response = call_user_func_array(array($this, '_callGraphApi'), $args);
        $response = Zend_Json::decode($response->getBody());

        if (isset($response['error']) and $response['error']['type'] == 'OAuthException') {
            // try to redeem a new "access_token"
            $this->redeemAccessToken();

            // try to call Graph API again
            $response = call_user_func_array(array($this, '_callGraphApi'), $args);
            $response = Zend_Json::decode($response->getBody());
        }

        if (isset($response['error'])) {
            // if problem still persist, just give up
            throw new Facebook_Api_Exception($response['error']);
        }

        return $response;
    }

    /**
     * Invoke Facebook's Graph API call
     *
     * @param string     $path
     * @param array|null $params
     * @param bool       $isPost
     * @return Zend_Http_Response
     */
    protected function _callGraphApi($path, array $params = null, $isPost = false)
    {
        foreach ($params as $key => $value) {
            if (!is_string($value)) {
                $params[$key] = Zend_Json::encode($value);
            }
        }

        // prepare the request
        $request = new Zend_Http_Client(self::GRAPH_URL . '/' . trim($path, '/'));

        if (false === $isPost) {
            $request->setParameterGet($params);
        } else {
            $request->setMethod(Zend_Http_Client::POST);
            $request->setParameterPost($params);
        }

        // return the response we got
        return $request->request();
    }

    /**
     * Tries to redeem new "access_token"
     *
     * @param void
     * @return string
     * @throws Facebook_Exception
     */
    public function redeemAccessToken()
    {
        $args = array(
            'client_id'     => $this->getConfig()->getAppId(),
            'redirect_uri'  => $this->getConfig()->getRedirectUrl(),
            'client_secret' => $this->getConfig()->getAppSecret(),
            'code'          => $this->getConfig()->getToken(),
        );

        $response     = $this->_callGraphApi(self::ACCESS_TOKEN_REDEEM_CALL, $args);
        $responseBody = $response->getBody();

        // check if response is not access_token
        if (false !== strpos($responseBody, 'access_token=')) {
            $accessToken = trim(str_replace('access_token=', '', $responseBody));

            // we don't need "expire" value, so just get "access_token"
            $accessToken = explode('&', $accessToken);

            // save into configuration
            $this->getConfig()->setAccessToken($accessToken[0]);

            if (method_exists($this, 'redeemAccessTokenCallback')) {
                // you can define a callback method once new accessToken has been re-issued
                // there you can update it say in identity object
                call_user_func_array(array($this, 'redeemAccessTokenCallback'), array($accessToken[0]));
            }

            return $accessToken[0];
        } else {
            throw new Facebook_Exception('Could not redeem new "access_token"');
        }
    }
}