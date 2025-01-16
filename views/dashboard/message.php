<?php
/** @var $this \app\core\View */

use app\core\Application;

$this->title = "Messages";
$this->cssFile = "dashboard/message";
$this->jsFile = "dashboard/message";

?>

<div class="flex gap-8">

    <!-- Tabs button -->
    <div class="flex flex-col min-w-[250px] h-fit sticky top-navbar-height">
        <div class="pro-name">
            <h1><?php echo Application::$app->user->specific()->denomination ?></h1>
        </div>

        <div class="flex flex-col gap-1" id="contactesListe"></div>
    </div>

    <!-- Page content -->
    <div class="page-content flex flex-col relative justify-end w-full">
        <!-- Opinions, generated in js file -->
        <div class="messages-container flex flex-col justify-end" id="message-container"></div>

        <!-- Just for testing -->
        <div id="message-writer-container" class="hidden">
            <textarea id="message-writer" class="message-writer" cols="30" rows="5"></textarea>
            <button class="send-button">
                <i data-lucide="send"></i>
            </button>
        </div>
    </div>
</div>

</div>