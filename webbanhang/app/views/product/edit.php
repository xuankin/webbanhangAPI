<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa sản phẩm</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome cho icon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        header { background-color: #ffcc00; }
        .form-card { transition: all 0.3s ease; }
        .btn-custom { transition: all 0.3s ease; }
        .btn-custom:hover { transform: scale(1.05); }
    </style>
</head>
<body class="bg-gray-100 font-['Roboto']">
    <?php include 'app/views/shares/header.php'; ?>

    <div class="container py-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6" data-aos="fade-down">Sửa sản phẩm</h1>

        <div class="card form-card bg-white rounded-lg shadow-md p-6" data-aos="fade-up">
            <form id="editProductForm" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $product->id; ?>">

                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-semibold">Tên sản phẩm:</label>
                    <input type="text" id="name" name="name" 
                           class="form-control mt-1 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                           value="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-gray-700 font-semibold">Mô tả:</label>
                    <textarea id="description" name="description" 
                              class="form-control mt-1 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                              required><?php echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>

                <div class="mb-4">
                    <label for="price" class="block text-gray-700 font-semibold">Giá:</label>
                    <input type="number" id="price" name="price" step="0.01" 
                           class="form-control mt-1 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                           value="<?php echo htmlspecialchars($product->price, ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>

                <div class="mb-4">
                    <label for="category_id" class="block text-gray-700 font-semibold">Danh mục:</label>
                    <select id="category_id" name="category_id" 
                            class="form-control mt-1 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category->id; ?>" 
                                    <?php echo $category->id == $product->category_id ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="image" class="block text-gray-700 font-semibold">Hình ảnh:</label>
                    <input type="file" id="image" name="image" 
                           class="form-control mt-1 rounded-md shadow-sm">
                    <input type="hidden" name="existing_image" value="<?php echo $product->image; ?>">
                    <?php if ($product->image): ?>
                        <img src="/webbanhang/<?php echo $product->image; ?>" 
                             alt="Product Image" class="mt-2 rounded-md shadow-sm" style="max-width: 100px;">
                    <?php endif; ?>
                </div>

                <button type="submit" 
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow-md hover:bg-indigo-700 btn-custom">
                    <i class="fas fa-save mr-2"></i>Lưu thay đổi
                </button>
                <a href="/webbanhang/admin" 
                   class="bg-gray-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-gray-600 btn-custom ml-2">
                    <i class="fas fa-arrow-left mr-2"></i>Quay lại
                </a>
            </form>
        </div>
    </div>

    <?php include 'app/views/shares/footer.php'; ?>

    <script>
    AOS.init();

    document.getElementById('editProductForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const id = formData.get('id');

        // Kiểm tra giá trị của ID
        if (!id) {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: 'ID sản phẩm không hợp lệ.',
                confirmButtonText: 'OK'
            });
            return;
        }

        try {
            const response = await fetch(`/webbanhang/api/product/${id}`, {
                method: 'PUT',
                body: formData
            });

            const result = await response.json();

            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: result.message,
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = '/webbanhang/admin';
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