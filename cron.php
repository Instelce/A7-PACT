<?php

// This is the entry point for the cron job to run tasks

require_once __DIR__ . '/vendor/autoload.php';

$taskName = $argv[1];
$taskManager = new app\core\TaskManager();
$taskManager->runTask($taskName);
