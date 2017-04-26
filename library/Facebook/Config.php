<?php

/**
 * @author  Laurynas Karvelis <laurynas.karvelis@gmail.com>
 * @author  Explosive Brains Limited
 * @license http://sam.zoy.org/wtfpl/COPYING
 */
class Facebook_Config
{
    /**
     * @var string Facebook Application Id
     */
    private $_appId;

    /**
     * @var string Facebook Application Secret
     */
    private $_appSecret;

    /**
     * @var string App's Redirect URL
     */
    private $_redirectUrl;

    /**
     * @var string App Permissions
     */
    private $_permissions;

    /**
     * @var string Access Token for this user
     */
    private $_accessToken;

    /**
     * @var string Token for this user
     */
    private $_token;

    /**
     * Constructor
     *
     * @param void
     * @return Facebook_Config
     */
    public function __construct()
    {
        $config = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('facebook');
        $this->setOptions($config);
    }

    /**
     * Sets Facebook configuration values to their properties
     *
     * @param array $options
     * @return Facebook_Config
     * @throws Facebook_Config_Exception
     */
    public function setOptions(array $options)
    {
        $requiredProperties = array('appId', 'appSecret', 'redirectUrl', 'permissions');

        foreach ($requiredProperties as $property) {
            if (empty($options[$property])) {
                throw new Facebook_Config_Exception('Required value for param facebook.' . $property . ' is missing in application.ini');
            }
        }

        foreach ($options as $key => $value) {
            if (property_exists($this, '_' . $key)) {
                $setter = 'set' . ucfirst($key);
                $this->{$setter}($value);
            }
        }

        return $this;
    }

    /**
     * Sets AppId
     *
     * @param string $appId
     * @return Facebook_Config
     */
    public function setAppId($appId)
    {
        $this->_appId = (string) $appId;
        return $this;
    }

    /**
     * Returns AppId
     *
     * @param void
     * @return string
     */
    public function getAppId()
    {
        return $this->_appId;
    }

    /**
     * Sets AppSecret
     *
     * @param string $appSecret
     * @return Facebook_Config
     */
    public function setAppSecret($appSecret)
    {
        $this->_appSecret = (string) $appSecret;
        return $this;
    }

    /**
     * Returns AppSecret
     *
     * @param void
     * @return string
     */
    public function getAppSecret()
    {
        return $this->_appSecret;
    }

    /**
     * Sets Permissions
     *
     * @param string $permissions
     * @return Facebook_Config
     */
    public function setPermissions($permissions)
    {
        $this->_permissions = (string) $permissions;
        return $this;
    }

    /**
     * Returns Permissions
     *
     * @param void
     * @return string
     */
    public function getPermissions()
    {
        return $this->_permissions;
    }

    /**
     * Sets Redirect URL
     *
     * @param string $redirectUrl
     * @return Facebook_Config
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->_redirectUrl = (string) $redirectUrl;
        return $this;
    }

    /**
     * Returns Redirect URL
     *
     * @param void
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->_redirectUrl;
    }

    /**
     * Sets user's Access Token (access_token)
     *
     * @param string $accessToken
     * @return Facebook_Config
     */
    public function setAccessToken($accessToken)
    {
        $this->_accessToken = (string) $accessToken;
        return $this;
    }

    /**
     * Returns user's Access Token (access_token)
     *
     * @param void
     * @return string
     */
    public function getAccessToken()
    {
        return $this->_accessToken;
    }

    /**
     * Sets user's Token (code parameter)
     *
     * @param string $token
     * @return Facebook_Config
     */
    public function setToken($token)
    {
        $this->_token = (string) $token;
        return $this;
    }

    /**
     * Returns user's Token (code parameter)
     *
     * @param void
     * @return string
     */
    public function getToken()
    {
        return $this->_token;
    }
}