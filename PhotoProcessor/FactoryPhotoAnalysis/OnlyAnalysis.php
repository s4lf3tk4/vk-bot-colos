<?php

    class OnlyAnalysis extends AbstractPhotoAnalysis{

        public function getAnalysis(){
            return [
                'text' => "Ваш анализ фото: " . $this->photoURL
            ];
        }
        public function getRecommendations(){
            return null;
        }
        public function getHealthRating(){
            return null;
        }
        

    }

?>