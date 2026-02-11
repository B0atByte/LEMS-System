<?php
namespace App\Models;

/**
 * LoginLog Model
 * Manages login/logout logs
 */
class LoginLog extends BaseModel
{
    protected $table = 'login_logs';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'username',
        'ip_address',
        'user_agent',
        'login_time',
        'logout_time',
        'status',
        'failure_reason'
    ];

    /**
     * Log successful login
     */
    public function logLogin(int $userId, string $username, string $ipAddress, string $userAgent): int
    {
        $data = [
            'user_id' => $userId,
            'username' => $username,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'login_time' => date('Y-m-d H:i:s'),
            'status' => 'success'
        ];

        return $this->create($data);
    }

    /**
     * Log failed login attempt
     */
    public function logFailedLogin(string $username, string $ipAddress, string $userAgent, string $reason = ''): int
    {
        $data = [
            'username' => $username,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'login_time' => date('Y-m-d H:i:s'),
            'status' => 'failed',
            'failure_reason' => $reason
        ];

        return $this->create($data);
    }

    /**
     * Log logout
     */
    public function logLogout(int $logId): bool
    {
        $sql = "UPDATE {$this->table} SET logout_time = NOW() WHERE id = :id";
        return $this->db->execute($sql, ['id' => $logId]) > 0;
    }

    /**
     * Get login logs with pagination and filters
     */
    public function getLoginLogs(int $page = 1, int $perPage = 20, array $filters = []): array
    {
        $offset = ($page - 1) * $perPage;
        $conditions = [];
        $params = [];

        // Build WHERE clause
        if (!empty($filters['user_id'])) {
            $conditions[] = "user_id = :user_id";
            $params['user_id'] = $filters['user_id'];
        }

        if (!empty($filters['username'])) {
            $conditions[] = "username LIKE :username";
            $params['username'] = '%' . $filters['username'] . '%';
        }

        if (!empty($filters['status'])) {
            $conditions[] = "status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['date_from'])) {
            $conditions[] = "DATE(login_time) >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $conditions[] = "DATE(login_time) <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} {$whereClause}";
        $countResult = $this->db->fetch($countSql, $params);
        $total = (int) $countResult['total'];

        // Get logs
        $sql = "SELECT * FROM {$this->table} {$whereClause} ORDER BY login_time DESC LIMIT {$perPage} OFFSET {$offset}";
        $items = $this->db->fetchAll($sql, $params);

        return [
            'items' => $items,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Get recent login activity for a user
     */
    public function getUserRecentLogins(int $userId, int $limit = 10): array
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE user_id = :user_id
                ORDER BY login_time DESC
                LIMIT {$limit}";
        return $this->db->fetchAll($sql, ['user_id' => $userId]);
    }

    /**
     * Get failed login attempts
     */
    public function getFailedLoginAttempts(string $username = null, int $hours = 24): array
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE status = 'failed'
                AND login_time >= DATE_SUB(NOW(), INTERVAL :hours HOUR)";

        $params = ['hours' => $hours];

        if ($username) {
            $sql .= " AND username = :username";
            $params['username'] = $username;
        }

        $sql .= " ORDER BY login_time DESC";

        return $this->db->fetchAll($sql, $params);
    }
}
