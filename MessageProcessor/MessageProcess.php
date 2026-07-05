<?php

class MessageProcess{

    private CommandHandler $commandHandler;
    
    public function __construct(CommandHandler $commandHandler){
        $this->commandHandler = $commandHandler;
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
            $this->commandHandler->handle($message, $peer_id);
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
    
    private function handleError($e, $peer_id = 0){
        $errorMessage = "Ошибка: " . $e->getMessage() . " в строке: " . $e->getLine();
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