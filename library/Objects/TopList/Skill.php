<?php
namespace Objects\TopList;

use Database\DBUtilQuery;

class Skill {
    protected $skill;

    public function __construct($skill = null) {
        $this->skill = $skill;
    }

    public function setSkill($skill) {
        $this->skill = $skill;
    }

    /**
     * Get's the top players of the given skill.
     *
     * @param int $limit
     * @param int $offset
     * @throws \Exception
     */
    public function getTopList($limit = 0, $offset = 0) {
        if(strlen($this->skill) >= 0) {
            throw new \Exception('You need to specify a skill before trying to get the top players.');
        }

        $query = new DBUtilQuery();
        $query->setName('topList')
            ->setMultipleRows(true)
            ->setDBUtil($GLOBALS['db'])
            ->setQuery("
            
            ")
            ->addParameter(':limit', $limit, \PDO::PARAM_INT)
            ->addParameter(':offset', $offset, \PDO::PARAM_INT)
            ->addParameter(':skill', $this->skill)
            ->execute();

        $result = $query->result();
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
}