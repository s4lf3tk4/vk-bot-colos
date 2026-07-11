<?php

class BotController{

    private Logger $logger;
    
    public function __construct(){

        require_once __DIR__ . '/../config/Config.php';
        require_once __DIR__ . '/../MessageProcessor/MessageProcess.php';
        require_once __DIR__ . '/../PhotoProcessor/PhotoProcess.php';
        require_once __DIR__ . '/../Service/SendResponse.php';
        require_once __DIR__ . '/../Service/ServiceMessage.php';
        require_once __DIR__ . '/../Service/KeyboardBuilder.php';
        require_once __DIR__ . '/../MessageProcessor/CommandHandler.php';

        require_once __DIR__ . '/../MessageProcessor/UserState/UserState.php';

        $this->logger = new Logger('botController_error.log');

    }

   private function isConfirmationRequest($data){
        return isset($data['type']) && $data['type'] === 'confirmation';
    }

    private function isMessageNewRequest($data){
        return isset($data['type']) && $data['type'] === 'message_new';
    }

    function handleRequest() : void {
        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);

        if (!$data) {
            $this->log("Ошибка парсинга JSON в BotController");
            echo('ok');
            return;
        }
        if ($this->isConfirmationRequest($data)) {
            echo VK_CONFIRMATION_CODE;
            return;
        }
        if ($this->isMessageNewRequest($data)) {
            $commands = require_once __DIR__ . '/../config/Commands.php';
            $commandHandler = new CommandHandler($commands);
            $newMessage = new MessageProcess($commandHandler);
            $newMessage->handleMessage($data);
            return;
        }
    }

    private function log($message) {
        $this->logger->handle($message);
    }
}