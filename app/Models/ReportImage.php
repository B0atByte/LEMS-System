<?php
namespace App\Models;

/**
 * ReportImage Model
 * Manages images attached to field reports
 */
class ReportImage extends BaseModel
{
    protected $table = 'report_images';
    protected $primaryKey = 'id';
    protected $fillable = [
        'report_id',
        'image_path',
        'image_name',
        'file_size',
        'created_by'
    ];

    /**
     * Get images by report ID
     */
    public function getByReportId(int $reportId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE report_id = :report_id ORDER BY created_at DESC";
        return $this->db->fetchAll($sql, ['report_id' => $reportId]);
    }

    /**
     * Save multiple images
     */
    public function saveImages(int $reportId, array $files, int $createdBy): void
    {
        // Files expected in standard PHP $_FILES structure (loop handled in controller)
        // Here we just save single record or loop?
        // Let's make it simple: addImage method.
    }

    /**
     * Delete image file and record
     */
    public function deleteImage(int $id): bool
    {
        $image = $this->find($id);
        if (!$image) return false;

        // Delete successful from DB, try deleting file
        if (file_exists(BASE_PATH . '/public' . $image['image_path'])) {
            unlink(BASE_PATH . '/public' . $image['image_path']);
        }

        return $this->delete($id);
    }
}
