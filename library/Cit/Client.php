<?php

/*use Cit_Error_UnhandledError;
use Cit_Error_AuthenticationError;
use Cit_Error_MethodNotAllowed;
use Cit_Error_NotFound;
use Cit_Error_InvalidApiKey;
use Cit_Error_UnableToConnect;*/


class Cit_Client extends Cit_Error_UnhandledError
{
    /**
    * La versión de API usada
    */
    const API_VERSION = "v1.2";
    /**
     * La URL Base por defecto
     */
    const BASE_URL = "https://integ-pago.culqi.com/api/v1";
    public function request($method, $url, $api_key, $data = NULL, $headers= array("Content-Type" => "application/json", "Accept" => "application/json") ) {
        try {
            $options = array(
                'auth' => new Cit_AuthBearer($api_key),
                'timeout' => 120
            );
            if($method == "GET") {
                $url_params = is_array($data) ? '?' . http_build_query($data) : '';
                $response = Request::get(Cit_Culqi::$api_base . $url . $url_params, $headers, $options);
            } else if($method == "POST") {
                $response = Request::post(Cit_Culqi::$api_base . $url, $headers, json_encode($data), $options);
            } else if($method == "PATCH") {
                $response = Request::patch(Cit_Culqi::$api_base . $url, $headers, json_encode($data), $options);
            } else if($method == "DELETE") {
                $response = Request::delete(Cit_Culqi::$api_base, $options);
            }
            //var_dump($response);die;
        } catch (Exception $e) {
            throw new Cit_Error_UnableToConnect;
        }
        //if ($response->status_code >= 200 && $response->status_code <= 206) {
            /*if ($method == "DELETE") {
                return $response->status_code == 204 || $response->status_code == 200;
            }*/
            return json_decode($response->body);
        //}
        /*
            string(230) "{"tipo":"error_tarjeta","codigo":"expiracion_invalida","mensaje_usuario":"La fecha de expiración de su tarjeta es inválida. Intente nuevamente ó utilice otra tarjeta.","objeto":"error","mensaje":"Fecha de Expiracion Invalida."}"
             *              */
        /*if ($response->status_code == 400) {
            $code = 0;
            $message = "";
            throw new Cit_Error_UnhandledError($response->body, $response->status_code);            
        }
        if ($response->status_code == 401) {
            throw new Cit_Error_AuthenticationError();
        }
        if ($response->status_code == 404) {
            throw new Cit_Error_NotFound();
        }
        if ($response->status_code == 403) {
            throw new Cit_Error_InvalidApiKey();
        }
        if ($response->status_code == 405) {
            throw new Cit_Error_MethodNotAllowed();
        }
        throw new Cit_Error_UnhandledError($response->body, $response->status_code);*/
    }
}