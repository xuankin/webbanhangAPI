<?php
require_once 'app/config/database.php';
require_once 'app/models/CategoryModel.php';
require_once 'app/helpers/SessionHelper.php';

class CategoryApiController
{
    private $categoryModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->categoryModel = new CategoryModel($this->db);
    }

    // GET /api/categories - Lấy danh sách danh mục
    public function index()
    {
        header('Content-Type: application/json');
        $categories = $this->categoryModel->getCategories();
        echo json_encode(['status' => 'success', 'data' => $categories]);
    }

    // GET /api/categories/{id} - Lấy thông tin danh mục theo ID
    public function show($id)
    {
        header('Content-Type: application/json');
        $category = $this->categoryModel->getCategoryById($id);
        if ($category) {
            echo json_encode(['status' => 'success', 'data' => $category]);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Danh mục không tồn tại']);
        }
    }

    // POST /api/admin/categories - Thêm danh mục mới
    public function store()
    {
        header('Content-Type: application/json');

        // Kiểm tra quyền admin (đã được xử lý trong model, nhưng để rõ ràng hơn, có thể kiểm tra ở đây nếu cần)
        if (!SessionHelper::isAdmin()) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Bạn không có quyền thêm danh mục']);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        if (empty($data)) {
            $data = $_POST;
        }

        $name = isset($data['name']) ? $data['name'] : '';
        $description = isset($data['description']) ? $data['description'] : '';

        $result = $this->categoryModel->addCategory($name, $description);
        if (is_array($result)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'errors' => $result]);
        } else if ($result) {
            http_response_code(201);
            echo json_encode(['status' => 'success', 'message' => 'Thêm danh mục thành công']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Không thể thêm danh mục']);
        }
    }

    // PUT /api/admin/categories/{id} - Cập nhật danh mục theo ID
    public function update($id)
    {
        header('Content-Type: application/json');

        // Kiểm tra quyền admin
        if (!SessionHelper::isAdmin()) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Bạn không có quyền sửa danh mục']);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        if (empty($data)) {
            $data = $_POST;
        }

        $name = isset($data['name']) ? $data['name'] : '';
        $description = isset($data['description']) ? $data['description'] : '';

        $result = $this->categoryModel->updateCategory($id, $name, $description);
        if (is_array($result)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'errors' => $result]);
        } else if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Cập nhật danh mục thành công']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Không thể cập nhật danh mục']);
        }
    }

    // DELETE /api/admin/categories/{id} - Xóa danh mục theo ID
    public function destroy($id)
    {
        header('Content-Type: application/json');

        // Kiểm tra quyền admin
        if (!SessionHelper::isAdmin()) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Bạn không có quyền xóa danh mục']);
            return;
        }

        $result = $this->categoryModel->deleteCategory($id);
        if (is_array($result)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $result['error']]);
        } else if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Xóa danh mục thành công']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Không thể xóa danh mục']);
        }
    }
}
?>