<?php

/**
 * @author  Laurynas Karvelis <laurynas.karvelis@gmail.com>
 * @author  Explosive Brains Limited
 * @license http://sam.zoy.org/wtfpl/COPYING
 */
class Facebook_Auth_Adapter implements Zend_Auth_Adapter_Interface
{
    /**
     * @var Facebook_Config
     */
    private $_config;

    /**
     * @var Zend_Session_Namespace The place were our secret "state" will be stored
     */
    private $_session;

    /**
     * Constructor
     *
     * @throws Zend_Auth_Exception
     * @return Facebook_Auth_Adapter
     */
    public function __construct()
    {
        $this->setConfig(new Facebook_Config());
        $this->_session = new Zend_Session_Namespace('FB');
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
     * @return Facebook_Auth_Adapter
     */
    public function setConfig(Facebook_Config $config)
    {
        $this->_config = $config;
        return $this;
    }

    /**
     * Sets the "token" value
     *
     * @param string $token
     * @return void
     */
    public function setToken($token)
    {
        $this->getConfig()->setToken($token);
    }

    /**
     * Generates new unique state value and stores it in session
     *
     * @param void
     * @return void
     */
    private function _generateState()
    {
        $this->_session->state = md5(uniqid(rand(), TRUE) . $this->getConfig()->getAppId());
    }

    /**
     * Returns generated state value
     *
     * @param void
     * @return string
     */
    private function _getState()
    {
        return $this->_session->state;
    }

    /**
     * Exchanges "token" with "access_token"
     *
     * @param void
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        if (!strlen($this->getConfig()->getToken())) {
            // token was not provided
            return new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
                false,
                array('Token is required to begin authentication')
            );
        }

        if ($_REQUEST['state'] === $this->_getState()) {
            // state is OK
            try {
                $api = new Facebook_Api_Base();
                $api->getConfig()->setToken($this->getConfig()->getToken());

                $accessToken = $api->redeemAccessToken();
                $this->getConfig()->setAccessToken($accessToken);

                $result = new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $accessToken);
            } catch (Facebook_Exception $e) {
                $result = new Zend_Auth_Result(
                    Zend_Auth_Result::FAILURE,
                    false,
                    array('Could not get "access_token"')
                );
            }

            // remove generated "state"
            $this->_session->unsetAll();
        } else {
            // possibly someone is playing with CSRF
            $result = new Zend_Auth_Result(
                Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS,
                false,
                array('Possible CSRF attack')
            );
        }

        return $result;
    }

    /**
     * Redirects to Facebook's Application authorisation page to get "token" or in other words -> "code"
     *
     * @param void
     * @return void
     */
    public function redirect()
    {
        // generate new state and save it for authentication
        $this->_generateState();

        // build arguments
        $args = array(
            'client_id'     => $this->getConfig()->getAppId(),
            'redirect_uri'  => $this->getConfig()->getRedirectUrl(),
            'scope'         => $this->getConfig()->getPermissions(),
            'state'         => $this->_getState(),
        );

        // construct URL and follow it
        $url = 'https://www.facebook.com/dialog/oauth?' . http_build_query($args, null, '&');
        echo('<script type="text/javascript">top.location.href="' . $url . '"</script>');
        exit;
    }
}