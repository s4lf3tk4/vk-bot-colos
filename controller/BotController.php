<?php

class BotController{

    private MessageProcess $messageProcessor;

    public function __construct(MessageProcess $messageProcessor = null){

        require_once __DIR__ . '/../config/Config.php';
        require_once __DIR__ . '/../MessageProcessor/MessageProcess.php';
        require_once __DIR__ . '/../PhotoProcessor/PhotoProcess.php';
        require_once __DIR__ . '/../Service/SendResponse.php';
        require_once __DIR__ . '/../Service/ServiceMessage.php';
        require_once __DIR__ . '/../Service/KeyboardBuilder.php';

        $this->messageProcessor = new MessageProcess();


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
            $this->log("Ошибка парсинга JSON");
            return;
        }
        if ($this->isConfirmationRequest($data)) {
            echo VK_CONFIRMATION_CODE;
            return;
        }
        if ($this->isMessageNewRequest($data)) {
            $commands = require_once __DIR__ . '/../config/Commands.php';
            $newMessage = new MessageProcess($commands);
            $newMessage->handleMessage($data);
            return;
        }
        echo('ok');
    }
    private function log($message) {
    file_put_contents(
        __DIR__ . '/../logs/debug.log',
        date('Y-m-d H:i:s') . " " . $message . "\n",
        FILE_APPEND
    );
    }
}