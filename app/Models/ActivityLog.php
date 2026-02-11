<?php
namespace App\Models;

/**
 * ActivityLog Model
 * Manages activity logs and audit trail
 */
class ActivityLog extends BaseModel
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'username',
        'action_type',
        'module',
        'reference_id',
        'description',
        'old_data',
        'new_data',
        'ip_address',
        'user_agent'
    ];

    /**
     * Log an activity
     */
    public function log(
        int $userId,
        string $username,
        string $actionType,
        string $module,
        ?int $referenceId = null,
        string $description = '',
        ?array $oldData = null,
        ?array $newData = null,
        string $ipAddress = '',
        string $userAgent = ''
    ): int {
        $data = [
            'user_id' => $userId,
            'username' => $username,
            'action_type' => $actionType,
            'module' => $module,
            'reference_id' => $referenceId,
            'description' => $description,
            'old_data' => $oldData ? json_encode($oldData, JSON_UNESCAPED_UNICODE) : null,
            'new_data' => $newData ? json_encode($newData, JSON_UNESCAPED_UNICODE) : null,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent
        ];

        return $this->create($data);
    }

    /**
     * Get activity logs with pagination and filters
     */
    public function getActivityLogs(int $page = 1, int $perPage = 20, array $filters = []): array
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

        if (!empty($filters['action_type'])) {
            $conditions[] = "action_type = :action_type";
            $params['action_type'] = $filters['action_type'];
        }

        if (!empty($filters['module'])) {
            $conditions[] = "module = :module";
            $params['module'] = $filters['module'];
        }

        if (!empty($filters['date_from'])) {
            $conditions[] = "DATE(created_at) >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $conditions[] = "DATE(created_at) <= :date_to";
            $params['date_to'] = $filters['date_to'];
        }

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} {$whereClause}";
        $countResult = $this->db->fetch($countSql, $params);
        $total = (int) $countResult['total'];

        // Get logs
        $sql = "SELECT * FROM {$this->table} {$whereClause} ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}";
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
     * Get audit trail for a specific record
     */
    public function getAuditTrail(string $module, int $referenceId): array
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE module = :module AND reference_id = :reference_id
                ORDER BY created_at DESC";

        return $this->db->fetchAll($sql, [
            'module' => $module,
            'reference_id' => $referenceId
        ]);
    }

    /**
     * Get user's recent activities
     */
    public function getUserRecentActivities(int $userId, int $limit = 10): array
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE user_id = :user_id
                ORDER BY created_at DESC
                LIMIT {$limit}";

        return $this->db->fetchAll($sql, ['user_id' => $userId]);
    }

    /**
     * Get activity statistics
     */
    public function getActivityStats(string $dateFrom = null, string $dateTo = null): array
    {
        $conditions = [];
        $params = [];

        if ($dateFrom) {
            $conditions[] = "DATE(created_at) >= :date_from";
            $params['date_from'] = $dateFrom;
        }

        if ($dateTo) {
            $conditions[] = "DATE(created_at) <= :date_to";
            $params['date_to'] = $dateTo;
        }

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $sql = "SELECT
                    action_type,
                    COUNT(*) as count
                FROM {$this->table}
                {$whereClause}
                GROUP BY action_type
                ORDER BY count DESC";

        return $this->db->fetchAll($sql, $params);
    }
}
