<?php

namespace Wertzui123\SignTodos;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use Wertzui123\SignTodos\commands\todos;

class Main extends PluginBase
{

    /** @var string */
    const PATTERN = '/^\/\/ *todo: */i';
    /** @var float */
    const CONFIG_VERSION = 1.2;

    // TODO: SQL Database support

    /** @var Config */
    private $stringsFile;
    /** @var Config */
    private $todosFile;
    public $todos = []; // ["Steve" => [0 => ["position" => ["x" => 0, "y" = 0, "z" = 0, "level" => "world"], "text => "this is a example"]]]

    public function onEnable(): void
    {
        $this->checkConfig();
        $this->stringsFile = new Config($this->getDataFolder() . 'strings.yml', Config::YAML);
        $this->todosFile = new Config($this->getDataFolder() . 'todos.json', Config::JSON);
        $this->todos = $this->todosFile->getAll();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $commandData = ['command' => $this->getConfig()->getNested('commands.todos.command'), 'description' => $this->getConfig()->getNested('commands.todos.description'), 'usage' => $this->getConfig()->getNested('commands.todos.usage'), 'aliases' => $this->getConfig()->getNested('commands.todos.aliases')];
        $this->getServer()->getCommandMap()->register("SignTodos", new todos($this, $commandData));
    }

    /**
     * Config updater
     */
    public function checkConfig()
    {
        if (!file_exists($this->getDataFolder() . 'config.yml') || !file_exists($this->getDataFolder() . 'strings.yml')) {
            $this->saveResource('config.yml');
            $this->saveResource('strings.yml');
            return;
        }
        if (($configVersion = $this->getConfig()->get('config-version', -1)) !== self::CONFIG_VERSION) {
            $this->getLogger()->warning("Your config is outdated!");
            if ($this->getConfig()->get('auto-config-update', true)) {
                $this->getLogger()->info('Your config is being updated to the latest version automatically...');
                rename($this->getDataFolder() . 'config.yml', $this->getDataFolder() . 'config-' . $configVersion . '.yml');
                rename($this->getDataFolder() . 'strings.yml', $this->getDataFolder() . 'strings-' . $configVersion . '.yml');
                $this->saveResource('config.yml');
                $this->saveResource('strings.yml');
            }
        }
    }

    /**
     * @internal
     * @param string $key
     * @param array $replace
     * @return string
     */
    public function getString($key, $replace = [])
    {
        return str_replace(array_keys($replace), $replace, $this->stringsFile->getNested($key) ?? '');
    }

    /**
     * @internal
     * @param string $key
     * @param array $replace
     * @return string
     */
    public function getMessage($key, $replace = [])
    {
        return $this->getString($key, $replace);
    }

    /**
     * @api
     * Returns all todos of the given player
     * @param Player $player
     * @return array
     */
    public function getTodosByPlayer(Player $player)
    {
        return $this->todos[$player->getName()] ?? [];
    }

    public function onDisable(): void
    {
        $this->todosFile->setAll($this->todos);
        $this->todosFile->save();
    }

}