<?php
    $GLOBALS['skills'] = [
        'attack', 'defence', 'strength', 'hitpoints',
        'prayer', 'magic', 'ranged', 'woodcutting', 'firemaking',
        'fletching', 'mining', 'smithing', 'agility', 'thieving',
        'runecrafting', 'slayer', 'farming', 'crafting', 'herblore',
        'fishing', 'cooking'
    ];

    /* vBulletin Database Configuration */
    /**/    include(__DIR__ . '/../../includes/config.php');
    /**/
    /**/    if(!isset($config['Database']) || !isset($config['MasterServer'])) {
    /**/        die('Failed to get vBulletin config.');
    /**/    }
    /**/
    /**/    $databaseDetails = (object) [
    /**/        'name' => $config['Database']['dbname'],
    /**/        'user' => $config['MasterServer']['username'],
    /**/        'pass' => $config['MasterServer']['password'],
    /**/        'host' => $config['MasterServer']['servername'],
    /**/        'port' => $config['MasterServer']['port'],
    /**/        'prefix' => $config['Database']['tableprefix']
    /**/    ];
    /* END vBulletin Database Configuration */


    $database = new DBUtil($databaseDetails);

    $GLOBALS['db'] = $database;
    $GLOBALS['dbDetails'] = $databaseDetails;