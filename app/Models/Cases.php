<?php
namespace App\Models;

/**
 * Cases Model
 * Manages legal enforcement cases
 */
class Cases extends BaseModel
{
    protected $table = 'cases';
    protected $primaryKey = 'id';
    protected $fillable = [
        'product',
        'debtor_name',
        'citizen_id',
        'address',
        'phone',
        'contract_no',
        'contract_date',
        'court',
        'black_case',
        'red_case',
        'filing_date',
        'judgment_date',
        'enforcement_status',
        'principal_amount',
        'interest_amount',
        'total_amount',
        'notes',
        'status',
        'created_by',
        'updated_by'
    ];

    /**
     * Search cases with pagination
     */
    public function searchPaginate(string $keyword = '', string $status = '', int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        
        $whereInfo = $this->buildSearchWhere($keyword, $status);
        $whereSql = $whereInfo['sql'];
        $params = $whereInfo['params'];

        // Get total count
        $sqlCount = "SELECT COUNT(*) as total FROM {$this->table} {$whereSql}";
        $totalResult = $this->db->fetch($sqlCount, $params);
        $total = (int) ($totalResult['total'] ?? 0);

        // Get items
        // Use direct integer interpolation for LIMIT/OFFSET to avoid PDO binding issues
        $sql = "SELECT * FROM {$this->table} {$whereSql} ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}";
        
        $items = $this->db->fetchAll($sql, $params);
        $totalPages = ceil($total / $perPage);

        return [
            'items' => $items,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => $totalPages,
            'has_more' => $page < $totalPages,
            'start' => ($total > 0) ? $offset + 1 : 0,
            'end' => min($offset + $perPage, $total)
        ];
    }

    /**
     * Build WHERE clause for search
     */
    private function buildSearchWhere(string $keyword, string $status): array
    {
        $conditions = [];
        $params = [];

        if (!empty($status)) {
            $conditions[] = "status = :status";
            $params['status'] = $status;
        }

        if (!empty($keyword)) {
            $keywordParam = "%{$keyword}%";
            $conditions[] = "(debtor_name LIKE :kw OR contract_no LIKE :kw OR citizen_id LIKE :kw OR black_case LIKE :kw OR red_case LIKE :kw)";
            $params['kw'] = $keywordParam;
        }

        $whereSql = "";
        if (!empty($conditions)) {
            $whereSql = "WHERE " . implode(' AND ', $conditions);
        }

        return ['sql' => $whereSql, 'params' => $params];
    }

    /**
     * Get data for export
     */
    public function getExportData(?string $from = null, ?string $to = null): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];
        
        if ($from) {
            $sql .= " AND created_at >= :from";
            $params['from'] = $from . ' 00:00:00';
        }
        if ($to) {
            $sql .= " AND created_at <= :to";
            $params['to'] = $to . ' 23:59:59';
        }
        $sql .= " ORDER BY created_at DESC LIMIT 2000"; 
        
        return $this->db->fetchAll($sql, $params);
    }
}
