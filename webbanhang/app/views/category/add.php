<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm danh mục</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container py-6">
        <h1 class="text-3xl font-bold mb-4">Thêm danh mục</h1>

        <form id="addCategoryForm">
            <div class="mb-3">
                <label for="name" class="form-label">Tên danh mục:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Mô tả:</label>
                <textarea class="form-control" id="description" name="description"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Thêm danh mục</button>
            <a href="/webbanhang/Category" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>

    <script>
        document.getElementById('addCategoryForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            try {
                const response = await fetch('/webbanhang/api/admin/categories', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công!',
                        text: result.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = '/webbanhang/Category';
                    });
                } else {
                    let errorMessage = 'Có lỗi xảy ra!';
                    if (result.errors) {
                        errorMessage = Object.values(result.errors).join('<br>');
                    } else if (result.message) {
                        errorMessage = result.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        html: errorMessage,
                        confirmButtonText: 'OK'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: 'Không thể kết nối đến server!',
                    confirmButtonText: 'OK'
                });
            }
        });
    </script>
</body>
</html>