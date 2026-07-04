<?php

class MessageProcess{
    private array $commands;
    public function __construct($commands = []){
        $this->commands = $commands;
    }
    public function handleMessage($data) : void{
        try {
            $message = $data['object']['message']['text'] ?? '';
            $peer_id = $data['object']['message']['peer_id'] ?? 0;
            $attachments = $data['object']['message']['attachments'] ?? [];

            if ($this->hasPhoto($attachments)) {
                $photo = new PhotoProcess($attachments, $peer_id);
                $photo->processPhoto();
                return;
            }

            $this->processTextMessage($message, $peer_id);
            echo('ok');
            
        } catch (\Throwable $e) {
            $this->handleError($e, $peer_id ?? 0);
            echo('ok');
        }
    }

    private function hasPhoto($attachments){
        foreach ($attachments as $attach) {
            if ($attach['type'] === 'photo') {
                return true;
            }
        }
        return false;
    }


    private function processTextMessage($message, $peer_id){
        $lowerMessage = mb_strtolower(trim($message));

            if (isset($this->commands[$lowerMessage])) {
            $this->executeCommand($lowerMessage, $message, $peer_id);
        } else {
            $this->handleUnknownCommand($lowerMessage, $peer_id);
        }
    }
    

   private function executeCommand($commandKey, $message, $peer_id){    
    if (!isset($this->commands[$commandKey])) {
        
        $this->sendResponse(
            $peer_id, 
            "Команда не найдена.", 
            KeyboardBuilder::getMainMenuJson()
        );
        return;
    }
    
    $handler = $this->commands[$commandKey];
    
    try{
           
        $responseData = $handler($message, $peer_id);
        $text = $responseData['text'] ?? '';
        $keyboard = $responseData['keyboard'] ?? null;
        $this->sendResponse($peer_id, $text, $keyboard);

    } catch (\Throwable $e) {
        $this->log("Ошибка при выполнении команды '$commandKey': " . $e->getMessage());
        
        $keyboard = KeyboardBuilder::getMainMenuJson();
        $this->sendResponse(
            $peer_id, 
            "😔 Произошла ошибка. Попробуйте позже или воспользуйтесь главным меню.",
            $keyboard
        );
    }
}

    private function handleUnknownCommand($message, $peer_id){
        $keyboard = KeyboardBuilder::getMainMenuJson();
        $this->sendResponse($peer_id, "Неизвестная команда. Используйте кнопки меню.", $keyboard);
        $this->log("Неизвестная команда: $message");
    }

    private function sendResponse($peer_id, $message, $keyboard = null){
        SendResponse::vkSendMessage($peer_id, $message, $keyboard);
    }

    private function handleError($e, $peer_id = 0){
        $errorMessage = "Ошибка: " . $e->getMessage() . " в " . $e->getFile() . ":" . $e->getLine();
        $this->log($errorMessage);

        if ($peer_id > 0) {
            $keyboard = KeyboardBuilder::getMainMenuJson();
            SendResponse::vkSendMessage($peer_id, "Произошла техническая ошибка. Попробуйте позже.", $keyboard);
        }
    }

private function log($message) {
    file_put_contents(
        __DIR__ . '/../logs/messageProc_error.log',
        date('Y-m-d H:i:s') . " " . $message . "\n",
        FILE_APPEND
    );
}

}