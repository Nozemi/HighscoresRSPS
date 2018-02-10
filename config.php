<?php
    const localConfig   = 0;
    const vBulletin     = 1;
    const MyBB          = 2;

    $dbConfig = vBulletin;

    // Highscores
    $playersPerPage = 25;

    $skills = [
        'attack', 'defence', 'strength', 'hitpoints',
        'prayer', 'magic', 'ranged', 'woodcutting', 'firemaking',
        'fletching', 'mining', 'smithing', 'agility', 'thieving',
        'runecrafting', 'slayer', 'farming', 'crafting', 'herblore',
        'fishing', 'cooking'
    ];

    if($dbConfig == localConfig) {
        // Only necessary to change details below if $dbConfig is set to 'localConfig' or 0.
        $databaseDetails = (object) [
            'host'      => '<DB_HOST>',
            'port'      => 3306,
            'name'      => '<DB_NAME>',
            'user'      => '<DB_USER>',
            'pass'      => '<DB_PASS>',
            'prefix'    => '<DB_PREFIX>'
        ];
    }

    if($dbConfig == vBulletin) {
        include(__DIR__ . '/../includes/config.php');
        if (!isset($config['Database']) || !isset($config['MasterServer'])) {
            die('Failed to get vBulletin config.');
        }

        /** @var array $config */

        $databaseDetails = (object) [
            'name'   => $config['Database']['dbname'],
            'user'   => $config['MasterServer']['username'],
            'pass'   => $config['MasterServer']['password'],
            'host'   => $config['MasterServer']['servername'],
            'port'   => $config['MasterServer']['port'],
            'prefix' => $config['Database']['tableprefix']
        ];
    }

    $GLOBALS['dbDetails']           = $databaseDetails;

    $GLOBALS['scores']['skills']    = $skills;
    $GLOBALS['scores']['perPage']   = $playersPerPage;