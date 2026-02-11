<?php
namespace App\Controllers;

use App\Models\Cases;
use App\Models\ActivityLog;

/**
 * CaseController
 * Manages legal enforcement cases
 */
class CaseController extends BaseController
{
    private $caseModel;
    private $activityLogModel;

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->caseModel = new Cases();
        $this->activityLogModel = new ActivityLog();
    }

    /**
     * List all cases
     */
    public function index(): void
    {
        $this->requireAuth();

        $page = (int) $this->request->query('page', 1);
        $perPage = 20;
        $keyword = $this->request->query('search', '');
        $status = $this->request->query('status', '');

        $result = $this->caseModel->searchPaginate($keyword, $status, $page, $perPage);

        $this->view('cases/index', [
            'pageTitle' => 'จัดการงานบังคับคดี',
            'cases' => $result['items'],
            'pagination' => $result,
            'search' => $keyword,
            'status' => $status
        ]);
    }

    /**
     * Show create case form
     */
    public function create(): void
    {
        $this->requireAuth();

        $this->view('cases/create', [
            'pageTitle' => 'เพิ่มงานบังคับคดีใหม่'
        ]);
    }

    /**
     * Store new case
     */
    public function store(): void
    {
        $this->requireAuth();

        if (!$this->request->isPost() || !$this->verifyCsrf()) {
            $this->redirect('/cases');
            return;
        }

        // Validate input
        $errors = $this->request->validate([
            'debtor_name' => 'required',
            'contract_no' => 'required',
            'citizen_id' => 'required|min:13|max:13'
        ]);

        if (!empty($errors)) {
            $this->setFlash('error', 'กรุณากรอกข้อมูลสำคัญให้ครบถ้วน (ชื่อลูกหนี้, เลขสัญญา, เลขบัตรประชาชน 13 หลัก)');
            $this->redirect('/cases/create');
            return;
        }

        $items = [
            'product', 'debtor_name', 'citizen_id', 'address', 'phone', 
            'contract_no', 'contract_date', 'court', 'black_case', 
            'red_case', 'filing_date', 'judgment_date', 'enforcement_status', 
            'principal_amount', 'interest_amount', 'total_amount', 'notes'
        ];
        
        $data = [];
        foreach ($items as $item) {
            $value = $this->request->input($item);
            // Handle numeric fields if empty string
            if (in_array($item, ['principal_amount', 'interest_amount', 'total_amount']) && $value === '') {
                $value = 0;
            }
            // Handle date fields if empty string
            if (in_array($item, ['contract_date', 'filing_date', 'judgment_date']) && $value === '') {
                $value = null;
            }
            $data[$item] = $value;
        }

        $data['status'] = 'active';
        $data['created_by'] = $this->getUserId();

        // Create case
        try {
            $caseId = $this->caseModel->create($data);

            // Log activity
            $this->activityLogModel->log(
                $this->getUserId(),
                $this->getUsername(),
                'CREATE',
                'cases',
                $caseId,
                "Created new case: {$data['contract_no']} - {$data['debtor_name']}",
                null,
                $data,
                $this->getClientIp(),
                $this->getUserAgent()
            );

            $this->setFlash('success', 'เพิ่มงานบังคับคดีสำเร็จ');
            $this->redirect('/cases');
        } catch (\Exception $e) {
            $this->setFlash('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
            $this->redirect('/cases/create');
        }
    }

    /**
     * Show case details
     */
    public function show(): void
    {
        $this->requireAuth();

        $id = $this->request->param('id');
        $case = $this->caseModel->find($id);

        if (!$case) {
            $this->response->notFound();
            return;
        }

        // Get assignments
        $assignmentModel = new \App\Models\Assignment();
        $assignments = $assignmentModel->getDetails(['case_id' => $id]);

        $this->view('cases/view', [
            'pageTitle' => 'รายละเอียดงานบังคับคดี',
            'case' => $case,
            'assignments' => $assignments
        ]);
    }

    /**
     * Show edit case form
     */
    public function edit(): void
    {
        $this->requireAuth();

        $id = $this->request->param('id');
        $case = $this->caseModel->find($id);

        if (!$case) {
            $this->response->notFound();
            return;
        }

        $this->view('cases/edit', [
            'pageTitle' => 'แก้ไขงานบังคับคดี',
            'case' => $case
        ]);
    }

    /**
     * Update case
     */
    public function update(): void
    {
        $this->requireAuth();

        if (!$this->request->isPost() || !$this->verifyCsrf()) {
            $this->redirect('/cases');
            return;
        }

        $id = $this->request->param('id');
        $oldCase = $this->caseModel->find($id);

        if (!$oldCase) {
            $this->response->notFound();
            return;
        }

        $items = [
            'product', 'debtor_name', 'citizen_id', 'address', 'phone', 
            'contract_no', 'contract_date', 'court', 'black_case', 
            'red_case', 'filing_date', 'judgment_date', 'enforcement_status', 
            'principal_amount', 'interest_amount', 'total_amount', 'notes', 'status'
        ];
        
        $data = [];
        foreach ($items as $item) {
            if ($this->request->has($item)) { // check if key exists in POST
               $value = $this->request->input($item);
               // Handle numeric fields if empty string
               if (in_array($item, ['principal_amount', 'interest_amount', 'total_amount']) && $value === '') {
                   $value = 0;
               }
               // Handle date fields if empty string
               if (in_array($item, ['contract_date', 'filing_date', 'judgment_date']) && $value === '') {
                   $value = null;
               }
               $data[$item] = $value;
            }
        }
        
        $data['updated_by'] = $this->getUserId();

        // Update case
        try {
            $this->caseModel->update($id, $data);

            // Log activity
            $this->activityLogModel->log(
                $this->getUserId(),
                $this->getUsername(),
                'UPDATE',
                'cases',
                $id,
                "Updated case: {$oldCase['contract_no']}",
                $oldCase, // simplified old data, usually specific changed fields
                $data,
                $this->getClientIp(),
                $this->getUserAgent()
            );

            $this->setFlash('success', 'แก้ไขข้อมูลสำเร็จ');
            $this->redirect('/cases/' . $id);
        } catch (\Exception $e) {
            $this->setFlash('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
            $this->redirect('/cases/' . $id . '/edit');
        }
    }

    /**
     * Delete case
     */
    public function delete(): void
    {
        $this->requireRole(['super_admin', 'admin']);

        if (!$this->request->isPost() || !$this->verifyCsrf()) {
            $this->redirect('/cases');
            return;
        }

        $id = $this->request->param('id');
        $case = $this->caseModel->find($id);

        if (!$case) {
            $this->response->notFound();
            return;
        }
        
        $this->caseModel->delete($id);

        // Log activity
        $this->activityLogModel->log(
            $this->getUserId(),
            $this->getUsername(),
            'DELETE',
            'cases',
            $id,
            "Deleted case: {$case['contract_no']}",
            $case,
            null,
            $this->getClientIp(),
            $this->getUserAgent()
        );

        $this->setFlash('success', 'ลบข้อมูลสำเร็จ');
        $this->redirect('/cases');
    }
}
