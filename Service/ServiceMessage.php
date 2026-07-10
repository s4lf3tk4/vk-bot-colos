<?php

class ServiceMessage
{
    public static function photoSending() {
        return [
            'text' => "📸 Отправьте фото блюда для анализа.",
            'keyboard' => KeyboardBuilder::getBackButtonJson(),
        ];
    }

    public static function startMessage() {
        return [
            'text' => "🌟 Добро пожаловать в бот здорового питания! 🌟\n\n" .
                      "🍽️ Я помогу вам анализировать рацион с помощью искусственного интеллекта.\n\n" .
                      "📸 Как получить анализ:\n" .
                      "1. Нажмите кнопку «📋 Получить анализ»\n" .
                      "2. Отправьте фото блюда\n" .
                      "3. Бот оценит его и даст рекомендации\n\n" .
                      "Приятного аппетита и будьте здоровы! 💚",
            'keyboard' => KeyboardBuilder::getMainMenuJson(),
        ];
    }

    public static function optionsButton() {
        return [
            'text' => '⚙️ Настройки аккаунта',
            'keyboard' => KeyboardBuilder::getUserOptionsJson(),
        ];
    }

    public static function backButton() {
        return [
            'text' => 'Вы вернулись в главное меню.',
            'keyboard' => KeyboardBuilder::getMainMenuJson(),
        ];
    }

}
?>