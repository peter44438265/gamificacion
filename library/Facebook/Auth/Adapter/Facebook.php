<?php

class Facebook_Auth_Adapter_Facebook implements Zend_Auth_Adapter_Interface {

    private $token = null;
    private $user = null;
    private $_data = array();
    private $_friends = array();

    public function __construct($token) {
        $this->token = $token;
    }

    public function getUser() {
        return $this->user;
    }

    public function authenticate() {
        if ($this->token == null) {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
                    false, array('Token was not set'));
        }
        /*$facebook = new Facebook(array(
                'appId' => '640122879376721',
                'secret' => 'a2e7c6cfa7e20892d22bf14eb58ee0ae',
                'cookie' => true,
            ));*/
        $graph_url = "https://graph.facebook.com/me?access_token=" . $this->token;
        $friends_url = "https://graph.facebook.com/me/friends?access_token=" . $this->token;
        $details = json_decode(file_get_contents($graph_url));
        $friends=json_decode(file_get_contents($friends_url));
        $this->_data = $details;
        $this->_friends = $friends;
        //var_dump($details->email);exit;
        //$user = lookUpUserInDB($details->email); // NOT AN ACTUALL FUNCTION
        //if ($user == false) { // first time login, register user
            //registerUser($user) // NOT AN ACTUAL FUNCTION
        //}
        $this->user = $details->email;
        return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $details->email);
    }
    public function getMe(){
        return (array)$this->_data;
    }
    
    public function getFriends(){
        return (array)$this->_friends;
    }

}