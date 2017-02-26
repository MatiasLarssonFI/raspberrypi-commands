<?php

namespace HTTPDispatch;


require_once(__DIR__ . "/icommand.interface.php");


interface IDispatcher {
    /**
     * Dispatches the command.
     * @param ICommand $command
     */
    public function dispatch(ICommand $command);
    
        
    /**
     * Returns true if the dispatcher is in a valid state.
     * @return boolean
     */
    public function is_good();
}
