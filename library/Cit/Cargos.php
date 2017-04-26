<?php

class Cit_Cargos extends Cit_Resource {
    const URL_CARGOS = "/cargos/";
    public function create($options = NULL)
    {
        return $this->request("POST", Cit_Cargos::URL_CARGOS, $api_key = $this->culqi->api_key, $options);
    }
    public function getList($options = NULL)
    {
        return $this->request("GET", Cit_Cargos::URL_CARGOS, $api_key = $this->culqi->api_key, $options);
    }
    public function get($id)
    {
        return $this->request("GET", Cit_Cargos::URL_CARGOS . $id . "/", $api_key = $this->culqi->api_key);
    }
}