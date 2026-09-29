<?php

    class RateLimiter 
    {

        private static $pdo = null;
        private static $loginMaxAttempts = 5;
        private static $loginWindowSeconds = 900;
        private static $postMaxAttempts = 10;
        private static $postWindowSeconds = 86400;

        private static $commentMaxAttempts = 5;
        private static $commentWindowSeconds = 60;

        public static function init($pdo) 
        {
            self::$pdo = $pdo;
        }

        public static function record($identifier, $action) 
        {
            $stmt = self::$pdo->prepare(
                "INSERT INTO rate_limits (identifier, action) VALUES (?, ?)"
            );
            $stmt->execute([$identifier, $action]);
        }


        public static function tooMany($identifier, $action, $maxAttempts, $windowSeconds) 
        {
            $stmt = self::$pdo->prepare(
                "SELECT COUNT(*) FROM rate_limits
                WHERE identifier = ? AND action = ?
                AND created_at > (NOW() - INTERVAL ? SECOND)"
            );
            $stmt->execute([$identifier, $action, $windowSeconds]);
            $count = (int)$stmt->fetchColumn();

            return $count >= $maxAttempts;
        }

        

        public static function recordLogin($identifier) 
        {
            $stmt = self::$pdo->prepare(
                "INSERT INTO rate_limits (identifier, action) VALUES (?, ?)"
            );
            $stmt->execute([$identifier, "login"]);
        }

        public static function recordPost($identifier) 
        {
            $stmt = self::$pdo->prepare(
                "INSERT INTO rate_limits (identifier, action) VALUES (?, ?)"
            );
            $stmt->execute([$identifier, "post"]);
        }

        public static function recordComment($identifier) 
        {
            $stmt = self::$pdo->prepare(
                "INSERT INTO rate_limits (identifier, action) VALUES (?, ?)"
            );
            $stmt->execute([$identifier, "comment"]);
        }

        public static function tooManyLogins($identifier) 
        {
            $stmt = self::$pdo->prepare(
                "SELECT COUNT(*) FROM rate_limits
                WHERE identifier = ? AND action = ?
                AND created_at > (NOW() - INTERVAL ? SECOND)"
            );
            $stmt->execute([$identifier, "login", self::$loginWindowSeconds]);
            $count = (int)$stmt->fetchColumn();

            return $count >= self::$loginMaxAttempts;
        }

        public static function tooManyPosts($identifier) 
        {
            $stmt = self::$pdo->prepare(
                "SELECT COUNT(*) FROM rate_limits
                WHERE identifier = ? AND action = ?
                AND created_at > (NOW() - INTERVAL ? SECOND)"
            );
            $stmt->execute([$identifier, "post", self::$postWindowSeconds]);
            $count = (int)$stmt->fetchColumn();

            return $count >= self::$postMaxAttempts;
        }

        public static function tooManyComments($identifier) 
        {
            $stmt = self::$pdo->prepare(
                "SELECT COUNT(*) FROM rate_limits
                WHERE identifier = ? AND action = ?
                AND created_at > (NOW() - INTERVAL ? SECOND)"
            );
            $stmt->execute([$identifier, "comment", self::$commentWindowSeconds]);
            $count = (int)$stmt->fetchColumn();

            return $count >= self::$commentMaxAttempts;
        }
    }