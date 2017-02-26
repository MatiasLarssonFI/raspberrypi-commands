<?php

namespace HTTPDispatch;

require_once(__DIR__ . "/command/music_command.class.php");
require_once(__DIR__ . "/dispatcher/fifo_dispatcher.class.php");

require_once(__DIR__ . "/idispatcher.interface.php");

require_once(__DIR__ . "/exception/invalid_comand_exception.class.php");
require_once(__DIR__ . "/exception/invalid_config_exception.class.php");


class Factory {
    private $_commands;
    private $_dispatcher;
    
    
    /**
     * @return Factory
     */
    public static function from_json_config($path) {
        $this->throw_on_invalid_config($path);
        $conf = json_decode(file_get_contents($path), true);
        
        $cmd_out_method = key($conf->cmd_output);
        $dispatcher_conf = current($conf->cmd_output);
        $dispatchers = [
            new FIFODispatcher($cmd_out_method, $dispatcher_conf),
        ];
        
        
        $dispatcher = null;
        foreach($dispatchers as $candidate) {
            if ($candidate->is_good()) {
                $dispatcher = $candidate;
            }
        }
        
        if ($dispatcher !== null) {
            return new self($dispatcher);
        }
        
        throw new InvalidConfigException("bad cmd output method");
    }
    
    
    protected function __construct(IDispatcher $dispatcher) {
        $this->_commands = [
            new MusicCommand(),
        ];
        $this->_dispatcher = $dispatcher;
    }
    
    
    /**
     * @return IDispatcher
     */
    public function get_dispatcher() {
        return $this->_dispatcher;
    }
    
    
    /**
     * Makes a command out of an HTTP POST request.
     * @return ICommand
     */
    public function command_from_http() {
        $src = $_POST;
        if (array_key_exists("name", $src)) {
            $command = null;
            foreach($this->_commands as $candidate) {
                $candidate->init($src);
                if ($candidate->is_good()) {
                    $command = $candidate;
                }
            }
            
            if ($command) {
                return $command;
            }
            
            throw new InvalidCommandException("bad command");
        }
        
        throw new InvalidCommandException("HTTP request is not a command (name is required)");
    }
    
    
    private function throw_on_invalid_config($path) {
        if (!is_readable($path)) throw new InvalidConfigException("config file must be readable");
        $conf = json_decode(file_get_contents($path));
        if (!is_object($conf)) throw new InvalidConfigException("config must be a JSON object");
        if (!isset($conf->cmd_output)) throw new InvalidConfigException("cmd_output must be defined (root element)");
        if (count(get_object_vars($conf->cmd_output)) == 0) throw new InvalidConfigException("cmd_output must have at least one child");
    }
}
