<?php

namespace Wertzui123\SignTodos\commands;

use pocketmine\block\BlockLegacyIds;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\plugin\PluginOwned;
use pocketmine\world\Position;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use Wertzui123\SignTodos\Main;
use jojoe77777\FormAPI\SimpleForm;

class todos extends Command implements PluginOwned
{

    private $plugin;

    public function __construct(Main $plugin, array $data)
    {
        parent::__construct($data['command'], $data['description'], null, $data['aliases']);
        $this->setPermission('signtodos.command.todos');
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage($this->plugin->getMessage('command.todos.runIngame'));
            return;
        }
        if (!$sender->hasPermission($this->getPermission())) {
            $sender->sendMessage($this->plugin->getMessage('command.todos.noPermission'));
            return;
        }
        if (count($this->plugin->getTodosByPlayer($sender)) < 1) {
            $sender->sendMessage($this->plugin->getMessage('command.todos.empty'));
            return;
        }
        $todos = $this->plugin->getTodosByPlayer($sender);
        $form = new SimpleForm(function (Player $player, $todo) use ($todos) {
            if (is_null($todo)) return;
            $data = $todos[$todo];
            $world = $this->plugin->getServer()->getWorldManager()->getWorldByName($data['position']['level']);
            if ($world === null) {
                $this->plugin->getServer()->getWorldManager()->loadWorld($data['position']['level']);
                $world = $this->plugin->getServer()->getWorldManager()->getWorldByName($data['position']['level']);
            }
            $position = new Position($data['position']['x'], $data['position']['y'], $data['position']['z'], $world);
            if (!$position->isValid() || !in_array($position->getWorld()->getBlock($position)->getId(), [BlockLegacyIds::SIGN_POST, BlockLegacyIds::WALL_SIGN, BlockLegacyIds::ACACIA_STANDING_SIGN, BlockLegacyIds::ACACIA_WALL_SIGN, BlockLegacyIds::BIRCH_STANDING_SIGN, BlockLegacyIds::BIRCH_WALL_SIGN, BlockLegacyIds::DARKOAK_STANDING_SIGN, BlockLegacyIds::DARKOAK_WALL_SIGN, BlockLegacyIds::JUNGLE_STANDING_SIGN, BlockLegacyIds::JUNGLE_WALL_SIGN, BlockLegacyIds::SPRUCE_STANDING_SIGN, BlockLegacyIds::SPRUCE_WALL_SIGN])) {
                $player->sendMessage($this->plugin->getMessage('command.todos.invalidPosition'));
                unset($this->plugin->todos[$player->getName()][$todo]);
                return;
            }
            $player->sendMessage($this->plugin->getMessage('command.todos.success', ['text' => $data['text']]));
            $player->teleport(new Position($data['position']['x'], $data['position']['y'], $data['position']['z'], $this->plugin->getServer()->getWorldManager()->getWorldByName($data['position']['level'])));
        });
        $form->setTitle($this->plugin->getString('ui.todos.title'));
        $form->setContent($this->plugin->getString('ui.todos.content'));
        foreach ($todos as $id => $todo) {
            $form->addButton($this->plugin->getString('format', ['{text}' => $todo['text'], '{x}' => $todo['position']['x'], '{y}' => $todo['position']['y'], '{z}' => $todo['position']['z'], 'level' => $todo['position']['level']]), -1, '', $id);
        }
        $sender->sendForm($form);
    }

    public function getOwningPlugin(): Plugin
    {
        return $this->plugin;
    }

}