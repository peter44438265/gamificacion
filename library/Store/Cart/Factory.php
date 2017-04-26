<?php

abstract class Store_Cart_Factory
{
	const ADAPTER_NAMESPACE = 'Store_Cart_Abstract_';    
	/**
	 *
	 * @param String $session
	 * @return Store_Cart_Abstract_StandardCart
	 */
	static public function createInstance($session = null)
	{
        $adapterName = 'StandardCart';
		if (!is_string($adapterName) || !strlen($adapterName)) {
			throw new Exception('Adapter Cart name must be specified in a string');
		}

		$classEngine = self::ADAPTER_NAMESPACE . $adapterName;

		Zend_Loader::loadClass($classEngine);
        if ($session instanceof Zend_Session_Namespace) {
            $sessionData = $session;
            Zend_Registry::set('session', $sessionData);
        } else {
            if (Zend_Registry::isRegistered('session')) {
                $sessionData = Zend_Registry::get('session');
            } else {                
                $sesionName = "cartSession";
                $sessionData = new Zend_Session_Namespace($sesionName);
                Zend_Registry::set('session', $sessionData);
            }
        }
		
		if (isset($sessionData->cart) && ($sessionData->cart !== null) ) {
			$cartObject = $sessionData->cart;
		} else {
			if (class_exists($classEngine, false)) {
				$cartObject = call_user_func(array($classEngine, 'getInstance'));
			} else {
				throw new Exception ("Adapter '$classEngine' not found");
			}
			$sessionData->cart = $cartObject;
		}
		if (!$cartObject instanceof Store_Cart_Abstract) {
			throw new Exception("Adapter class '$classEngine' does not extend Store_Cart_Abstract");
		}
        
		return $cartObject;
	}    
}
