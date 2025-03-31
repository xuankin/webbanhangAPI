<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách danh mục</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container py-6">
        <h1 class="text-3xl font-bold mb-4">Danh sách danh mục</h1>
        <a href="/webbanhang/Category/add" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Thêm danh mục</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Mô tả</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody id="categoryList">
                <!-- Dữ liệu danh mục sẽ được tải động bằng JavaScript -->
            </tbody>
        </table>
    </div>

    <script>
        async function loadCategories() {
            try {
                const response = await fetch('/webbanhang/api/admin/categories', {
                    method: 'GET'
                });
                const result = await response.json();

                const categoryList = document.getElementById('categoryList');
                if (result.status === 'success' && result.data.length > 0) {
                    let html = '';
                    result.data.forEach(category => {
                        html += `
                            <tr>
                                <td>${category.id}</td>
                                <td>${category.name}</td>
                                <td>${category.description || ''}</td>
                                <td>
                                    <a href="/webbanhang/Category/edit/${category.id}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Sửa</a>
                                    <button class="btn btn-danger btn-sm delete-category" data-id="${category.id}"><i class="fas fa-trash"></i> Xóa</button>
                                </td>
                            </tr>
                        `;
                    });
                    categoryList.innerHTML = html;

                    // Gắn sự kiện xóa cho các nút "Xóa"
                    document.querySelectorAll('.delete-category').forEach(button => {
                        button.addEventListener('click', async function() {
                            const categoryId = this.getAttribute('data-id');
                            Swal.fire({
                                title: 'Bạn có chắc chắn?',
                                text: 'Bạn muốn xóa danh mục này?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Xóa',
                                cancelButtonText: 'Hủy'
                            }).then(async (result) => {
                                if (result.isConfirmed) {
                                    try {
                                        const response = await fetch(`/webbanhang/api/admin/categories/${categoryId}`, {
                                            method: 'DELETE'
                                        });
                                        const data = await response.json();

                                        if (response.ok) {
                                            Swal.fire('Đã xóa!', 'Danh mục đã được xóa.', 'success').then(() => {
                                                loadCategories(); // Tải lại danh sách
                                            });
                                        } else {
                                            Swal.fire('Lỗi!', data.message || 'Không thể xóa danh mục.', 'error');
                                        }
                                    } catch (error) {
                                        Swal.fire('Lỗi!', 'Không thể kết nối đến server!', 'error');
                                    }
                                }
                            });
                        });
                    });
                } else {
                    categoryList.innerHTML = `
                        <tr><td colspan="4" class="text-center">Không có danh mục nào.</td></tr>
                    `;
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: 'Không thể tải danh sách danh mục!',
                    confirmButtonText: 'OK'
                });
            }
        }

        // Tải danh mục khi trang được tải
        document.addEventListener('DOMContentLoaded', loadCategories);
    </script>
</body>
</html>