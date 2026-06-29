<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PracticumClassModel;
use App\Models\CourseModel;
use App\Models\LecturerModel;
use App\Models\AssistantModel;
use App\Models\StudentModel;
use App\Models\ClassLecturerModel;
use App\Models\ClassAssistantModel;
use App\Models\ClassStudentModel;
use App\Models\AcademicYearModel;
use App\Models\SemesterModel;
use App\Models\LaboratoryModel;
use App\Models\AssessmentTemplateModel;

class Kelas extends BaseController
{
    protected $practicumClassModel;
    protected $courseModel;
    protected $lecturerModel;
    protected $assistantModel;
    protected $studentModel;
    protected $classLecturerModel;
    protected $classAssistantModel;
    protected $classStudentModel;
    protected $academicYearModel;
    protected $semesterModel;
    protected $laboratoryModel;
    protected $templateModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->practicumClassModel = new PracticumClassModel();
        $this->courseModel = new CourseModel();
        $this->lecturerModel = new LecturerModel();
        $this->assistantModel = new AssistantModel();
        $this->studentModel = new StudentModel();
        $this->classLecturerModel = new ClassLecturerModel();
        $this->classAssistantModel = new ClassAssistantModel();
        $this->classStudentModel = new ClassStudentModel();
        $this->academicYearModel = new AcademicYearModel();
        $this->semesterModel = new SemesterModel();
        $this->laboratoryModel = new LaboratoryModel();
        $this->templateModel = new AssessmentTemplateModel();
    }

    public function index()
    {
        $classes = $this->practicumClassModel->getClassesWithDetails();

        // Add student count to each class
        foreach ($classes as &$class) {
            $class['student_count'] = $this->practicumClassModel->countStudents($class['id']);
        }

        $data = [
            'title' => 'Manajemen Kelas - Sisfo Praktikum',
            'classes' => $classes,
            'courses' => $this->courseModel->getActiveCourses(),
            'lecturers' => $this->lecturerModel->getLecturersWithUserInfo(),
            'assistants' => $this->assistantModel->getAssistantsWithUserInfo(),
            'students' => $this->studentModel->getStudentsWithUserInfo(),
            'academicYears' => $this->academicYearModel->where('is_active', 1)->findAll(),
            'semesters' => $this->semesterModel->where('is_active', 1)->findAll(),
            'laboratories' => $this->laboratoryModel->where('status', 'aktif')->findAll(),
            'templates' => $this->templateModel->where('is_active', 1)->findAll(),
        ];

        return view('admin/manajemen_kelas', $data);
    }

    /**
     * Store new class
     */
    public function store()
    {
        $rules = [
            'class_code' => 'required|max_length[50]|is_unique[practicum_classes.class_code]',
            'class_name' => 'required|max_length[150]',
            'course_id' => 'required|integer',
            'academic_year_id' => 'required|integer',
            'semester_id' => 'required|integer',
            'laboratory_id' => 'permit_empty|integer',
            'template_id' => 'permit_empty|integer',
            'capacity' => 'permit_empty|integer',
            'status' => 'required|in_list[draft,aktif,selesai,terkunci,diarsipkan]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();

        try {
            $now = date('Y-m-d H:i:s');

            $classData = [
                'course_id' => $this->request->getPost('course_id'),
                'academic_year_id' => $this->request->getPost('academic_year_id'),
                'semester_id' => $this->request->getPost('semester_id'),
                'laboratory_id' => $this->request->getPost('laboratory_id') ?: null,
                'template_id' => $this->request->getPost('template_id') ?: null,
                'class_code' => $this->request->getPost('class_code'),
                'class_name' => $this->request->getPost('class_name'),
                'status' => $this->request->getPost('status'),
                'description' => $this->request->getPost('description'),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $classId = $this->practicumClassModel->insert($classData);

            if (!$classId) {
                throw new \Exception('Gagal membuat kelas');
            }

            // Assign lecturer if selected
            $lecturerId = $this->request->getPost('lecturer_id');
            if ($lecturerId) {
                $this->classLecturerModel->assignLecturer($classId, $lecturerId, 'pengampu');
            }

            // Assign assistant if selected
            $assistantId = $this->request->getPost('assistant_id');
            if ($assistantId) {
                $this->classAssistantModel->assignAssistant($classId, $assistantId, true);
            }

            // Log activity
            $db->table('activity_logs')->insert([
                'user_id' => session()->get('user_id') ?? '0000000001',
                'action' => 'create',
                'module' => 'practicum_class',
                'target_type' => 'practicum_class',
                'target_id' => $classId,
                'description' => "Membuat kelas praktikum baru: {$classData['class_name']}",
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString(),
                'created_at' => $now,
            ]);

            return redirect()->to('/admin/kelas')->with('success', 'Kelas berhasil dibuat!');
        } catch (\Exception $e) {
            log_message('error', '[Kelas::store] Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal membuat kelas: ' . $e->getMessage());
        }
    }

    /**
     * Update class
     */
    public function update()
    {
        $classId = $this->request->getPost('class_id');

        if (!$classId) {
            return redirect()->back()->with('error', 'ID kelas tidak valid');
        }

        $rules = [
            'class_id' => 'required|integer',
            'class_name' => 'required|max_length[150]',
            'course_id' => 'required|integer',
            'academic_year_id' => 'required|integer',
            'semester_id' => 'required|integer',
            'status' => 'required|in_list[draft,aktif,selesai,terkunci,diarsipkan]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $now = date('Y-m-d H:i:s');

            $classData = [
                'course_id' => $this->request->getPost('course_id'),
                'academic_year_id' => $this->request->getPost('academic_year_id'),
                'semester_id' => $this->request->getPost('semester_id'),
                'laboratory_id' => $this->request->getPost('laboratory_id') ?: null,
                'template_id' => $this->request->getPost('template_id') ?: null,
                'class_name' => $this->request->getPost('class_name'),
                'status' => $this->request->getPost('status'),
                'description' => $this->request->getPost('description'),
                'updated_at' => $now,
            ];

            $this->practicumClassModel->update($classId, $classData);

            // Update lecturer
            $lecturerId = $this->request->getPost('lecturer_id');
            if ($lecturerId) {
                $this->classLecturerModel->assignLecturer($classId, $lecturerId, 'pengampu');
            }

            // Update assistant
            $assistantId = $this->request->getPost('assistant_id');
            if ($assistantId) {
                $this->classAssistantModel->assignAssistant($classId, $assistantId, true);
            }

            return redirect()->to('/admin/kelas')->with('success', 'Kelas berhasil diperbarui!');
        } catch (\Exception $e) {
            log_message('error', '[Kelas::update] Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui kelas: ' . $e->getMessage());
        }
    }

    /**
     * Delete class (soft delete)
     */
    public function delete(int $classId)
    {
        try {
            $class = $this->practicumClassModel->find($classId);

            if (!$class) {
                return redirect()->back()->with('error', 'Kelas tidak ditemukan');
            }

            $this->practicumClassModel->delete($classId);

            // Log activity
            $db = \Config\Database::connect();
            $db->table('activity_logs')->insert([
                'user_id' => session()->get('user_id') ?? '0000000001',
                'action' => 'delete',
                'module' => 'practicum_class',
                'target_type' => 'practicum_class',
                'target_id' => $classId,
                'description' => "Menghapus kelas: {$class['class_name']}",
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/kelas')->with('success', 'Kelas berhasil dihapus!');
        } catch (\Exception $e) {
            log_message('error', '[Kelas::delete] Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus kelas: ' . $e->getMessage());
        }
    }

    /**
     * Get class info for modal (AJAX)
     */
    public function getClassInfo(int $classId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        try {
            $class = $this->practicumClassModel->getClassDetail($classId);

            if (!$class) {
                return $this->response->setJSON(['success' => false, 'message' => 'Kelas tidak ditemukan']);
            }

            $members = $this->practicumClassModel->getClassMembers($classId);

            return $this->response->setJSON([
                'success' => true,
                'class' => $class,
                'members' => $members,
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Add student to class (AJAX)
     */
    public function addStudent()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false]);
        }

        $classId = $this->request->getPost('class_id');
        $studentNim = $this->request->getPost('student_nim');

        if (!$classId || !$studentNim) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak lengkap']);
        }

        $result = $this->classStudentModel->addStudent($classId, $studentNim);

        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Mahasiswa berhasil ditambahkan',
                'total' => $this->classStudentModel->countByClassId($classId),
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Mahasiswa sudah terdaftar atau gagal ditambahkan']);
    }

    /**
     * Remove student from class (AJAX)
     */
    public function removeStudent()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false]);
        }

        $classId = $this->request->getPost('class_id');
        $studentNim = $this->request->getPost('student_nim');

        if (!$classId || !$studentNim) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak lengkap']);
        }

        $result = $this->classStudentModel->removeStudent($classId, $studentNim);

        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Mahasiswa berhasil dihapus',
                'total' => $this->classStudentModel->countByClassId($classId),
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus mahasiswa']);
    }
}
