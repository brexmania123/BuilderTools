<?php

declare(strict_types=1);

namespace buildertools\commands;

use buildertools\BuilderTools;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\level\generator\object\BirchTree;
use pocketmine\level\generator\object\JungleTree;
use pocketmine\level\generator\object\OakTree;
use pocketmine\level\generator\object\SpruceTree;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\Random;

/**
 * Class TreeCommand
 * @package buildertools\commands
 */
class TreeCommand extends Command implements PluginIdentifiableCommand {

    /**
     * TreeCommand constructor.
     */
    public function __construct() {
        parent::__construct("/tree", "Place tree object", null, []);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return mixed|void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can be used only in-game!");
            return;
        }
        if(!$sender->hasPermission("bt.cmd.tree")) {
            $sender->sendMessage("§cYou have not permissions to use this command!");
            return;
        }

        if(empty($args[0])) {
            $sender->sendMessage("§cUsage: §7/tree <list|treeType>");
            return;
        }

        if(strtolower($args[0]) == "list") {
            $sender->sendMessage(BuilderTools::getPrefix()."§aTree list: Birch, Oak, Jungle, Spruce");
            return;
        }

        $object = null;

        switch (strtolower($args[0])) {
            case "oak":
                $object = new OakTree;
                break;
            case "birch":
                $object = new BirchTree;
                break;
            case "jungle":
                $object = new JungleTree;
                break;
            case "spruce":
                $object = new SpruceTree;
                break;
        }

        if($object === null) {
            $sender->sendMessage(BuilderTools::getPrefix()."§cObject {$args[0]} does not found!");
            return;
        }

        $object->placeObject($sender->getLevel(), (int)$sender->getX(), (int)$sender->getY(), (int)$sender->getZ(), new Random($sender->getLevel()->getSeed()));
        $sender->sendMessage(BuilderTools::getPrefix()."§aObject {$args[0]} placed!");
    }

    /**
     * @return BuilderTools $plugin
     */
    public function getPlugin(): Plugin {
        return BuilderTools::getInstance();
    }

}
