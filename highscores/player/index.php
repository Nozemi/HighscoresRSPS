<?php
/**
 * Get information about player
 *
 * This API accepts a PlayerID or a PlayerName
 */
require_once(__DIR__ . '/../../library/objects/database/DBUtilException.php');
require_once(__DIR__ . '/../../library/objects/database/DBUtilQuery.php');
require_once(__DIR__ . '/../../library/objects/database/DBUtil.php');

require_once(__DIR__ . '/../../library/objects/Player.php');
require_once(__DIR__ . '/../../library/objects/Skill.php');

include(__DIR__ . '/../../library/config.php');

if(!isset($_REQUEST['name']) && !isset($_REQUEST['id'])) {
    die("You need to specify an ID or a Username.");
}

$player = new Player();
$getById = false;

if(isset($_REQUEST['id'])) {
    if(!filter_var($_REQUEST['id'], FILTER_VALIDATE_INT)) {
        die("ID needs to be an integer.");
    }

    $player->setId((int) $_REQUEST['id']);
    $player->getPlayerById($GLOBALS['db']);
} else {
    $player->setName($_REQUEST['name']);
    $player->getPlayerByName($GLOBALS['db']);
}

if($player->getName() == null || $player->getId() === null) {
    die("Player not found.");
}

$playerArray = [
    'id'                => $player->getId(),
    'name'              => $player->getName(),
    'combat'            => $player->getCombat(),
    'totalLevel'        => $player->getTotalLevel(),
    'totalExperience'   => $player->getTotalExperience()
];

foreach($player->getSkills() as $skill) {
    /** @var Skill $skill */
    $playerArray['skills'][$skill->getName()] = [
        'level'      => $skill->getLevel(),
        'experience' => $skill->getExperience()
    ];
}

if(!isset($_REQUEST['skill'])) {
    echo json_encode($playerArray, JSON_PRETTY_PRINT);
} else {
    $skill = $player->getSkill($_REQUEST['skill']);
    if($skill instanceof Skill) {
        $skillArray = [
            'username' => $player->getName(),
            $skill->getName() => [
                'level'      => $skill->getLevel(),
                'experience' => $skill->getExperience()
            ]
        ];

        echo json_encode($skillArray, JSON_PRETTY_PRINT);
    } else {
        die("Didn't find the skill {$_REQUEST['skill']}.");
    }
}