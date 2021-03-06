<?php

declare(strict_types=1);

namespace buildertools\event;

use pocketmine\event\Cancellable;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;

class FillEvent extends BuilderToolsEvent implements Cancellable {

    /** @var Player $player */
    protected $player;

    /** @var Level $level */
    protected $level;

    /**
     * @var Vector3 $pos1
     * @var Vector3 $pos2
     */
    protected $pos1, $pos2;

    /**
     * FillEvent constructor.
     * @param Player $player
     * @param Level $level
     * @param Vector3 $pos1
     * @param Vector3 $pos2
     * @param array $settings
     */
    public function __construct(Player $player, Level $level, Vector3 $pos1, Vector3 $pos2, array $settings) {
        $this->player = $player;
        $this->level = $level;
        $this->pos1 = $pos1;
        $this->pos2 = $pos2;
        parent::__construct($settings);
    }

    /**
     * @return Player $player
     */
    public function getPlayer(): Player {
        return $this->player;
    }

    /**
     * @return Level $level
     */
    public function getLevel(): Level {
        return $this->level;
    }

    /**
     * @return Vector3 $pos1
     */
    public function getPos1(): Vector3 {
        return $this->pos1;
    }

    /**
     * @return Vector3 $pos2
     */
    public function getPos2(): Vector3 {
        return $this->pos2;
    }
}
