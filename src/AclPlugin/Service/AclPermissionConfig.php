<?php
namespace AclPlugin\Service;

class AclPermissionConfig
{
    private $resource;
    private $params;
    
    
    public function __construct($resource, $params = null)
    {
        $this->resource = $resource;
        $this->params = $params;
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
