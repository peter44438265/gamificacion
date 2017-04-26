<?php

class Cit_Planes extends Cit_Resource {
    const URL_PLANES = "/planes/";
    public function create($options = NULL)
    {
        return $this->request("POST", Cit_Planes::URL_PLANES, $api_key = $this->culqi->api_key, $options);
    }
    public function getList($options)
    {
        return $this->request("GET", Cit_Planes::URL_PLANES, $api_key = $this->culqi->api_key, $options);
    }
    public function get($id)
    {
        return $this->request("GET", Cit_Planes::URL_PLANES . $id . "/", $api_key = $this->culqi->api_key);
    }
    public function delete($id)
    {
       return $this->request("DELETE", Cit_Planes::URL_PLANES . $id . "/", $api_key = $this->culqi->api_key);
   }
}