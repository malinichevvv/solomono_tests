<?php

require_once '../components/Database.php';
require_once 'repositories/CategoryRepository.php';

$db = new Database();
$categoryRepository = new CategoryRepository($db->getConnection());

$categories = $categoryRepository->getAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Магазин</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .category-item {
            cursor: pointer;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 5px;
            transition: background 0.2s;
        }
        .category-item:hover { background-color: #f8f9fa; }
        .category-item.active { background-color: #0d6efd; color: white; }
        .product-card { margin-bottom: 20px; }
        .buy-btn { width: 100%; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Левая колонка - категории -->
        <div class="col-md-3">
            <div class="sticky-top" style="top: 20px;">
                <h4>Категории</h4>
                <div id="categories-list">
                    <?php foreach ($categories as $category): ?>
                        <div class="category-item" data-category-id="<?= $category->id ?>"><?= $category->name ?> (<?= $category->count_products ?>)</div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Правая колонка - товары -->
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-10">
                    <h4>Товары</h4>
                </div>
                <div class="col-md-2 align-items-right">
                    <select id="sort-select" class="form-select w-auto">
                        <option value="price_asc">Сначала дешевые</option>
                        <option value="name_asc">По алфавиту</option>
                        <option value="date_desc">Сначала новые</option>
                    </select>
                </div>
            </div>
            <hr class="divider">
            <div id="products-list" class="row">
                <!-- Товары будут загружены через AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно Bootstrap -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody"></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript">
    const productsList = document.getElementById('products-list');
    const sortSelect = document.getElementById('sort-select');
    const categoriesList = document.getElementById('categories-list');
    const modal = new bootstrap.Modal(document.getElementById('productModal'));
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');

    let currentCategory = new URLSearchParams(window.location.search).get('category_id') || '';
    let currentSort = new URLSearchParams(window.location.search).get('sort') || 'price_asc';

    function updateURL(category_id, sort) {
        const params = new URLSearchParams();
        if (category_id) params.set('category_id', category_id);
        if (sort) params.set('sort', sort);
        history.pushState(null, '', '?' + params.toString());
    }

    function fetchProducts(category_id = '', sort = 'price_asc') {
        fetch(`ajax.php?action=get_products&category_id=${category_id}&sort=${sort}`)
            .then(response => response.json())
            .then(products => {
                productsList.innerHTML = '';
                if (!products.length) {
                    productsList.innerHTML = `<div class="col-12 text-center text-muted">Нет товаров</div>`;
                    return;
                }

                products.forEach(p => {
                    const div = document.createElement('div');
                    div.className = 'col-md-4';
                    div.innerHTML = `
                    <div class="card product-card shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">${p.name}</h5>
                            <p class="card-text text-muted">Дата: ${p.created_at}</p>
                            <p class="fw-bold mb-3">Цена: ${p.price} грн</p>
                            <button
                                class="btn btn-primary mt-auto buy-btn"
                                data-id="${p.id}"
                                data-name="${p.name}"
                                data-price="${p.price}"
                                data-date="${p.created_at}">
                                <i class="bi bi-cart"></i> Купить
                            </button>
                        </div>
                    </div>
                `;
                    productsList.appendChild(div);
                });
            });
    }

    // При изменении категории
    categoriesList.querySelectorAll('.category-item').forEach(el => {
        el.addEventListener('click', e => {
            e.preventDefault();

            // убираем активный класс у всех
            categoriesList.querySelectorAll('.category-item').forEach(c => c.classList.remove('active'));

            // выделяем выбранную
            e.currentTarget.classList.add('active');

            currentCategory = e.currentTarget.dataset.categoryId;
            updateURL(currentCategory, currentSort);
            fetchProducts(currentCategory, currentSort);
        })
    });

    // При изменении сортировки
    sortSelect.addEventListener('change', e => {
        currentSort = e.target.value;
        updateURL(currentCategory, currentSort);
        fetchProducts(currentCategory, currentSort);
    });

    // При клике на кнопку купить
    document.addEventListener('click', e => {
        if (e.target.classList.contains('buy-btn')) {
            const name = e.target.dataset.name;
            const price = e.target.dataset.price;
            const date = e.target.dataset.date;
            modalTitle.textContent = name;
            modalBody.innerHTML = `<p>Ціна: ${price} грн</p><p>Дата: ${date}</p>`;
            modal.show();
        }
    });

    // Загружаем при загрузке страницы
    sortSelect.value = currentSort;
    fetchProducts(currentCategory, currentSort);
</script>
</body>
</html>