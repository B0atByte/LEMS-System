<?php
namespace App\Controllers;

use App\Models\FieldReport;
use App\Models\ReportImage;
use App\Models\Assignment;
use App\Models\ActivityLog;
use App\Models\User;

/**
 * FieldReportController
 * Manages field reports, GPS tracking, and image uploads
 */
class FieldReportController extends BaseController
{
    private $fieldReportModel;
    private $reportImageModel;
    private $assignmentModel;
    private $activityLogModel;

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->fieldReportModel = new FieldReport();
        $this->reportImageModel = new ReportImage();
        $this->assignmentModel = new Assignment();
        $this->activityLogModel = new ActivityLog();
    }

    /**
     * List assignments for currently logged-in officer
     */
    public function myAssignments(): void
    {
        $this->requireRole(['officer']);

        $officerId = $this->getUserId();
        
        $page = (int) $this->request->query('page', 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        // Fetch officer's assignments
        // Using assignmentModel->getDetails which joins with cases
        $conditions = ['officer_id' => $officerId];
        $status = $this->request->query('status');
        if ($status) {
            $conditions['status'] = $status;
        }

        $assignments = $this->assignmentModel->getDetails($conditions, $perPage, $offset);
        $total = $this->assignmentModel->countDetails($conditions);
        $totalPages = ceil($total / $perPage);

        $this->view('field_reports/my_assignments', [
            'pageTitle' => 'งานของฉัน',
            'assignments' => $assignments,
             'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total' => $total,
                'start' => ($total > 0) ? $offset + 1 : 0,
                'end' => min($offset + $perPage, $total),
                'has_more' => $page < $totalPages
            ],
            'status' => $status
        ]);
    }

    /**
     * Start working on an assignment (Check-in / Acknowledge)
     */
    public function startWork(): void
    {
        $this->requireRole(['officer']);

        $assignmentId = $this->request->param('id');
        $assignment = $this->assignmentModel->find($assignmentId);

        if (!$assignment || $assignment['officer_id'] != $this->getUserId()) {
            $this->response->forbidden('คุณไม่มีสิทธิ์ในงานนี้');
            return;
        }

        if ($assignment['status'] == 'assigned') {
            $this->assignmentModel->update($assignmentId, [
                'status' => 'in_progress',
                'work_date' => date('Y-m-d H:i:s'),
                'updated_by' => $this->getUserId()
            ]);

            $this->activityLogModel->log(
                $this->getUserId(),
                $this->getUsername(),
                'START_WORK',
                'assignments',
                $assignmentId,
                "Started work on assignment #{$assignmentId}",
                null,
                ['status' => 'in_progress'],
                $this->getClientIp(),
                $this->getUserAgent()
            );

            $this->setFlash('success', 'เริ่มปฏิบัติงานแล้ว');
        }

        $this->redirect('/assignments/' . $assignmentId . '/report');
    }

    /**
     * Show report form
     */
    public function createReport(): void
    {
        $this->requireRole(['officer']);

        $assignmentId = $this->request->param('id');
        
        // Use full detail for context
        $assignments = $this->assignmentModel->getDetails(['id' => $assignmentId], 1, 0); 
        // Oh wait getDetails returns array of arrays, but we map conditions differently in model..
        // Actually getDetails takes conditions array.. but 'id' condition needs to be handled in Assignment::getDetails 
        // Let's modify Assignment model or assume 'id' works if I fix it..
        // Or simpler, fetch basic assignment then find case.
        
        // Actually, let's look at Assignment::getDetails logic again.
        // It handles officer_id, status, case_id.. but not 'id' (assignment id).
        // Let's fetch basic assignment first.
        
        $assignment = $this->assignmentModel->find($assignmentId);
        
        if (!$assignment || $assignment['officer_id'] != $this->getUserId()) {
            $this->response->forbidden('คุณไม่มีสิทธิ์รายงานผลงานนี้');
            return;
        }

        // Get case info separately 
        $caseModel = new \App\Models\Cases();
        $case = $caseModel->find($assignment['case_id']);
        
        // Check if report already exists? If yes, edit instead?
        $existingReport = $this->fieldReportModel->findByAssignmentId($assignmentId);
        if ($existingReport) {
            $this->redirect("/reports/{$existingReport['id']}/edit");
            return;
        }

        $this->view('field_reports/create', [
            'pageTitle' => 'ส่งรายงานผลการปฏิบัติงาน',
            'assignment' => $assignment,
            'case' => $case
        ]);
    }

    /**
     * Store field report
     */
    public function storeReport(): void
    {
        $this->requireRole(['officer']);

        if (!$this->request->isPost() || !$this->verifyCsrf()) {
            $this->redirect('/my-assignments');
            return;
        }

        $assignmentId = $this->request->param('id');
        $assignment = $this->assignmentModel->find($assignmentId);

        if (!$assignment || $assignment['officer_id'] != $this->getUserId()) {
            $this->response->forbidden();
            return;
        }

        // Validate
        $errors = $this->request->validate([
            'asset_investigation' => 'required',
            // other fields optional/conditional
        ]);

        if (!empty($errors)) {
            $this->setFlash('error', 'กรุณาระบุผลการตรวจสอบทรัพย์');
            $this->redirect("/assignments/{$assignmentId}/report");
            return;
        }

        $data = [
            'assignment_id' => $assignmentId,
            'asset_investigation' => $this->request->input('asset_investigation'),
            'seized_asset_type' => $this->request->input('seized_asset_type'),
            'enforcement_status' => $this->request->input('enforcement_status'),
            'report_detail' => $this->request->input('report_detail'),
            'extra_detail' => $this->request->input('extra_detail'),
            'latitude' => $this->request->input('latitude'),
            'longitude' => $this->request->input('longitude'),
            'location_accuracy' => $this->request->input('accuracy'),
            'created_by' => $this->getUserId()
        ];

        try {
            // Begin Transaction
            $this->fieldReportModel->beginTransaction();

            // Create Report
            $reportId = $this->fieldReportModel->create($data);

            // Handle File Uploads
            if (!empty($_FILES['images']['name'][0])) {
                $uploadDir = BASE_PATH . '/public/uploads/reports/' . date('Y/m');
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $files = $_FILES['images'];
                $count = count($files['name']);

                for ($i = 0; $i < $count; $i++) {
                    if ($files['error'][$i] === UPLOAD_ERR_OK) {
                        $tmpName = $files['tmp_name'][$i];
                        $originalName = $files['name'][$i];
                        $size = $files['size'][$i];
                        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                        
                        // Validate extension
                        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                            continue;
                        }

                        $newFileName = uniqid('report_') . '_' . time() . '.' . $ext;
                        $destination = $uploadDir . '/' . $newFileName;
                        $publicPath = '/uploads/reports/' . date('Y/m') . '/' . $newFileName;

                        if (move_uploaded_file($tmpName, $destination)) {
                            // Resize image if strictly needed (skip for now to keep quality high/speed)
                            
                            $this->reportImageModel->create([
                                'report_id' => $reportId,
                                'image_path' => $publicPath,
                                'image_name' => $originalName,
                                'file_size' => $size,
                                'created_by' => $this->getUserId()
                            ]);
                        }
                    }
                }
            }

            // Update Assignment Status to Completed
            $this->assignmentModel->update($assignmentId, [
                'status' => 'completed',
                'updated_by' => $this->getUserId()
            ]);

            // Log
            $this->activityLogModel->log(
                $this->getUserId(),
                $this->getUsername(),
                'UPLOAD',
                'field_reports',
                $reportId,
                "Submitted field report for assignment #{$assignmentId}",
                null, 
                $data,
                $this->getClientIp(),
                $this->getUserAgent()
            );

            $this->fieldReportModel->commit();
            $this->setFlash('success', 'ส่งรายงานเรียบร้อยแล้ว');
            $this->redirect('/my-assignments');

        } catch (\Exception $e) {
            $this->fieldReportModel->rollback();
            $this->setFlash('error', 'เกิดข้อผิดพลาดในการบันทึก: ' . $e->getMessage());
            $this->redirect("/assignments/{$assignmentId}/report");
        }
    }

    /**
     * Show report details (View only)
     */
    public function show(): void
    {
        $this->requireAuth();
        
        $id = $this->request->param('id');
        $report = $this->fieldReportModel->find($id);

        if (!$report) {
            $this->response->notFound();
            return;
        }
        
        // Check permissions: Owner, Admin, Super Admin
        $userRole = $this->getUserRole();
        if ($userRole == 'officer' && $report['created_by'] != $this->getUserId()) {
            $this->response->forbidden();
            return;
        }

        // Get images
        $images = $this->reportImageModel->getByReportId($id);
        
        // Get Assignment & Case info
        $assignment = $this->assignmentModel->find($report['assignment_id']);
        $caseModel = new \App\Models\Cases();
        $case = $caseModel->find($assignment['case_id']);
        
        // Get Officer info
        $userModel = new User();
        $officer = $userModel->find($assignment['officer_id']);

        $this->view('field_reports/show', [
            'pageTitle' => 'รายละเอียดรายงาน',
            'report' => $report,
            'images' => $images,
            'assignment' => $assignment,
            'case' => $case,
            'officer' => $officer
        ]);
    }
    
    /**
     * Edit report (If allowed)
     */
    public function edit(): void
    {
         $this->requireRole(['officer', 'admin', 'super_admin']);
         // Implement edit logic similar to create but pre-filled
         // Placeholder for now as per minimal requirement, but let's redirect to show or implement basic
         $id = $this->request->param('id');
         $this->redirect("/reports/{$id}");
    }
    
    /**
     * Approve report
     */
    public function approve(): void
    {
        $this->requireRole(['super_admin', 'admin']);
        
        if (!$this->request->isPost() || !$this->verifyCsrf()) {
            $this->redirect('/assignments'); // Or reports list
            return;
        }
        
        $id = $this->request->param('id');
        
        $this->fieldReportModel->update($id, [
            'approved_by_admin' => 1,
            'approved_by' => $this->getUserId(),
            'approved_at' => date('Y-m-d H:i:s'),
            'updated_by' => $this->getUserId()
        ]);
        
        $this->setFlash('success', 'อนุมัติรายงานเรียบร้อยแล้ว');
        
        // Redirect back to wherever they came from? Usually show page.
        $this->redirect("/reports/{$id}");
    }
}
