<?php
namespace Objects\Player;

class Skill {
    protected $name;
    protected $level;
    protected $experience;

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

    public function getInfo($includeName = false) {
        $skillInfo = [
            'level'      => $this->getLevel(),
            'experience' => $this->getExperience(),
            'rank'       => 'N/A - Not yet implemented.'
        ];

        if($includeName) {
            $skillInfo = array_merge([
                'name' => $this->getName()
            ], $skillInfo);
        }

        return $skillInfo;
    }
}