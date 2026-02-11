<?php
namespace App\Models;

/**
 * Assignment Model
 * Manages work assignments for officers
 */
class Assignment extends BaseModel
{
    protected $table = 'assignments';
    protected $primaryKey = 'id';
    protected $fillable = [
        'case_id',
        'officer_id',
        'assigned_date',
        'work_date',
        'status',
        'remarks',
        'created_by',
        'updated_by'
    ];

    /**
     * Get detailed assignments with related case and officer info
     */
    public function getDetails(array $conditions = [], int $limit = 20, int $offset = 0): array
    {
        $sql = "SELECT 
                    a.*,
                    c.contract_no,
                    c.debtor_name,
                    c.red_case,
                    c.address,
                    c.phone,
                    u.fullname as officer_name,
                    u.username as officer_username,
                    fr.id as report_id
                FROM {$this->table} a
                JOIN cases c ON a.case_id = c.id
                JOIN users u ON a.officer_id = u.id
                LEFT JOIN field_reports fr ON a.id = fr.assignment_id";

        $where = [];
        $params = [];

        if (!empty($conditions)) {
            foreach ($conditions as $key => $value) {
                // Handle special filters if needed
                if ($key == 'officer_id') {
                    $where[] = "a.officer_id = :officer_id";
                    $params['officer_id'] = $value;
                } elseif ($key == 'status') {
                    $where[] = "a.status = :status";
                    $params['status'] = $value;
                } elseif ($key == 'case_id') {
                    $where[] = "a.case_id = :case_id";
                    $params['case_id'] = $value;
                }
            }
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $sql .= " ORDER BY a.assigned_date DESC, a.created_at DESC LIMIT {$limit} OFFSET {$offset}";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Count assignments with filters
     */
    public function countDetails(array $conditions = []): int
    {
        $sql = "SELECT COUNT(*) as total
                FROM {$this->table} a
                JOIN cases c ON a.case_id = c.id
                JOIN users u ON a.officer_id = u.id";
        
        $where = [];
        $params = [];

        if (!empty($conditions)) {
            foreach ($conditions as $key => $value) {
                if ($key == 'officer_id') {
                    $where[] = "a.officer_id = :officer_id";
                    $params['officer_id'] = $value;
                } elseif ($key == 'status') {
                    $where[] = "a.status = :status";
                    $params['status'] = $value;
                }
                // Add more filters as needed
            }
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $result = $this->db->fetch($sql, $params);
        return (int) ($result['total'] ?? 0);
    }
}
