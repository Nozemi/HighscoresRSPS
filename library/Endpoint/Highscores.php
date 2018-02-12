<?php
namespace Endpoint;

use Database\DBUtilQuery;
use Objects\Player\Player;
use Objects\Player\Skill;

use Objects\TopList\Skill as TopSkill;

use Objects\TopList\Total;
use Utilities\JsonData;

class Highscores extends AbstractEndpoint {

    const GET_USER = 1;
    const GET_USER_BY_ID = 2;
    const GET_USER_BY_NAME = 3;

    const GET_PLAYER_SKILL = 4;

    const GET_TOP = 5;

    // Indexes
    const INDEX_PLAYER = 0;
    const INDEX_TOP    = 0;

    const INDEX_PLAYER_BY_ID   = 1;
    const INDEX_PLAYER_BY_NAME = 1;

    const INDEX_PLAYER_NAME = 2;
    const INDEX_PLAYER_ID   = 2;

    const INDEX_SKILL = 3;
    const INDEX_SKILL_NAME = 4;

    protected $type;

    /**
     * Highscores constructor.
     * @param null $params
     * @throws \Exception
     */
    public function __construct($params = null) {
        parent::__construct($params);

        $this->type = null;
        if(strtolower($this->getParam(self::INDEX_PLAYER)) == 'user') {
            $this->type = self::GET_USER;
        }

        if($this->getParam(self::INDEX_TOP) == 'top') {
            $this->type = self::GET_TOP;
        }

        if($this->type == self::GET_TOP) {
            $skill = $this->getParam(1);

            $minCombat = null;
            $maxCombat = null;

            $limit       = $GLOBALS['scores']['perPage'];
            $offset      = 0;
            $currentPage = 1;

            if($this->getRequest('minCombat')) {
                $minCombat = $this->getRequest('minCombat');
            }

            if($this->getRequest('maxCombat')) {
                $maxCombat = $this->getRequest('maxCombat');
            }

            if($this->getRequest('limit')) {
                $limit = $this->getRequest('limit');
            }

            if($this->getRequest('offset')) {
                $offset = $this->getRequest('offset') - 1;
                $currentPage = $offset + 1;
            }

            $jsonData = null;

            if($skill == 'total') {
                if($this->getParam(3)) {
                    $offset = $this->getParam(3) - 1;
                    $currentPage = $offset + 1;
                }

                if($this->getParam(4)) {
                    $limit = $this->getParam(4);
                }

                $type = $this->getParam(2);
                $total = new Total($type, $maxCombat, $minCombat);
                $total->setOffset($offset)
                    ->setLimit($limit);
                $players = $total->getTotal();

                $jsonData = [
                    'currentPage'     => $currentPage,
                    'availablePages'  => round(($this->getPlayerAmount() / $limit), 0),
                    'playersShown'    => (int) $limit,
                    'orderedBy'       => 'Total ' . ucfirst($type),
                    'availableSkills' => $GLOBALS['scores']['skills']
                ];

                foreach($players as $player) {
                    /** @var Player $player */
                    $jsonData['players'][] = $player->getInfo(false);
                }
            } else {
                if($this->getParam(2)) {
                    $offset = $this->getParam(2) - 1;
                    $currentPage = $offset + 1;
                }

                if($this->getParam(3)) {
                    $limit = $this->getParam(3);
                }

                $skill = new TopSkill($skill);
                $skill->setMinCombat($minCombat)
                    ->setMaxCombat($maxCombat);
                $players = $skill->getTopList($limit, ($offset * $limit));

                $jsonData = [
                    'currentPage'  => $currentPage,
                    'availablePages' => round(($this->getPlayerAmount() / $limit), 0),
                    'playersShown' => (int) $limit,
                    'orderedBy'    => ucfirst($skill->getSkill()),
                    'availableSkills' => $GLOBALS['scores']['skills']
                ];

                foreach ($players as $player) {
                    /** @var Player $player */
                    $playerSkill = $player->getSkill($skill->getSkill());
                    $playerInfo  = $player->getInfo(false);

                    $jsonData['players'][] = array_merge($playerInfo, [
                        'skill' => $playerSkill->getInfo(true),
                        $playerSkill->getName() => $playerSkill->getInfo()
                    ]);
                }
            }

            $success = new JsonData(JsonData::SUCCESS, "Successfully got the requested top list.", $jsonData);
            echo $success->getMessage();
        }

        if($this->type == self::GET_USER) {
            $player = null;
            if(strtolower($this->getParam(self::INDEX_PLAYER_BY_NAME)) == 'name') {
                $this->type = self::GET_USER_BY_NAME;
                $player = $this->getUserByName($this->getParam(self::INDEX_PLAYER_NAME));
            } else if(strtolower($this->getParam(self::INDEX_PLAYER_BY_ID)) == 'id') {
                $this->type = self::GET_USER_BY_ID;
                $player = $this->getUserById($this->getParam(self::INDEX_PLAYER_ID));
            }

            if($player instanceof Player) {
                if(strtolower($this->getParameter(self::INDEX_SKILL)) == 'skill') {
                    $this->type = self::GET_PLAYER_SKILL;
                }

                if($this->type == self::GET_PLAYER_SKILL) {
                    $skill = $player->getSkill($this->getParameter(self::INDEX_SKILL_NAME));
                    if($skill instanceof Skill) {
                        $skillArray = [
                            'id'   => $player->getId(),
                            'name' => $player->getName(),
                            $skill->getName() => $skill->getInfo(),
                            'skill' => $skill->getInfo(true)
                        ];

                        $data = new JsonData(JsonData::SUCCESS, 'Successfully got player skill data.', $skillArray);
                        echo $data->getMessage();
                    } else {
                        $error = new JsonData(JsonData::ERROR_NOT_FOUND,'Skill not found. (' . $skill . ')');
                        echo $error->getMessage();
                    }
                } else {
                    $playerArray = $player->getInfo();
                    $data = new JsonData(JsonData::SUCCESS, 'Successfully got player data.', $playerArray);
                    echo $data->getMessage();
                }
            } else {
                $error = new JsonData(JsonData::ERROR_NOT_FOUND,'Player not found.');
                echo $error->getMessage();
            }
        }

        return false;
    }

    /**
     * @param $id
     * @return Player
     * @throws \Exception
     */
    public function getUserById($id) {
        $player = new Player($id);
        $player->getPlayerById($GLOBALS['db']);

        return $player;
    }

    /**
     * @param $name
     * @return Player
     * @throws \Exception
     */
    public function getUserByName($name) {
        $player = new Player(null, $name);
        $player->getPlayerByName($GLOBALS['db']);

        return $player;
    }

    public function getPlayerAmount() {
        $playerAmountQuery = new DBUtilQuery();
        $playerAmountQuery->setName('playerAmount')
            ->setMultipleRows(false)
            ->setQuery("
                SELECT
                    COUNT(*) `playerCount`
                FROM `character_stats` `S`
                WHERE NOT `S`.`rights` = 2
            ")
            ->setDBUtil($GLOBALS['db'])
            ->execute();
        $result = $playerAmountQuery->result();

        return $result['playerCount'];
    }
}