<?php

    class UserState{
        private $peer_id;
        private mysqli $conn;
        public function __construct($peer_id){
            $this->peer_id = $peer_id;
             $this->conn = require __DIR__ . '/DB_config.php';
        }
        public function handle(){
            try{ 
            
                if(!$this->isUnique()){
                    $this->createUser();
                    return ['status' => 'guest', 'requests' => 5];    
                }
                $userData = $this->getUserData();

                if ($userData['status'] === 'prem' && strtotime($userData['time_status']) > time()){
                    return ['status' => 'prem', 'requests' => null];
                }
                return ['status' => 'guest', 'requests' => $userData['requests']];
                
              
            }catch (\Throwable $e) {
                $this->handleError($e);
                echo('ok');
            }
        }

        public function decrementRequests(){
            $sql = "UPDATE users SET requests = requests - 1 WHERE id = ? AND requests > 0";
            $stmt = $this->conn->prepare($sql);

            if (!$stmt) {
                throw new \Exception("Ошибка: " . $this->conn->error);
            }

            $stmt->bind_param("s", $this->peer_id);
            $stmt->execute();
            $stmt->close();
        }

        public function getUserData(){
            $sql = "SELECT status, time_status, requests FROM users WHERE id = ? LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new \Exception("Ошибка getUserData: " . $this->conn->error);
            }

            $stmt->bind_param("s", $this->peer_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $responce = $result->fetch_assoc();
            return $responce;
        }

        public function createUser(){
            $sql = "INSERT INTO users (id, status, requests) VALUES (?, 'guest', 5)";
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                throw new \Exception("Ошибка createUser: " . $this->conn->error);
            }

            $stmt->bind_param("i", $this->peer_id);
            $stmt->execute();
            $stmt->close();
        }

        private function isUnique(): bool{
            $sql = "SELECT id FROM users WHERE id = ? LIMIT 1";
            $stmt = $this->conn->prepare($sql);

            if (!$stmt) {
                throw new \Exception("Ошибка isUnique: " . $this->conn->error);
            }

            $stmt->bind_param("s", $this->peer_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $exists = $result->num_rows > 0;
            $stmt->close();

            return $exists;
        }
        private function handleError($e, $peer_id = 0){
            $errorMessage = "Ошибка: " . $e->getMessage() . " в строке: " . $e->getLine();
            $this->log($errorMessage);
        }
        private function log($message) {
            file_put_contents(
                __DIR__ . '/../../logs/UserState_errors.log',
                date('Y-m-d H:i:s') . " " . $message . "\n",
                FILE_APPEND
            );
        }

    }

?>