<?php

namespace app\core;

class TaskManager
{
    private array $tasks = [];

    public function __construct()
    {
        $this->loadTasks();
    }

    public function loadTasks(): void
    {
        $files = scandir(Application::$ROOT_DIR . '/tasks');

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $className = pathinfo($file, PATHINFO_FILENAME);
            $class = "app\\tasks\\$className";

            $task = new $class();
            $this->tasks[] = $task;
        }
    }

    public function scheduleTasks(): void
    {
        shell_exec("crontab -r");
        foreach ($this->tasks as $task) {
            $this->scheduleTask($task->getName());
        }
    }

    public function scheduleTask(string $taskName): void
    {
        $task = $this->getTask($taskName);

        if ($task) {
            $schedule = $task->schedule();
            shell_exec("echo \"$schedule php " . Application::$ROOT_DIR . "/cron.php $taskName\" | crontab -");

            echo "Schedule $taskName at " . $task->scheduleString() . PHP_EOL;
        }
    }

    public function getTask(string $taskName): ?CronTask
    {
        return array_filter($this->tasks, fn($task) => $task->getName() === $taskName)[0] ?? null;
    }

    public function runTask(string $taskName): void
    {
        $task = $this->getTask($taskName);
        if ($task && $task->runCondition()) {
            echo "Running task $taskName" . PHP_EOL;
            $task->run();
        } else {
            echo "Task $taskName cannot be run" . PHP_EOL;
        }
    }

    public function runTaskWithoutContidion(string $taskName)
    {
        echo "Running task $taskName" . PHP_EOL;
        $this->getTask($taskName)?->run();
    }

    public function listTasks(): void
    {
        foreach ($this->tasks as $task) {
            echo $task->getName() . " - " . $task->getDescription() . " - Run " . $task->scheduleString() . PHP_EOL;
        }
    }
}