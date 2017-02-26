<?php

require_once(__DIR__ . "/factory.class.php");

/*
 * Dispatches commands from HTTP requests. Supports
 * application/x-www-form-urlencoded.
 */


function dispatch($config_file) {
    try {
        $factory = \HTTPDispatch\Factory::from_json_config($config_file);
        $factory->get_dispatcher()->dispatch($factory->command_from_http());
        
        echo "ok\n";
    } catch (\HTTPDispatch\InvalidCommandException $e) {
        echo "invalid command: {$e->getMessage()}\n";
    } catch (\HTTPDispatch\InvalidConfigException $e) {
        echo "invalid config: {$e->getMessage()}\n";
     catch (\Exception $e) {
        echo "error: {$e->getMessage()}\n";
    }
}

dispatch(__DIR__ . "/config.json");
