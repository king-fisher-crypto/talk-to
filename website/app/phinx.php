<?php
require dirname(__FILE__) . '/Config/database.php';

$cfg = new DATABASE_CONFIG();
$cfg = $cfg->default;

return
[
    'paths' => [
        'migrations' => dirname(__FILE__) . '/db/migrations',
        'seeds' => dirname(__FILE__) . '/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'development' => [
            'adapter' => 'mysql',
            'host' => $cfg['host'],
            'name' => $cfg['database'],
            'user' => $cfg['login'],
            'pass' => $cfg['password'],
            'port' => '3306',
            'charset' => $cfg['encoding'],
        ],
    ],
    'version_order' => 'creation'
];
