<?php include 'app/views/shares/header.php'; ?>

<div class="container mt-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white text-center">
            <h2 class="mb-0">Chi tiết sản phẩm</h2>
        </div>
        <div class="card-body" id="productDetails">
            <!-- Dữ liệu sản phẩm sẽ được tải động bằng JavaScript -->
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    async function loadProduct() {
        const url = new URL(window.location);
        const id = url.pathname.split('/').pop();

        try {
            const response = await fetch(`/webbanhang/api/products/${id}`, {
                method: 'GET'
            });
            const result = await response.json();

            const productDetails = document.getElementById('productDetails');
            if (result.status === 'success' && result.data) {
                const product = result.data;
                productDetails.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            ${product.image ? 
                                `<img src="/webbanhang/${product.image}" class="img-fluid rounded" alt="${product.name}">` : 
                                `<img src="/webbanhang/images/no-image.png" class="img-fluid rounded" alt="Không có ảnh">`
                            }
                        </div>
                        <div class="col-md-6">
                            <h3 class="card-title text-dark font-weight-bold">${product.name}</h3>
                            <p class="card-text">${product.description.replace(/\n/g, '<br>')}</p>
                            <p class="text-danger font-weight-bold h4">
                                💰 ${new Intl.NumberFormat('vi-VN').format(product.price)} VND
                            </p>
                            <p><strong>Danh mục:</strong>
                                <span class="badge bg-info text-white">
                                    ${product.category_name || 'Chưa có danh mục'}
                                </span>
                            </p>
                            <div class="mt-4">
                                <a href="/webbanhang/Product/addToCart/${product.id}"
                                   class="btn btn-success px-4">➕ Thêm vào giỏ hàng</a>
                                <a href="/webbanhang/Product/list" class="btn btn-secondary px-4 ml-2">Quay lại danh sách</a>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                productDetails.innerHTML = `
                    <div class="alert alert-danger text-center">
                        <h4>Không tìm thấy sản phẩm!</h4>
                    </div>
                `;
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: 'Không thể tải chi tiết sản phẩm!',
                confirmButtonText: 'OK'
            });
        }
    }

    document.addEventListener('DOMContentLoaded', loadProduct);
</script>