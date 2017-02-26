<?php

namespace HTTPDispatch;


interface ICommand {
    /**
     * Writes the command to the resource.
     * 
     * @param resource $fp
     */
    public function write($fp);
    
    
    /**
     * Initialize the command.
     * @param array $command Command data
     */
    public function init(array $command);
    
    
    /**
     * Returns true if the command is in a valid state.
     * @return boolean
     */
    public function is_good();
}
