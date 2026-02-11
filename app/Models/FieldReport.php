<?php
namespace App\Models;

/**
 * FieldReport Model
 * Manages field operation reports
 */
class FieldReport extends BaseModel
{
    protected $table = 'field_reports';
    protected $primaryKey = 'id';
    protected $fillable = [
        'assignment_id',
        'asset_investigation',
        'seized_asset_type',
        'enforcement_status',
        'report_detail',
        'extra_detail',
        'latitude',
        'longitude',
        'location_accuracy',
        'approved_by_admin',
        'approved_by',
        'approved_at',
        'created_by',
        'updated_by'
    ];

    /**
     * Get report details with images
     */
    public function getWithImages(int $id): ?array
    {
        $report = $this->find($id);
        
        if ($report) {
            $imageModel = new ReportImage();
            $report['images'] = $imageModel->getByReportId($id);
            
            // Get assignment details
            $assignmentModel = new Assignment(); 
            // We can implement a specific method in Assignment or do a manual join here if needed
            // But let's keep it simple for now, maybe fetch assignment separately in controller
        }

        return $report;
    }

    /**
     * Find report by assignment ID
     */
    public function findByAssignmentId(int $assignmentId): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE assignment_id = :assignment_id LIMIT 1";
        return $this->db->fetch($sql, ['assignment_id' => $assignmentId]);
    }

    /**
     * Get report data for export
     */
    public function getExportData(?string $from = null, ?string $to = null, ?int $officerId = null): array
    {
        $sql = "SELECT 
                    fr.*, 
                    c.contract_no, c.debtor_name, 
                    u.fullname as officer_name,
                    a.assigned_date
                FROM {$this->table} fr
                JOIN assignments a ON fr.assignment_id = a.id
                JOIN cases c ON a.case_id = c.id
                JOIN users u ON a.officer_id = u.id
                WHERE 1=1";
        
        $params = [];

        if ($from) {
            $sql .= " AND fr.created_at >= :from";
            $params['from'] = $from . ' 00:00:00';
        }
        if ($to) {
            $sql .= " AND fr.created_at <= :to";
            $params['to'] = $to . ' 23:59:59';
        }
        if ($officerId) {
            $sql .= " AND a.officer_id = :officer_id";
            $params['officer_id'] = $officerId;
        }

        $sql .= " ORDER BY fr.created_at DESC LIMIT 2000";

        return $this->db->fetchAll($sql, $params);
    }
}
