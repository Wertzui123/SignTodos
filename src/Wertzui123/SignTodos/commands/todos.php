<?php

namespace Wertzui123\SignTodos\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\level\Position;
use pocketmine\Player;
use Wertzui123\SignTodos\Main;
use jojoe77777\FormAPI\SimpleForm;

class todos extends Command
{

    private $plugin;

    public function __construct(Main $plugin, array $data)
    {
        parent::__construct($data['command'], $data['description'], null, $data['aliases']);
        $this->setPermission("signtodos.cmd.todos");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!$sender instanceof Player){
            $sender->sendMessage($this->plugin->getMessage('cmd.todos.runIngame'));
            return;
        }
        if(!$sender->hasPermission($this->getPermission())){
            $sender->sendMessage($this->plugin->getMessage('cmd.todos.noPermission'));
            return;
        }
        if(count($this->plugin->getTodosByPlayer($sender)) < 1){
            $sender->sendMessage($this->plugin->getMessage('cmd.todos.empty'));
            return;
        }
        $todos = $this->plugin->getTodosByPlayer($sender);
        $form = new SimpleForm(function (Player $player, $data) use ($todos){
            if(is_null($data)) return;
            $data = $todos[$data];
            $player->sendMessage($this->plugin->getMessage('cmd.todos.success', ['text' => $data['text']]));
            $player->teleport(new Position($data['position']['x'], $data['position']['y'], $data['position']['z'], $this->plugin->getServer()->getLevelByName($data['position']['level'])));
        });
        $form->setTitle($this->plugin->getString('ui.todos.title'));
        $form->setContent($this->plugin->getString('ui.todos.content'));
        foreach ($todos as $id => $todo) {
            $form->addButton($this->plugin->getString('format', ['{text}' => $todo['text'], '{x}' => $todo['position']['x'], '{y}' => $todo['position']['y'], '{z}' => $todo['position']['z'], 'level' => $todo['position']['level']]), -1, '', $id);
        }
        $sender->sendForm($form);
    }
}