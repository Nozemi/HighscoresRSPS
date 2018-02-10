<?php
namespace Objects\Player;

use Database\DBUtilQuery;

use \PDO;

class Total {
    protected $level = [
        'value'       => -1,
        'rank'        => -1,
        'skillerRank' => -1
    ];
    protected $experience = [
        'value'       => -1,
        'rank'        => -1,
        'skillerRank' => -1
    ];

    protected $player;

    public function __construct(Player $player, $experience = 0, $level = 0) {
        $this->player = $player;

        $this->setLevel($level);
        $this->setExperience($experience);
    }

    public function setLevel($level) {
        $this->getLevelRankFromDb($this->player, $level);

        return $this;
    }

    public function setExperience($experience) {
        $this->getExperienceRankFromDb($this->player, $experience);
        return $this;
    }

    public function getExperienceRankFromDb(Player $player, $experience) {
        $query = "
            SELECT
                COUNT(*) `rank`
        ";

        if($player instanceof Player) {
            $query .= "
                ,(
                    SELECT
                        COUNT(*)
                    FROM `character_stats` `S`
                    WHERE `S`.`combat` = 3 AND `S`.`totalxp` >= :experience AND NOT `S`.`rights` = 2
                ) `skillerRank`
            ";
        }

        $query .= "
            FROM `character_stats` `S`
            WHERE `totalxp` >= :experience AND NOT `S`.`rights` = 2
        ";

        $getExperienceRankQuery = new DBUtilQuery();
        $getExperienceRankQuery->setName('getExperienceRank')
            ->setMultipleRows(false)
            ->setDBUtil($GLOBALS['db'])
            ->setQuery($query)
            ->addParameter(':experience', (int) $experience, PDO::PARAM_INT)
            ->execute();

        $result = $getExperienceRankQuery->result();

        $this->experience = [
            'value'       => $experience,
            'rank'        => -1,
            'skillerRank' => -1
        ];

        if(isset($result['rank'])) {
            $this->experience['rank'] = $result['rank'];
        }

        if(isset($result['skillerRank']) && $player->getCombat() == 3) {
            $this->experience['skillerRank'] = $result['skillerRank'];
        }

        return $this;
    }

    public function getLevelRankFromDb(Player $player, $level) {
        $query = "
            SELECT
                COUNT(*) `rank`
        ";

        if($player instanceof Player) {
            $query .= "
                ,(
                    SELECT
                        COUNT(*)
                    FROM `character_stats` `S`
                    WHERE `S`.`combat` = 3 AND `S`.`total` >= :level AND NOT `S`.`rights` = 2
                ) `skillerRank`
            ";
        }

        $query .= "
            FROM `character_stats` `S`
            WHERE `total` >= :level AND NOT `S`.`rights` = 2
        ";

        $getLevelRankQuery = new DBUtilQuery();
        $getLevelRankQuery->setName('getLevelRank')
            ->setMultipleRows(false)
            ->setDBUtil($GLOBALS['db'])
            ->addParameter(':level', (int) $level, PDO::PARAM_INT)
            ->setQuery($query)
            ->execute();

        $result = $getLevelRankQuery->result();

        $this->level = [
            'value'       => $level,
            'rank'        => -1,
            'skillerRank' => -1
        ];

        if(isset($result['rank'])) {
            $this->level['rank'] = $result['rank'];
        }

        if(isset($result['skillerRank']) && $player->getCombat() == 3) {
            $this->level['skillerRank'] = $result['skillerRank'];
        }

        return $this;
    }

    public function getLevel() {
        return $this->level['value'];
    }

    public function getExperience() {
        return $this->experience['value'];
    }

    public function getLevelRank() {
        return $this->level['rank'];
    }

    public function getLevelRankSkiller() {
        return $this->level['skillerRank'];
    }

    public function getExperienceRank() {
        return $this->experience['rank'];
    }

    public function getExperienceRankSkiller() {
        return $this->experience['skillerRank'];
    }
}