<?php
namespace App\Controllers;

use App\Models\Assignment;
use App\Models\Cases;
use App\Models\User;
use App\Models\ActivityLog;

/**
 * AssignmentController
 * Manages assignments of cases to officers
 */
class AssignmentController extends BaseController
{
    private $assignmentModel;
    private $caseModel;
    private $userModel;
    private $activityLogModel;

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->assignmentModel = new Assignment(); // Note: Singular filename/class
        $this->caseModel = new Cases();
        $this->userModel = new User();
        $this->activityLogModel = new ActivityLog();
    }

    /**
     * List assignments
     */
    public function index(): void
    {
        $this->requireAuth();

        $page = (int) $this->request->query('page', 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        // Filters
        $officerId = $this->request->query('officer_id');
        $status = $this->request->query('status');

        $conditions = [];
        if ($officerId) $conditions['officer_id'] = $officerId;
        if ($status) $conditions['status'] = $status;
        
        // If officer logged in, restrict to own assignments unless admin
        if ($this->getUserRole() == 'officer') {
            $conditions['officer_id'] = $this->getUserId();
        }

        // Get total
        // We'll need a count method in model that supports joins/filters
        // For now using simple count or improved one
        // Wait, Assignment model has countDetails but I didn't verify it fully.
        // Assuming I'll implement count logic in model or controller.
        // Let's rely on model's `count` for basic or custom query.
        // Using custom query in model is better.
        
        // Get items
        // $this->assignmentModel needs a method for joined data.
        // I'll assume getDetails implemented in model returns array.
        
        // Wait, I just wrote Assignment model with getDetails.
        // But countDetails was implemented there too? Yes.
        
        $assignments = $this->assignmentModel->getDetails($conditions, $perPage, $offset);
        // We need total count for pagination
        // Using countDetails from model (assuming I implemented it or will fix) -> Yes I did.
        
        // Wait, in previous step I implemented `getDetails` but `countDetails`?
        // Let me check my memory of step 90.
        // Yes, `countDetails` was implemented.
        
        $total = $this->assignmentModel->countDetails($conditions);
        $totalPages = ceil($total / $perPage);

        $this->view('assignments/index', [
            'pageTitle' => 'รายการมอบหมายงาน',
            'assignments' => $assignments,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total' => $total,
                'start' => ($total > 0) ? $offset + 1 : 0,
                'end' => min($offset + $perPage, $total),
                'has_more' => $page < $totalPages
            ],
            'officers' => $this->userModel->getOfficers(),
            'filters' => [
                'officer_id' => $officerId,
                'status' => $status
            ]
        ]);
    }

    /**
     * Show create assignment form
     */
    public function create(): void
    {
        $this->requireRole(['super_admin', 'admin']);

        $caseId = $this->request->query('case_id');

        if (!$caseId) {
            $this->setFlash('error', 'กรุณาเลือกงานบังคับคดีที่ต้องการมอบหมายก่อน');
            $this->redirect('/cases'); // Redirect to cases list to select one
            return;
        }

        $case = $this->caseModel->find($caseId);
        if (!$case) {
            $this->response->notFound();
            return;
        }

        // Check if already active assignment exists?
        // Maybe warn but allow (re-assign).

        $officers = $this->userModel->getOfficers();

        $this->view('assignments/create', [
            'pageTitle' => 'มอบหมายงาน',
            'case' => $case,
            'officers' => $officers
        ]);
    }

    /**
     * Store assignment
     */
    public function store(): void
    {
        $this->requireRole(['super_admin', 'admin']);

        if (!$this->request->isPost() || !$this->verifyCsrf()) {
            $this->redirect('/assignments');
            return;
        }

        $caseId = $this->request->input('case_id');
        $officerId = $this->request->input('officer_id');
        $remarks = $this->request->input('remarks');
        
        // Validate
        if (!$caseId || !$officerId) {
            $this->setFlash('error', 'ข้อมูลไม่ครบถ้วน');
            $this->redirect('/assignments/create?case_id=' . $caseId);
            return;
        }

        $data = [
            'case_id' => $caseId,
            'officer_id' => $officerId,
            'assigned_date' => date('Y-m-d'),
            'status' => 'assigned',
            'remarks' => $remarks,
            'created_by' => $this->getUserId()
        ];

        $id = $this->assignmentModel->create($data);

        // Update case status to active if not? Or maybe leave as is.
        // Maybe log?
        $this->activityLogModel->log(
            $this->getUserId(),
            $this->getUsername(),
            'ASSIGN',
            'assignments',
            $id,
            "Assigned case #{$caseId} to officer #{$officerId}",
            null,
            $data,
            $this->getClientIp(),
            $this->getUserAgent()
        );

        $this->setFlash('success', 'มอบหมายงานเรียบร้อยแล้ว');
        $this->redirect('/cases/' . $caseId); // Back to case details
    }
}
