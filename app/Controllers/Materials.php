<?php
namespace App\Controllers;
use CodeIgniter\Controller;
use App\Models\MaterialModel;
use App\Models\CourseModel;
class Materials extends BaseController
{
    protected $materialModel;
    protected $courseModel;

    protected $enrollmentModel;

    public function __construct()
    {
        $this->materialModel = new MaterialModel();
        $this->courseModel = new CourseModel();
        helper(['form', 'url',]);
    }
    
    /**
     * Get upload error message by error code
     */
    protected function getUploadErrorMessage($errorCode)
    {
        $errors = [
            UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload',
        ];
        
        return $errors[$errorCode] ?? 'Unknown upload error';
    }

    public function uploadForm($courseId = null)
    {
        // Check if user is logged in and has the right role
        if (!session()->get('isLoggedIn') || !in_array(strtolower(session()->get('role')), ['admin', 'teacher'])) {
            log_message('debug', 'User not logged in or not authorized. Role: ' . (session()->get('role') ?? 'none'));
            return redirect()->to('/login')->with('error', 'You do not have permission to access this page');
        }

        $course = $this->courseModel->find($courseId);
        
        if (!$course) {
            return redirect()->to('/admin/courses')->with('error', 'Course not found');
        }

        return view('materials/upload', [
            'title' => 'Upload Material',
            'course' => $course
        ]);
    }

    public function upload($courseId = null)
{
    try {
        $file = $this->request->getFile('material');
        
        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $uploadPath = 'uploads/materials/' . date('Y/m/d');
            
            // Create directory if it doesn't exist
            if (!is_dir(WRITEPATH . $uploadPath)) {
                mkdir(WRITEPATH . $uploadPath, 0777, true);
            }
            
            $file->move(WRITEPATH . $uploadPath, $newName);
            $filePath = $uploadPath . '/' . $newName;

            // Save to database
            $data = [
                'course_id' => $courseId,
                'file_name' => $file->getClientName(),
                'file_path' => $filePath,
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($this->materialModel->insert($data)) {
                return redirect()->back()->with('success', 'File uploaded successfully');
            }
        }
        
        return redirect()->back()->with('error', 'Failed to upload file');
        
    } catch (\Exception $e) {
        log_message('error', 'Upload error: ' . $e->getMessage());
        return redirect()->back()->with('error', $e->getMessage());
    }
}
    public function download($id = null)
{
    try {
        $material = $this->materialModel->find($id);
        if (!$material) {
            throw new \RuntimeException('File not found');
        }

        // Check if user is enrolled in the course (for students)
        // or is the instructor (for teachers)
        $userId = session()->get('userID');
        $userRole = session()->get('role');
        
        $hasAccess = false;
        
        if ($userRole === 'student') {
            $enrollment = $this->enrollmentModel->where([
                'student_id' => $userId,
                'course_id' => $material['course_id']
            ])->first();
            
            $hasAccess = (bool)$enrollment;
        } else if (in_array(strtolower($userRole), ['admin', 'teacher'])) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            throw new \RuntimeException('You do not have permission to download this file');
        }

        $filePath = ROOTPATH . 'public/' . $material['file_path'];
        
        if (file_exists($filePath)) {
            return $this->response->download($filePath, null, true);
        }

        throw new \RuntimeException('File not found on server');

    } catch (\Exception $e) {
        log_message('error', 'Download error: ' . $e->getMessage());
        return redirect()->back()->with('error', $e->getMessage());
    }
}

    public function delete($courseId = null, $materialId = null)
{
    try {
        $material = $this->materialModel->find($materialId);
        
        if (!$material) {
            return redirect()->back()->with('error', 'Material not found');
        }

        // Delete the file
        $filePath = WRITEPATH . 'uploads/' . $material['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete from database
        $this->materialModel->delete($materialId);

        return redirect()->back()->with('success', 'Material deleted successfully');
        
    } catch (\Exception $e) {
        log_message('error', 'Error deleting material: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to delete material: ' . $e->getMessage());
    }
}
    
    public function view($courseId = null)
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please log in to view materials');
        }

        $course = $this->courseModel->find($courseId);
        
        if (!$course) {
            return redirect()->back()->with('error', 'Course not found');
        }

        // Get materials for this course
        $materials = $this->materialModel->where('course_id', $courseId)
                                       ->orderBy('created_at', 'DESC')
                                       ->findAll();

        return view('materials/view', [
            'title' => 'Course Materials',
            'course' => $course,
            'materials' => $materials
        ]);
    }
}