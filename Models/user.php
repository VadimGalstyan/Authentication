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

            $stmt = $this->pdo->prepare("INSERT INTO users (name,email,password) VALUES(?,?,?)");
            $stmt->execute([$name, $email,$hashedPassword]);
        }
        
        public function getRow($email)
        {
            $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->execute([$email]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
    }

?>