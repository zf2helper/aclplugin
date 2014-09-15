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
                'Webmaster',
                'Advertiser',
                'Administrator'
            )
        ),
        'modules' => array(
            'application',
            'admin',
            'format',
            'doctrineormmodule',
            'maintenance',
            'zftool',
        ),
        'permissions' => array(
            'guest' => array(
                'allow' => array(
                    new AclConfig('application', 'index'),
                    new AclConfig('format')
                ),
            ),
            'Advertiser' => array(
                'allow' => array(
                    new AclConfig('application', 'Advertiser'),
                    new AclConfig('application', 'Advert')
                ),
                'failRoute' => 'app/contactus'
            ),
            'Webmaster' => array(
                'allow' => array(
                    new AclConfig('application', 'Webmaster'),
                    new AclConfig('application', 'Portal')
                ),
                'failRoute' => 'app/contactus'
            ),
            'Administrator' => array(
                'allow' => array(
                    new AclConfig('admin'),
                )
            )
        )
    )
);
