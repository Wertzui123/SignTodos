<?php

namespace Wertzui123\SignTodos;

use pocketmine\block\utils\SignText;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Listener;

class EventListener implements Listener
{

    /** @var Main */
    private $plugin;

    /**
     * EventListener constructor.
     * @param Main $plugin
     */
    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @param SignChangeEvent $event
     */
    public function onSignChange(SignChangeEvent $event)
    {
        $lines = [];
        foreach ($event->getNewText()->getLines() as $line) {
            if ($line !== '') $lines[] = $line;
        }
        $text = implode("\n", $lines);
        $results = [];
        if (preg_match(Main::PATTERN, $text, $results)) {
            if (!isset($this->plugin->todos[$event->getPlayer()->getName()])) $this->plugin->todos[$event->getPlayer()->getName()] = [];
            $todoText = preg_replace(Main::PATTERN, '', $text, 1);
            $text = '§a' . $results[0] . '§r' . $todoText;
            $event->setNewText(new SignText(array_slice(array_pad(explode("\n", $text), 4, ''), 0, 4)));
            foreach ($this->plugin->todos as $todos) {
                foreach ($todos as $i => $data) {
                    if ($data['position']['x'] === $event->getBlock()->getPosition()->x && $data['position']['y'] === $event->getBlock()->getPosition()->y && $data['position']['z'] === $event->getBlock()->getPosition()->z && $data['position']['level'] === $event->getBlock()->getPosition()->getWorld()->getFolderName()) {
                        unset($this->plugin->todos[$event->getPlayer()->getName()][$i]);
                    }
                }
            }
            $this->plugin->todos[$event->getPlayer()->getName()][] = ['position' => ['x' => $event->getBlock()->getPosition()->x, 'y' => $event->getBlock()->getPosition()->y, 'z' => $event->getBlock()->getPosition()->z, 'level' => $event->getBlock()->getPosition()->getWorld()->getFolderName()], 'text' => $todoText];
        }
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onBlockBreak(BlockBreakEvent $event)
    {
        if ($event->isCancelled()) return;
        foreach ($this->plugin->todos as $player => $todos) {
            foreach ($todos as $todo => $data) {
                if ($data['position']['x'] === $event->getBlock()->getPosition()->x && $data['position']['y'] === $event->getBlock()->getPosition()->y && $data['position']['z'] === $event->getBlock()->getPosition()->z && $data['position']['level'] === $event->getBlock()->getPosition()->getWorld()->getFolderName()) {
                    if ($player !== $event->getPlayer()->getName() && !$event->getPlayer()->hasPermission('signtodos.destroy.others')) {
                        $event->getPlayer()->sendMessage($this->plugin->getMessage('cannotDestroyOthers'));
                        $event->cancel();
                        return;
                    }
                    unset($this->plugin->todos[$player][$todo]);
                    return;
                }
            }
        }
    }

}