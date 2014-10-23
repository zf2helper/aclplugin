<?php

use \AclPlugin\Service\AclPermissionConfig as AclConfig;

return array(
    'controller_plugins' => array(
        'invokables' => array(
            'AclPlugin' => 'AclPlugin\Controller\Plugin\AclPluginPlugin',
        )
    ),
    'acl_config' => array(
        'defaults' => array(
            'access' => 'allow', // "allow" or "deny"
            'loginRoute' => 'app/login', //Login route
            'statusCode' => 302, //Status code
            'guestRoleName' => 'guest',
            'roles' => array(
            )
        ),
        'modules' => array(
        ),
        'permissions' => array(
        )
    )
);
