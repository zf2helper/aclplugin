<?php
namespace AclPlugin\Service;

class AclPermissionConfig
{
    private $resource;
    private $params;
    
    
    public function __construct($params)
    {
        $this->resource = $params[0];
        $this->params = $params[1];
    }
    
    public function getResource()
    {
        return strtolower($this->resource);
    }
    public function getParams()
    {
        return ($this->params) ? strtolower($this->params) : null;
    }
}
