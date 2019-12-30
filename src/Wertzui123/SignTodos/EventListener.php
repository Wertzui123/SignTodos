<?php

namespace Wertzui123\SignTodos;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Listener;

class EventListener implements Listener
{

    private $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onSignChange(SignChangeEvent $event){
        $lines = [];
        foreach ($event->getLines() as $line){
            if($line !== "") $lines[] = $line;
        }
        $text = implode("\n", $lines);
        unset($lines);
        $results = [];
        if(preg_match(Main::PATTERN , $text, $results)){
            if(!isset($this->plugin->todos[$event->getPlayer()->getName()])) $this->plugin->todos[$event->getPlayer()->getName()] = [];
            $todotext = preg_replace(Main::PATTERN, "", $text, 1);
            $text = "§a".$results[0]."§r".$todotext;
            $event->setLines(array_slice(array_pad(explode("\n", $text), 4, ""), 0, 4));
            foreach ($this->plugin->todos as $player => $todos) {
                foreach($todos as $todo => $data){
                    if($data['position']['x'] === $event->getBlock()->x && $data['position']['y'] === $event->getBlock()->y && $data['position']['z'] === $event->getBlock()->z && $data['position']['level'] === $event->getBlock()->level->getFolderName()){
                        unset($results);
                        unset($text);
                        return;
                    }
                }
            }
            $this->plugin->todos[$event->getPlayer()->getName()][] = ["position" => ["x" => $event->getBlock()->x, "y" => $event->getBlock()->y, "z" => $event->getBlock()->z, 'level' => $event->getBlock()->getLevel()->getFolderName()], "text" => $todotext];
            unset($results);
            unset($text);
        }
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onBlockBreak(BlockBreakEvent $event){
        if($event->isCancelled()) return;
        foreach ($this->plugin->todos as $player => $todos) {
            foreach($todos as $todo => $data){
                if($data['position']['x'] === $event->getBlock()->x && $data['position']['y'] === $event->getBlock()->y && $data['position']['z'] === $event->getBlock()->z && $data['position']['level'] === $event->getBlock()->level->getFolderName()){
                    if($player !== $event->getPlayer()->getName() && !$event->getPlayer()->hasPermission('signtodos.destroy.others')){
                        $event->getPlayer()->sendMessage($this->plugin->getMessage('cannotDestroyOthers'));
                        $event->setCancelled();
                        return;
                    }
                    unset($this->plugin->todos[$player][$todo]);
                    return;
                }
            }
        }
    }

}