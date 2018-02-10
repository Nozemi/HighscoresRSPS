<?php
namespace Objects\TopList;

use Database\DBUtilQuery;
use Objects\Player\Player;
use Utilities\JsonData;
use \PDO;

class Skill {
    protected $skill;

    protected $maxCombat;
    protected $minCombat;

    public function __construct($skill = null) {
        $this->skill = $skill;
    }

    public function setSkill($skill) {
        $this->skill = $skill;
    }

    public function setMinCombat($minCombat) {
        $this->minCombat = $minCombat;
        return $this;
    }

    public function setMaxCombat($maxCombat) {
        $this->maxCombat = $maxCombat;
        return $this;
    }

    /**
     * Get's the top players of the given skill.
     *
     * @param int $limit
     * @param int $offset
     * @return array
     * @throws \Exception
     */
    public function getTopList($limit = 0, $offset = 0) {
        if(strlen($this->skill) <= 0) {
            throw new \Exception('You need to specify a skill before trying to get the top players.');
        }

        $result = null;
        if(in_array(strtolower($this->skill), $GLOBALS['skills'])) {

            $query = new DBUtilQuery();
            $query->setName('topList')
                ->setMultipleRows(true)
                ->setDBUtil($GLOBALS['db'])
                ->addParameter(':limit', (int)$limit, \PDO::PARAM_INT)
                ->addParameter(':offset', (int)$offset, \PDO::PARAM_INT);

            $queryString = "
                SELECT
                     `U`.`username`
                    ,`S`.*
                FROM `character_stats` `S`
                    INNER JOIN `user` `U` ON `U`.`userid` = `S`.`uid`
                WHERE NOT `S`.`rights` = 2 
            ";

            if ($this->maxCombat >= 3) {
                $queryString .= " AND `S`.`combat` <= :maxCombat ";
                $query->addParameter(':maxCombat', (int) $this->maxCombat, PDO::PARAM_INT);
            }

            if ($this->minCombat >= 3) {
                $queryString .= " AND `S`.`combat` >= :minCombat ";
                $query->addParameter(':minCombat', (int) $this->minCombat, PDO::PARAM_INT);
            }

            $queryString .= "
                ORDER BY `S`.`{$this->skill}` DESC
                LIMIT :offset,:limit
            ";

            $query->setQuery($queryString)
                ->execute();

            $result = $query->result();
        }
        if(empty($result)) {
            $error = new JsonData(JsonData::ERROR_NOT_FOUND, 'Skill not found. (' . $this->skill . ')');
            echo $error->getMessage();
            exit;
        }

        $players = [];
        foreach($result as $playerData) {
            $player = new Player($playerData['uid']);
            $player->setName($playerData['username'])
                ->getPlayerByName($GLOBALS['db']);
            $players[] = $player;
        }

        return $players;
    }

    /**
     * Get's the top player of the given skill.
     *
     * @throws \Exception
     */
    public function getTopPlayer() {
        if(strlen($this->skill) >= 0) {
            throw new \Exception('You need to specify a skill before trying to get the top players.');
        }


    }

    public function getSkillName() {
        return $this->skill;
    }

    public function getSkill() {
        return $this->getSkillName();
    }
}