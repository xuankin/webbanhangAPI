<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/helpers/SessionHelper.php';

class ProductApiController
{
    private $productModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    // GET /api/products - Lấy danh sách sản phẩm (hỗ trợ tìm kiếm, lọc và sắp xếp)
    public function index()
    {
        header('Content-Type: application/json');
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $category = isset($_GET['category']) ? $_GET['category'] : '';
        $sort = isset($_GET['sort']) ? $_GET['sort'] : '';

        $products = $this->productModel->getProducts($search, $category, $sort);
        echo json_encode(['status' => 'success', 'data' => $products]);
    }

    // GET /api/products/{id} - Lấy thông tin sản phẩm theo ID
    public function show($id)
    {
        header('Content-Type: application/json');
        $product = $this->productModel->getProductById($id);
        if ($product) {
            echo json_encode(['status' => 'success', 'data' => $product]);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không tồn tại']);
        }
    }

    // POST /api/admin/products - Thêm sản phẩm mới
    public function store()
    {
        header('Content-Type: application/json');

        // Kiểm tra quyền admin
        if (!SessionHelper::isAdmin()) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Bạn không có quyền thêm sản phẩm']);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        if (empty($data)) {
            $data = $_POST;
        }

        $name = isset($data['name']) ? $data['name'] : '';
        $description = isset($data['description']) ? $data['description'] : '';
        $price = isset($data['price']) ? $data['price'] : '';
        $category_id = isset($data['category_id']) ? $data['category_id'] : '';
        $image = '';

        // Xử lý upload file nếu có
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'app/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $imageName = uniqid() . '-' . basename($_FILES['image']['name']);
            $imagePath = $uploadDir . $imageName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                $image = $imagePath;
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Không thể upload hình ảnh']);
                return;
            }
        } elseif (isset($data['image'])) {
            $image = $data['image'];
        }

        $result = $this->productModel->addProduct($name, $description, $price, $category_id, $image);
        if (is_array($result)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'errors' => $result]);
        } else if ($result) {
            http_response_code(201);
            echo json_encode(['status' => 'success', 'message' => 'Thêm sản phẩm thành công']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Không thể thêm sản phẩm']);
        }
    }
public function update($id)
{
    header('Content-Type: application/json');

    // // Kiểm tra quyền admin
    // if (!SessionHelper::isAdmin()) {
    //     http_response_code(403);
    //     echo json_encode(['status' => 'error', 'message' => 'Bạn không có quyền sửa sản phẩm']);
    //     return;
    // }

    // Debug request
    error_log("Request method: " . $_SERVER['REQUEST_METHOD']);
    error_log("Raw input: " . file_get_contents("php://input"));

    // Xử lý dữ liệu từ PUT request
    $data = [];
    
    // Xử lý JSON nếu Content-Type là application/json
    if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
        $data = json_decode(file_get_contents("php://input"), true);
    } else {
        // Xử lý multipart/form-data hoặc application/x-www-form-urlencoded
        $putdata = file_get_contents("php://input");
        
        // Kiểm tra nếu dữ liệu có dạng JSON
        $jsonData = json_decode($putdata, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $data = $jsonData;
        } else {
            // Fallback - thử sử dụng $_POST (nếu sử dụng form bình thường)
            $data = $_POST;
        }
    }
    // Kiểm tra dữ liệu đầu vào
    if (empty($data) && empty($_FILES)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Không nhận được dữ liệu']);
        return;
    }

    // Lấy các giá trị từ dữ liệu với giá trị mặc định
    $name = isset($data['name']) ? trim($data['name']) : '';
    $description = isset($data['description']) ? trim($data['description']) : '';
    $price = isset($data['price']) ? trim($data['price']) : '';
    $category_id = isset($data['category_id']) ? trim($data['category_id']) : '';
    $image = isset($data['image']) ? trim($data['image']) : null;

    // Xử lý upload file nếu có
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'app/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $imageName = uniqid() . '-' . basename($_FILES['image']['name']);
        $imagePath = $uploadDir . $imageName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            $image = $imagePath;
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Không thể upload hình ảnh']);
            return;
        }
    }

    // Gọi hàm updateProduct từ ProductModel
    $result = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image);

    // Xử lý kết quả trả về từ model
    if (is_array($result)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'errors' => $result]);
    } elseif ($result === true) {
        echo json_encode(['status' => 'success', 'message' => 'Product updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Không thể cập nhật sản phẩm, lỗi server']);
    }
}
    // DELETE /api/admin/products/{id} - Xóa sản phẩm theo ID
    public function destroy($id)
    {
        header('Content-Type: application/json');

        // Kiểm tra quyền admin
        if (!SessionHelper::isAdmin()) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Bạn không có quyền xóa sản phẩm']);
            return;
        }

        $result = $this->productModel->deleteProduct($id);
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Xóa sản phẩm thành công']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Không thể xóa sản phẩm']);
        }
    }
}