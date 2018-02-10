<?php
namespace Endpoint;

use Objects\Player\Player;
use Objects\Player\Skill;

use Utilities\JsonData;

class Highscores extends AbstractEndpoint {

    const GET_USER = 1;
    const GET_USER_BY_ID = 2;
    const GET_USER_BY_NAME = 3;

    const GET_PLAYER_SKILL = 4;

    protected $type;

    /**
     * Highscores constructor.
     * @param null $params
     * @throws \Exception
     */
    public function __construct($params = null) {
        parent::__construct($params);

        $this->type = null;
        if(strtolower($this->getParam(0)) == 'user') {
            $this->type = self::GET_USER;
        }

        if($this->type == self::GET_USER) {
            $player = null;
            if(strtolower($this->getParam(1)) == 'name') {
                $this->type = self::GET_USER_BY_NAME;
                $player = $this->getUserByName($this->getParam(2));
            } else if(strtolower($this->getParam(1)) == 'id') {
                $this->type = self::GET_USER_BY_ID;
                $player = $this->getUserById($this->getParam(2));
            }

            if($player instanceof Player) {
                if(strtolower($this->getParameter(3)) == 'skill') {
                    $this->type = self::GET_PLAYER_SKILL;
                }

                if($this->type == self::GET_PLAYER_SKILL) {
                    $skill = $player->getSkill($this->getParameter(4));
                    if($skill instanceof Skill) {
                        $skillArray = [
                            'id'   => $player->getId(),
                            'name' => $player->getName(),
                            $skill->getName() => $skill->getInfo(),
                            'skill' => $skill->getInfo(true)
                        ];

                        $data = new JsonData(200, 'Successfully got player skill data.', $skillArray);
                        echo $data->getMessage();
                    } else {
                        $error = new JsonData(404,'Skill not found.');
                        echo $error->getMessage();
                    }
                } else {
                    $playerArray = $player->getInfo();
                    $data = new JsonData(200, 'Successfully got player data.', $playerArray);
                    echo $data->getMessage();
                }
            } else {
                $error = new JsonData(404,'Player not found.');
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
}