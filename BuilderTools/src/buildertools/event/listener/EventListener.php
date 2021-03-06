<?php

declare(strict_types=1);

namespace buildertools\event\listener;

use buildertools\BuilderTools;
use buildertools\editors\Copier;
use buildertools\editors\Printer;
use buildertools\Selectors;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\level\Position;
use pocketmine\Player;

/**
 * Class EventListener
 * @package buildertools\events\listener
 */
class EventListener implements Listener {

    /** @var int[] $directionCheck */
    public $directionCheck = [];

    /** @var array $toRotate */
    public $toRotate = [];

    /** @var array $rotateCache */
    public $rotateCache = [];

    /**
     * @param PlayerMoveEvent $event
     */
    public function onDirectionChange(PlayerMoveEvent $event) {
        if(count($this->directionCheck) == 0) {
            return;
        }

        $player = $event->getPlayer();

        if(empty($this->directionCheck[$player->getName()])) {
            return;
        }

        $direction = intval($this->directionCheck[$player->getName()]);

        if($direction != $player->getDirection()) {
            if(isset($this->rotateCache[$player->getName()])) {
                if($this->rotateCache[$player->getName()] == $event->getPlayer()->getDirection()) {
                    return;
                }
            }
            $this->rotateCache[$player->getName()] = $player->getDirection();
            $player->sendMessage(BuilderTools::getPrefix()."If you want to rotate object changing direction from {$direction} to {$player->getDirection()} type into the chat §bconfirm§a.");
            $this->toRotate[$player->getName()] = [$player, $direction, $player->getDirection()];
            return;
        }


    }

    /**
     * @param PlayerChatEvent $event
     */
    public function onChatConfirm(PlayerChatEvent $event) {
        if(count($this->toRotate) == 0) {
            return;
        }

        $player = $event->getPlayer();
        $msg = explode(" ", strtolower($event->getMessage()))[0];


        if(empty($this->toRotate[$player->getName()])) {
            return;
        }

        if(!in_array($msg, ["confirm", "cancel"])) {
            $player->sendMessage(BuilderTools::getPrefix()."You are rotating object. If you want to cancel it, type into the chat§b cancel§a.");
            $event->setCancelled(true);
            return;
        }

        if($msg == "cancel") {
            unset($this->toRotate[$player->getName()]);
            unset($this->directionCheck[$player->getName()]);
            $player->sendMessage(BuilderTools::getPrefix()."You are cancelled rotating an object.");
            $event->setCancelled(true);
            return;
        }

        $player->sendMessage(BuilderTools::getPrefix()."§aRotating selected area...");

        /** @var Copier $copier */
        $copier = BuilderTools::getEditor("Copier");

        $copier->rotate($player, $this->toRotate[$player->getName()][1], $this->toRotate[$player->getName()][2]);
        unset($this->toRotate[$player->getName()]);
        unset($this->directionCheck[$player->getName()]);
        $event->setCancelled(true);
    }

    /**
     * @param PlayerInteractEvent $event
     */
    public function onAirClick(PlayerInteractEvent $event) {
        if(!Selectors::isDrawingPlayer($player = $event->getPlayer())) return;
        $position = $player->getTargetBlock(50)->asPosition();
        $printer = BuilderTools::getEditor("Printer");
        if($printer instanceof Printer) {
            $printer->draw($position, Selectors::getDrawingPlayerBrush($player), $player->getInventory()->getItemInHand()->getBlock(), Selectors::getDrawingPlayerMode($player), Selectors::getDrawingPlayerFall($player), $player);
        }
        $event->setCancelled(true);
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onBlockBreak(BlockBreakEvent $event) {
        if(!Selectors::isWandSelector($player = $event->getPlayer())) return;
        Selectors::addSelector($player, 1, $position = new Position(intval($event->getBlock()->getX()), intval($event->getBlock()->getY()), intval($event->getBlock()->getZ()), $player->getLevel()));
        $player->sendMessage(BuilderTools::getPrefix()."§aSelected first position at {$position->getX()}, {$position->getY()}, {$position->getZ()}");
        $event->setCancelled(true);
    }

    /**
     * @param PlayerInteractEvent $event
     */
    public function onBlockTouch(PlayerInteractEvent $event) {
        if(!Selectors::isWandSelector($player = $event->getPlayer())) return;
        Selectors::addSelector($player, 2, $position = new Position(intval($event->getBlock()->getX()), intval($event->getBlock()->getY()), intval($event->getBlock()->getZ()), $player->getLevel()));
        $player->sendMessage(BuilderTools::getPrefix()."§aSelected second position at {$position->getX()}, {$position->getY()}, {$position->getZ()}");
        $event->setCancelled(true);
    }

    /**
     * @return BuilderTools $builderTools
     */
    public function getPlugin():BuilderTools {
        return BuilderTools::getInstance();
    }
}