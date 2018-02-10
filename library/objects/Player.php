<?php

class Player {
    protected $id;
    protected $name;
    protected $combat;
    protected $totalLevel;
    protected $totalExperience;
    protected $skills;

    public function __construct($id = null, $name = null) {
        $this->id   = $id;
        $this->name = $name;
    }

    /**
     * @param $id
     * @return $this
     * @throws Exception
     */
    public function setId($id) {
        if(!is_int($id)) {
            throw new Exception('Parameter $id needs to be an integer');
        }

        $this->id = $id;
        return $this;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @param $combat
     * @return $this
     * @throws Exception
     */
    public function setCombat($combat) {
        if(!is_int($combat)) {
            throw new Exception('Parameter $combat needs to be an integer');
        }

        $this->combat = $combat;
        return $this;
    }

    /**
     * @param $totalLevel
     * @return $this
     * @throws Exception
     */
    public function setTotalLevel($totalLevel) {
        if(!is_int($totalLevel)) {
            throw new Exception('Parameter $totalLevel needs to be an integer');
        }

        $this->totalLevel = $totalLevel;
        return $this;
    }

    /**
     * @param $totalExperience
     * @return $this
     * @throws Exception
     */
    public function setTotalExperience($totalExperience) {
        if(!is_int($totalExperience)) {
            throw new Exception('Parameter $totalExperience needs to be an integer');
        }

        $this->totalExperience = $totalExperience;
        return $this;
    }

    public function addSkill(Skill $skill) {
        $this->skills[] = $skill;
        return $this;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getCombat() {
        return $this->combat;
    }

    public function getTotalLevel() {
        return $this->totalLevel;
    }

    public function getTotalExperience() {
        return $this->totalExperience;
    }

    /**
     * @param null $id
     * @param null $name
     * @return Player
     * @throws Exception
     */
    public static function getPlayerFromDatabase($id = null, $name = null) {
        if($id === null && $name == null) {
            throw new Exception('Either $id or $name needs to be provided');
        }

        $player = new Player();
        $player->setId($id);
        $player->setName($name);

        $details = $GLOBALS['dbDetails'];
        $db = new DBUtil($details);

        if($id > 0) {
            $player->getPlayerById($db);
        } else {
            $player->getPlayerByName($db);
        }

        return $player;
    }

    /**
     * @param DBUtil $db
     * @return Player
     * @throws Exception
     */
    public function getPlayerById(DBUtil $db) {
        $query = new DBUtilQuery();
        $query->setName('getPlayerById')
            ->setQuery("
                SELECT
                     `U`.`username`
                    ,`S`.*
                FROM `user` `U`
                    INNER JOIN `character_stats` `S` ON `S`.`uid` = `U`.`userid`
                WHERE `U`.`userid` = :id;
            ")
            ->addParameter(':id', $this->getId(), PDO::PARAM_INT)
            ->setMultipleRows(false)
            ->setDBUtil($db)
            ->execute();

        $result = $db->getResultByName($query->getName());

        $this->setName($result['username'])
            ->setCombat((int) $result['combat'])
            ->setTotalExperience((int) $result['totalxp'])
            ->setTotalLevel((int) $result['total']);

        foreach($GLOBALS['skills'] as $skillName) {
            $skill = new Skill($skillName);
            $skill->setExperience((int) $result[$skillName])
                ->setLevelFromExperience();

            $this->addSkill($skill);
        }

        return $this;
    }

    public function getPlayerByName(DBUtil $db) {
        $query = new DBUtilQuery();
        $query->setName('getPlayerById')
            ->setQuery("
                SELECT
                     `U`.`username`
                    ,`S`.*
                FROM `user` `U`
                    INNER JOIN `character_stats` `S` ON `S`.`uid` = `U`.`userid`
                WHERE `U`.`username` = :username;
            ")
            ->addParameter(':username', $this->getName(), PDO::PARAM_INT)
            ->setMultipleRows(false)
            ->setDBUtil($db)
            ->execute();

        $result = $db->getResultByName($query->getName());

        $this->setId((int) $result['uid'])
            ->setCombat((int) $result['combat'])
            ->setTotalExperience((int) $result['totalxp'])
            ->setTotalLevel((int) $result['total']);

        foreach($GLOBALS['skills'] as $skillName) {
            $skill = new Skill($skillName);
            $skill->setExperience((int) $result[$skillName])
                ->setLevelFromExperience();

            $this->addSkill($skill);
        }

        return $this;
    }

    public function getSkills() {
        return $this->skills;
    }

    public function getSkill($name) {
        foreach($this->getSkills() as $skill) {
            /** @var Skill $skill */
            if(strtolower($skill->getName()) == strtolower($name)) {
                return $skill;
            }
        }

        return false;
    }
}