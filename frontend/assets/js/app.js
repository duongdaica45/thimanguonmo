const API_BASE = 'https://myproject-ww37.onrender.com/api/';

function getSessionId() {
    let sid = localStorage.getItem('session_id');
    if (!sid) {
        sid = 'sess_' + Math.random().toString(36).substring(2);
        localStorage.setItem('session_id', sid);
    }
    return sid;
}

document.addEventListener('DOMContentLoaded', function() {
    loadProducts();

    if (document.getElementById('loginForm')) {
        document.getElementById('loginForm').addEventListener('submit', handleLogin);
    }

    if (document.getElementById('cartItems')) {
        loadCart();
    }
});

async function loadProducts() {
    try {
        const response = await fetch(API_BASE + 'products.php');
        const products = await response.json();
        displayProducts(products);
    } catch (error) {
        console.error('Error loading products:', error);
    }
}

function displayProducts(products) {
    const container = document.getElementById('products') || document.getElementById('product-list');
    if (!container) return;
    container.innerHTML = products.map(product => `
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">${product.name}</h5>
                    <p class="card-text">Giá: ${product.price.toLocaleString()} VND</p>
                    <button class="btn btn-success" onclick="addToCart(${product.id})">Thêm vào giỏ</button>
                </div>
            </div>
        </div>
    `).join('');
}

async function addToCart(id) {
    const sessionId = getSessionId();

    try {
        const response = await fetch(API_BASE + 'add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Session-Id': sessionId
            },
            body: JSON.stringify({ id, quantity: 1 })
        });

        const result = await response.json();
        if (result.success) {
            alert('Đã thêm vào giỏ!');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
    }
}

async function loadCart() {
    const sessionId = getSessionId();
    try {
        const response = await fetch(API_BASE + 'get_cart.php', {
            headers: {
                'X-Session-Id': sessionId
            }
        });
        const result = await response.json();
        if (result.success) {
            displayCart(result.items);
        }
    } catch (error) {
        console.error('Error loading cart:', error);
    }
}

function displayCart(items) {
    const cartContainer = document.getElementById('cartItems');
    if (!cartContainer) return;

    if (items.length === 0) {
        cartContainer.innerHTML = '<p>Giỏ hàng trống</p>';
        return;
    }

    cartContainer.innerHTML = items.map(item => `
        <div class="card mb-2 p-2">
            <h5>${item.name}</h5>
            <p>Giá: ${item.price.toLocaleString()} VND</p>
            <p>Số lượng: ${item.quantity}</p>
        </div>
    `).join('');
}

async function handleLogin(event) {
    event.preventDefault();
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    try {
        const response = await fetch(API_BASE + 'login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
        });
        const result = await response.json();
        const messageDiv = document.getElementById('message');
        if (result.success) {
            messageDiv.innerHTML = '<div class="alert alert-success">Đăng nhập thành công!</div>';
            window.location.href = '/index.html'; // chuyển trang sau login
        } else {
            messageDiv.innerHTML = '<div class="alert alert-danger">' + (result.message || 'Đăng nhập thất bại') + '</div>';
        }
    } catch (error) {
        console.error('Error logging in:', error);
    }
}
