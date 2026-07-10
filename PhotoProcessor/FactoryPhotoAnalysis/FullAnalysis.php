<?php

class FullAnalysis extends AbstractPhotoAnalysis{

    public function getAnalysis(){
    return [
            'text' => "Ваш анализ фото: " . $this->photoURL
        ];
    }
    public function getRecommendations(){
    return [
            'text' => "Ваши реккомендации: " 
        ];
    }
    public function getHealthRating(){
    return [
            'text' => "Ваша оценка питания:" 
        ];
    }

}

?>
