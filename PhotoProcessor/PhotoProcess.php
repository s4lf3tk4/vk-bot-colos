<?php

class PhotoProcess{

    private $attachment;
    private $peer_id;      

    public function __construct($attachment, $peer_id) {   

        require_once 

        $this->attachment = $attachment;
        $this->peer_id = $peer_id;                      
    }
    private function extractPhotoUrl($attachments){
        foreach ($attachments as $attach) {
            if ($attach['type'] === 'photo') {
                $sizes = $attach['photo']['sizes'];
                return end($sizes)['url'];
            }
        }
        return null;
    }

    private function processPhotoMessage($photoUrl){
        try {
            $responseData = ServiceMessage::uploadPhoto($photoUrl);
            SendResponse::vkSendMessage($this->peer_id, $responseData);
        } catch (\Throwable $e) {
            $this->log("Ошибка при обработке фото: " . $e->getMessage());
           SendResponse::vkSendMessage($this->peer_id, "Произошла ошибка при обработке фото.", KeyboardBuilder::getMainMenuJson());
        }
    }

    public function processPhoto(){
        $photoURL = $this->extractPhotoUrl($this->attachment);
        $this->processPhotoMessage($photoURL);
    }
        
    private function log($message){
        file_put_contents(
            __DIR__ . '/../logs/photo_error.log',
            date('Y-m-d H:i:s') . " " . $message . "\n",
            FILE_APPEND
        );
    }
    
}

?>