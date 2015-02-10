<?php

namespace AclPlugin\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin,
    Zend\Session\Container as SessionContainer,
    Zend\Console\Request as ConsoleRequest,
    Zend\Permissions\Acl\Acl,
    Zend\Permissions\Acl\Role\GenericRole as Role,
    Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Router\RouteMatch as RouteMatch;

use \AclPlugin\Service\AclPermissionConfig as AclConfig;


class AclPluginPlugin extends AbstractPlugin implements ServiceManagerAwareInterface
{

    protected $sesscontainer;
    protected $sm;
    protected $config;

    public function getSessContainer($e)
    {
        $session = new SessionContainer('user');
        if (!$session->user) {
            $authenticationService = $this->sm->get('Zend\Authentication\AuthenticationService');
            $session->user = $authenticationService->getIdentity();
        }

        return $session->user;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager $serviceManager
     * @return void
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->sm = $serviceManager;
    }

    public function setConfig(\Zend\Config\Config $config)
    {
        $this->config = $config;
        return $this;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function doAuthorization($e)
    {
        //Allow console routes
        if ($e->getRequest() instanceof ConsoleRequest) return;
        
        // set ACL
        $acl = new Acl();

        if ($this->getConfig()->defaults->access === 'deny') {
            $acl->deny();
        } else {
            $acl->allow();
        }

        $this->setRoles($acl);
        $this->setModules($acl);
        $this->setPermissions($acl);

        $controller = $e->getTarget();
        $controllerClass = get_class($controller);
        $moduleName = strtolower(substr($controllerClass, 0, strpos($controllerClass, '\\')));
        $role = ($this->getSessContainer($e) == null ) ? 'guest' : $this->getSessContainer($e)->getRole();
        $routeMatch = $e->getRouteMatch();

        $actionName = strtolower($routeMatch->getParam('action', 'not-found')); // get the action name  
        $controllerName = $routeMatch->getParam('controller', 'not-found');     // get the controller name      
        $controllerName = explode('\\', $controllerName);
        $controllerName = array_pop($controllerName);
        $controllerName = strtolower($controllerName);

        if ((!$acl->isAllowed($role, $moduleName, $controllerName . ':' . $actionName) 
                || !$acl->isAllowed($role, $moduleName, $controllerName)) 
                && $routeMatch->getMatchedRouteName() !== $this->getConfig()->defaults->loginRoute) {
            $this->goToLogin($e, $role);
        }
    }

    public function setRoles(Acl $acl)
    {
        $acl->addRole(new Role($this->getConfig()->defaults->guestRoleName));
        foreach ($this->getConfig()->defaults->roles as $role => $subRole) {
            if (is_numeric($role)) {
                $acl->addRole(new Role($subRole), $this->getConfig()->defaults->guestRoleName);
            } else {
                $acl->addRole(new Role($role), new Role($subRole));
            }
        }
    }

    public function setModules(Acl $acl)
    {
        foreach ($this->getConfig()->modules as $module) {
            $acl->addResource($module);
        }
    }

    public function setPermissions(Acl $acl)
    {
        foreach ($this->getConfig()->permissions as $role => $type) {
            if ($type->allow) {
                foreach ($type->allow as $allow) {
                    $acl->allow($role, $allow->get('resource'), $allow->get('params'));
                }
            }
            if ($type->deny) {
                foreach ($type->deny as $deny) {
                    $acl->deny($role, $deny->get('resource'), $deny->get('params'));
                }
            }
        }
    }

    public function goToLogin($e, $role)
    {
        $router = $e->getRouter();
        $response = $e->getResponse();
        if ($this->getConfig()->permissions->$role->failRoute) {
            $url = $router->assemble(array(), array('name' => $this->getConfig()->permissions->$role->failRoute));
        } else {
            $url = $router->assemble(array(), array('name' => $this->getConfig()->defaults->loginRoute));
        }
        $response->setStatusCode($this->getConfig()->defaults->statusCode);
        // redirect to login page or other page.
        $response->getHeaders()->addHeaderLine('Location', $url);

        $e->setResponse($response);
        $e->stopPropagation();
    }

}
