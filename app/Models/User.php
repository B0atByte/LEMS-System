<?php
namespace App\Models;

/**
 * User Model
 * Manages user data and authentication
 */
class User extends BaseModel
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'fullname',
        'username',
        'password',
        'role',
        'status',
        'last_login',
        'created_by',
        'updated_by'
    ];

    /**
     * Find user by username
     */
    public function findByUsername(string $username): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username LIMIT 1";
        return $this->db->fetch($sql, ['username' => $username]);
    }

    /**
     * Verify user credentials
     */
    public function verifyCredentials(string $username, string $password): ?array
    {
        $user = $this->findByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }

        return null;
    }

    /**
     * Update last login time
     */
    public function updateLastLogin(int $userId): bool
    {
        $sql = "UPDATE {$this->table} SET last_login = NOW() WHERE id = :id";
        return $this->db->execute($sql, ['id' => $userId]) > 0;
    }

    /**
     * Create new user
     */
    public function createUser(array $data): int
    {
        // Hash password before storing
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        return $this->create($data);
    }

    /**
     * Update user password
     */
    public function updatePassword(int $userId, string $newPassword): bool
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE {$this->table} SET password = :password, updated_at = NOW() WHERE id = :id";
        return $this->db->execute($sql, [
            'password' => $hashedPassword,
            'id' => $userId
        ]) > 0;
    }

    /**
     * Toggle user status (active/inactive)
     */
    public function toggleStatus(int $userId): bool
    {
        $sql = "UPDATE {$this->table}
                SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END,
                    updated_at = NOW()
                WHERE id = :id";
        return $this->db->execute($sql, ['id' => $userId]) > 0;
    }

    /**
     * Get users by role
     */
    public function getUsersByRole(string $role): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE role = :role AND status = 'active' ORDER BY fullname ASC";
        return $this->db->fetchAll($sql, ['role' => $role]);
    }

    /**
     * Get all officers (for assignment)
     */
    public function getOfficers(): array
    {
        return $this->getUsersByRole('officer');
    }

    /**
     * Check if username exists
     */
    public function usernameExists(string $username, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE username = :username";
        $params = ['username' => $username];

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $result = $this->db->fetch($sql, $params);
        return $result['count'] > 0;
    }

    /**
     * Get user statistics by role
     */
    public function getUserStats(): array
    {
        $sql = "SELECT
                    role,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive
                FROM {$this->table}
                GROUP BY role";
        return $this->db->fetchAll($sql);
    }
}
