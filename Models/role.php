<?php
class Role
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById($roleId) : array|false
    {
        $stmt = $this->pdo->prepare('SELECT * FROM roles WHERE id = ?');
        $stmt->execute([$roleId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByName($roleName) : array|false
    {
        $stmt = $this->pdo->prepare('SELECT * FROM roles WHERE name = ?');
        $stmt->execute([$roleName]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllRoles() : array
    {
        $stmt = $this->pdo->query('SELECT * FROM roles ORDER BY id');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPermissionsForRole($roleId) : array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.name FROM permissions p
             JOIN role_permissions rp ON rp.permission_id = p.id
             WHERE rp.role_id = ?'
        );
        $stmt->execute([$roleId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}