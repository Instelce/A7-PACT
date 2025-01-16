<?php
/** @var $this \app\core\View */

use app\core\Application;

$this->title = "Messages";
$this->cssFile = "dashboard/message";
$this->jsFile = "dashboard/message";

?>

<div class="flex gap-8 h-[80vh]">

    <!-- Tabs button -->
    <div class="flex flex-col justify-between min-w-[250px] h-fit top-navbar-height">
        <div class="pro-name">
            <h1><?php echo Application::$app->user->specific()->denomination ?></h1>
        </div>

        <div class="flex flex-col h-[70vh] overflow-y-scroll" id="contactesListe"></div>
    </div>

    <!-- Page content -->
    <div class="page-content flex flex-col justify-end w-full h-[80vh] gap-2">
        <!-- Opinions, generated in js file -->
        <div class="messages-container flex flex-col m-0 p-0 h-[65vh] overflow-y-scroll" id="message-container"></div>

        <!-- Writing indicator -->
        <div class="writing-indicator !hidden !px-0 py-1">
            <span style="--i:1"></span>
            <span style="--i:2"></span>
            <span style="--i:3"></span>
        </div>

        <!-- Just for testing -->
        <div id="message-writer-container" class="hidden w-full h-[15vh] flex flex-row gap-2">
            <textarea id="message-writer" class="message-writer" cols="30" rows="5"></textarea>
            <button class="send-button w-1/12">
                <i data-lucide="send"></i>
            </button>
        </div>
    </div>
</div>

</div>