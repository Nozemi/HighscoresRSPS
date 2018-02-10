<?php
namespace Objects\TopList;

use Database\DBUtilQuery;
use Objects\Player\Player;
use Utilities\JsonData;
use \PDO;

class Total {
    const TOTAL_LEVEL = 0;
    const TOTAL_EXPERIENCE = 1;

    protected $valid = ['experience', 'level'];
    protected $type;

    protected $minCombat;
    protected $maxCombat;

    protected $offset;
    protected $limit;

    /**
     * Total constructor.
     * @param string $type
     * @param null $maxCombat
     * @param null $minCombat
     */
    public function __construct($type = 'level', $maxCombat = null, $minCombat = null) {
        $this->setType($type);
        $this->setMinCombat($minCombat);
        $this->setMaxCombat($maxCombat);
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    public function setMaxCombat($maxCombat) {
        $this->maxCombat = $maxCombat;
        return $this;
    }

    public function setMinCombat($minCombat) {
        $this->minCombat = $minCombat;
        return $this;
    }

    public function setOffset($offset) {
        $this->offset = $offset;
        return $this;
    }

    public function setLimit($limit) {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getTotal() {
        $orderBy = 'total';
        if($this->type == 'experience') {
            $orderBy = 'totalxp';
        }

        if(in_array($this->type, $this->valid)) {
            $getTotalQuery = new DBUtilQuery();
            $getTotalQuery->setName('getTotal')
                ->setMultipleRows(true)
                ->setDBUtil($GLOBALS['db']);

            $query = "
                SELECT
                     `U`.`username`
                    ,`S`.*
                FROM `character_stats` `S`
                    INNER JOIN `user` `U` ON `U`.`userid` = `S`.`uid`
                WHERE NOT `S`.`rights` = 2
            ";

            if ($this->maxCombat >= 3) {
                $query .= " AND `S`.`combat` <= :maxCombat ";
                $getTotalQuery->addParameter(':maxCombat', (int) $this->maxCombat, PDO::PARAM_INT);
            }

            if ($this->minCombat >= 3) {
                $query .= " AND `S`.`combat` >= :minCombat ";
                $getTotalQuery->addParameter(':minCombat', (int) $this->minCombat, PDO::PARAM_INT);
            }

            $query .= "
                ORDER BY `S`.`{$orderBy}` DESC
                LIMIT :offset,:limit
            ";

            $getTotalQuery->setQuery($query)
                ->addParameter(':offset', (int) $this->offset, PDO::PARAM_INT)
                ->addParameter(':limit', (int) $this->limit, PDO::PARAM_INT)
                ->execute();

            $result = $getTotalQuery->result();

        }

        if(empty($result)) {
            $error = new JsonData(JsonData::ERROR_NOT_FOUND, 'Skill not found.');
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
}