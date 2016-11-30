<?php

return array(
    'default-connection' => 'concrete',
    'connections' => array(
        'concrete' => array(
            'driver' => 'c5_pdo_mysql',
            'server' => 'db',
            'database' => 'aclsnat_c5',
            'username' => 'aclsnat_c5user',
            'password' => 'PASSWORD',
            'charset' => 'utf8',
        ),
    ),
);
