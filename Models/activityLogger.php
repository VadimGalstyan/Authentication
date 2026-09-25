<?php


    class ActivityLogger 
    {
    
        private static $pdo = null;

        public static function init($pdo) 
        {
            self::$pdo = $pdo;
        }

        public static function log($userId, $action, $targetType = null, $targetId = null) 
        {
            if (self::$pdo === null) 
            {
                
                return;
            }

            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;

            $stmt = self::$pdo->prepare(
                "INSERT INTO activity_log (user_id, action, target_type, target_id, ip_address)
                VALUES (?, ?, ?, ?, ?)"
            );

            $stmt->execute([$userId, $action, $targetType, $targetId, $ipAddress]);
        }


        public static function postCreated($userId, $postId) 
        {
            self::log($userId, 'post_created', 'post', $postId);
        }

        public static function postRestoredByAdmin($userId, $postId) 
        {
            self::log($userId, 'post_restored', 'post', $postId);
        }

        public static function postUpdated($userId, $postId) 
        {
            self::log($userId, 'post_updated', 'post', $postId);
        }

        public static function postDeleted($userId, $postId) 
        {
            self::log($userId, 'post_deleted', 'post', $postId);
        }

        public static function commentDeletedByModerator($moderatorId, $commentId) 
        {
            self::log($moderatorId, 'comment_deleted_by_moderator', 'comment', $commentId);
        }

        public static function roleChanged($adminId, $targetUserId) 
        {
            self::log($adminId, 'role_changed', 'user', $targetUserId);
        }

        public static function Login($userId) 
        {
            self::log($userId, 'login'); 
        }

        public static function Logout($userId) 
        {
            self::log($userId, 'logout'); 
        }

        public static function passwordReset($userId) 
        {
            self::log($userId, 'password_reset');
        }

        public static function emailVerificationResend($userId, $email)
        {
            self::log($userId, 'email_verification_sent',$email);
        }

        public static function registration($userId, $email)
        {
            self::log($userId, 'registration',$email);
        }
        
        public static function addComment($userId, $commentId)
        {
            self::log($userId, 'added_comment',"",$commentId);
        }
        public static function deleteComment($userId, $commentId)
        {
            self::log($userId, 'delete_comment',"",$commentId);
        }

        

    }