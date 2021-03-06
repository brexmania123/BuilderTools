<?php

declare(strict_types=1);

namespace buildertools\task\async;


use buildertools\BuilderTools;
use buildertools\editors\Canceller;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

/**
 * Class FillAsyncTask
 * @package buildertools\task\async
 */
class FillAsyncTask extends AsyncTask {

    /** @var array $data */
    public $data;

    /**
     * FillAsyncTask constructor.
     * @param array $fillData
     */
    public function __construct(array $fillData) {
        $this->data = $fillData;
    }

    /**
     * @task
     */
    public function onRun() {
        $this->setResult(serialize($this->data), false);
    }

    /**
     * @asyncTask
     *
     * @param Server $server
     */
    public function onCompletion(Server $server) {
        $result = $this->data;

        if($result === null) {
            $server->getLogger()->critical("§cNULL");
        }

        /** @var Position $pos1 */
        $pos1 = new Position($result["pos1"][0], $result["pos1"][1], $result["pos1"][2], $server->getLevelByName($result["pos1"][3]));

        /** @var Position $pos2 */
        $pos2 = new Position($result["pos2"][0], $result["pos2"][1], $result["pos2"][2], $server->getLevelByName($result["pos2"][3]));

        /** @var array $blocks */
        $blocks = $result["blocks"];

        /** @var Player $player */
        $player = $server->getPlayer($result["player"]);
        
        /** @var array $args */
        $args = explode(",", strval($blocks));

        if($pos1->getLevel()->getName() !== $pos2->getLevel()->getName()) {
            return;
        }
        
        $count = 0;

        $undo = [];

        for($x = min($pos1->getX(), $pos2->getX()); $x <= max($pos1->getX(), $pos2->getX()); $x++) {
            for($y = min($pos1->getY(), $pos2->getY()); $y <= max($pos1->getY(), $pos2->getY()); $y++) {
                for($z = min($pos1->getZ(), $pos2->getZ()); $z <= max($pos1->getZ(), $pos2->getZ()); $z++) {
                    array_push($undo, $pos1->getLevel()->getBlock(new Vector3($x, $y, $z)));
                    $pos1->getLevel()->setBlock(new Vector3($x, $y, $z), Item::fromString($args[array_rand($args, 1)])->getBlock());
                    $count++;
                }
            }
        }

        /** @var Canceller $canceller */
        $canceller = BuilderTools::getEditor("Canceller");

        $canceller->addStep($player, $undo);

        $player->sendMessage(BuilderTools::getPrefix()."§aSelected area successfully filled using async task! ($count blocks changed!)");
        
    }
}
