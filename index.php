<?php

    require_once __DIR__ . '/controller/BotController.php';

    $bot = new BotController;

    $bot->handleRequest();
?>