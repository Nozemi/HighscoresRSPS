<?php
namespace Objects\Player;

use Database\DBUtilQuery;

use \PDO;

class Skill {
    protected $name;
    protected $level;
    protected $experience;
    protected $rank;

    public function __construct($name = null) {
        $this->name = $name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @param $level
     * @return $this
     * @throws \Exception
     */
    public function setLevel($level) {
        if(!is_int($level)) {
            throw new \Exception('Parameter $level needs to be an integer');
        }

        $this->level = $level;
        return $this;
    }

    /**
     * @param null $experience
     * @return Skill
     * @throws \Exception
     */
    public function setLevelFromExperience($experience = null) {
        if($this->experience === null && $experience === null) {
            throw new \Exception('Failed to get level from experience. Looks like experience is missing.');
        }

        if($experience === null) {
            $experience = $this->experience;
        }

        $playerLevel = 99;

        $points = 0;
        for($level = 1; $level < 99; $level++) {
            $points += floor($level + 300.0 * pow(2.0, $level / 7.0));
            $output = floor($points / 4);

            if($experience < $output) {
                $playerLevel = $level;
                break;
            }
        }

        $this->level = $playerLevel;
        return $this;
    }

    /**
     * @param $rank
     * @return $this
     * @throws \Exception
     */
    public function setRank($rank) {
        if(!is_int($rank)) {
            throw new \Exception('Parameter $rank needs to be an integer');
        }

        $this->rank = $rank;
        return $this;
    }

    /**
     * @param $experience
     * @return $this
     * @throws \Exception
     */
    public function setExperience($experience) {
        if(!is_int($experience)) {
            throw new \Exception('Parameter $experience needs to be an integer');
        }

        $this->experience = $experience;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function getLevel() {
        return $this->level;
    }

    public function getExperience() {
        return $this->experience;
    }

    public function getRank() {
        return $this->rank;
    }

    /**
     * @param Player|null $player
     * @return $this
     * @throws \Exception
     */
    public function getRankFromDb(Player $player = null) {
        $query = new DBUtilQuery();

        $query->setName('skillRank')
            ->addParameter(':experience', $this->getExperience(), PDO::PARAM_INT)
            ->setDBUtil($GLOBALS['db'])
            ->setMultipleRows();

        if($player instanceof Player) {
            $query->setQuery("
                SELECT
                      COUNT(*) `rank`
                     ,(
                        SELECT
                          `rights`
                        FROM `character_stats`
                        WHERE `uid` = :uid
                     ) `rights`
                FROM `character_stats` `S`
                WHERE `S`.`{$this->getName()}` >= :experience AND `S`.`rights` < 2
            ")
            ->addParameter(':uid', $player->getId(), PDO::PARAM_INT);
        } else {
            $query->setQuery("
                SELECT
                     COUNT(*) `rank`
                FROM `character_stats` `S`
                WHERE `S`.`{$this->getName()}` >= :experience AND `S`.`rights` < 2
            ");
        }
        $query->execute();

        $result = $query->result();
        $this->setRank((int) $result['rank']);

        if(isset($result['rights']) && ((int) $result['rights'] === 2)) {
            $this->setRank(-1);
        }

        return $this;
    }

    public function getInfo($includeName = false) {
        $skillInfo = [
            'level'      => $this->getLevel(),
            'experience' => $this->getExperience(),
            'rank'       => $this->getRank()
        ];

        if($includeName) {
            $skillInfo = array_merge([
                'name' => $this->getName()
            ], $skillInfo);
        }

        return $skillInfo;
    }
}