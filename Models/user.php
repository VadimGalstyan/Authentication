<?php

    class User
    {
        private PDO $pdo;

        public function __construct(PDO $pdo) {
            $this->pdo = $pdo;
        }
         
        public function findByEmail($email) : array | false
        {
            $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->execute([$email]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function emailExists($email) : bool 
        {
            return $this->findByEmail($email) !== false;
        }
        

        public function registration($name, $email, $password)
        {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $verificationToken = bin2hex(random_bytes(32));

            $stmt = $this->pdo->prepare(
                "INSERT INTO users (name, email, password, verification_token) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$name, $email, $hashedPassword, $verificationToken]);

            return $verificationToken;
        }
        
        public function getRow($email)
        {
            $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->execute([$email]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }


        public function findByToken($token) : array | false
        {
            $stmt = $this->pdo->prepare('SELECT * FROM users WHERE verification_token = ?');
            $stmt->execute([$token]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function verifyEmail($userId) : void
        {
            $stmt = $this->pdo->prepare(
                "UPDATE users SET email_verified_at = NOW(), verification_token = NULL WHERE id = ?"
            );
            $stmt->execute([$userId]);
        }

        public function regenerateVerificationToken($email) : string|false
        {
            $user = $this->findByEmail($email);

            if (!$user || $user['email_verified_at'] !== null) {
                return false;
            }

            $newToken = bin2hex(random_bytes(32));

            $stmt = $this->pdo->prepare("UPDATE users SET verification_token = ? WHERE id = ?");
            $stmt->execute([$newToken, $user['id']]);

            return $newToken;
        }

        public function isVerified($email) : bool
        {
            $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->Fetch(PDO::FETCH_ASSOC);

            return $user['email_verified_at'] !== NULL;
        }
                
    }

?>