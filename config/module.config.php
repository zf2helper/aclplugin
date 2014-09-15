<?php

use \AclPlugin\Service\AclPermissionConfig as AclConfig;

return array(
    'controller_plugins' => array(
        'invokables' => array(
            'AclPlugin' => 'AclPlugin\Controller\Plugin\AclPluginPlugin',
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Authentication\AuthenticationService' => function($serviceManager) {
                return $serviceManager->get('doctrine.authenticationservice.orm_default');
            },
        ),
    ),
    'acl_config' => array(
        'defaults' => array(
            'access' => 'deny', // "allow" or "deny"
            'loginRoute' => 'app/login', //Login route
            'statusCode' => 302, //Status code
            'guestRoleName' => 'guest',
            'roles' => array(
            )
        ),
        'modules' => array(
        ),
        'permissions' => array(
            'guest' => array(
                'allow' => array(
                    new AclConfig('application'),
                ),
            ),
        )
    )
);
