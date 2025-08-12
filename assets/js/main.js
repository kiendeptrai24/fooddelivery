// Main JavaScript file for Food Delivery Application

document.addEventListener('DOMContentLoaded', function () {
    // Initialize cart count
    updateCartCount();

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});

// Update cart count in navigation
function updateCartCount() {
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
        fetch('get_cart_count.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cartCountElement.textContent = data.count;
                }
            })
            .catch(error => {
                console.error('Error updating cart count:', error);
            });
    }
}

// Add to cart functionality
function addToCart(menuItemId, restaurantId) {
    const quantity = document.getElementById(`quantity-${menuItemId}`)?.value || 1;

    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `menu_item_id=${menuItemId}&restaurant_id=${restaurantId}&quantity=${quantity}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showAlert('Thêm vào giỏ hàng thành công!', 'success');

                // Update cart count
                updateCartCount();

                // Update quantity input if it exists
                const quantityInput = document.getElementById(`quantity-${menuItemId}`);
                if (quantityInput) {
                    quantityInput.value = 1;
                }
            } else {
                showAlert(data.message || 'Có lỗi xảy ra!', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Có lỗi xảy ra khi thêm vào giỏ hàng!', 'danger');
        });
}

// Remove from cart functionality
function removeFromCart(cartItemId) {
    if (confirm('Bạn có chắc chắn muốn xóa món này khỏi giỏ hàng?')) {
        fetch('remove_from_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `cart_item_id=${cartItemId}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove item from DOM
                    const cartItem = document.querySelector(`[data-cart-item-id="${cartItemId}"]`);
                    if (cartItem) {
                        cartItem.remove();
                    }

                    // Update cart count
                    updateCartCount();

                    // Update total
                    updateCartTotal();

                    showAlert('Đã xóa món khỏi giỏ hàng!', 'success');
                } else {
                    showAlert(data.message || 'Có lỗi xảy ra!', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Có lỗi xảy ra khi xóa món!', 'danger');
            });
    }
}

// Update cart item quantity
function updateCartQuantity(cartItemId, newQuantity) {
    if (newQuantity <= 0) {
        removeFromCart(cartItemId);
        return;
    }

    fetch('update_cart_quantity.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `cart_item_id=${cartItemId}&quantity=${newQuantity}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update quantity input
                const quantityInput = document.querySelector(`[data-cart-item-id="${cartItemId}"] input[type="number"]`);
                if (quantityInput) {
                    quantityInput.value = newQuantity;
                }

                // Update price
                const priceElement = document.querySelector(`[data-cart-item-id="${cartItemId}"] .item-price`);
                if (priceElement && data.item_price) {
                    priceElement.textContent = formatPrice(data.item_price * newQuantity);
                }

                // Update total
                updateCartTotal();

                // Update cart count
                updateCartCount();
            } else {
                showAlert(data.message || 'Có lỗi xảy ra!', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Có lỗi xảy ra khi cập nhật số lượng!', 'danger');
        });
}

// Update cart total
function updateCartTotal() {
    const cartItems = document.querySelectorAll('[data-cart-item-id]');
    let total = 0;

    cartItems.forEach(item => {
        const quantity = parseInt(item.querySelector('input[type="number"]')?.value || 1);
        const priceText = item.querySelector('.item-price')?.textContent || '0';
        const price = parseFloat(priceText.replace(/[^\d]/g, ''));
        total += price * quantity;
    });

    const totalElement = document.getElementById('cart-total');
    if (totalElement) {
        totalElement.textContent = formatPrice(total);
    }

    const checkoutButton = document.getElementById('checkout-btn');
    if (checkoutButton) {
        checkoutButton.disabled = total <= 0;
    }
}

// Format price to Vietnamese currency
function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(price);
}

// Show alert message
function showAlert(message, type = 'info') {
    const alertContainer = document.getElementById('alert-container') || createAlertContainer();

    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    alertContainer.appendChild(alertDiv);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Create alert container if it doesn't exist
function createAlertContainer() {
    const container = document.createElement('div');
    container.id = 'alert-container';
    container.className = 'position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}

// Search functionality
function performSearch() {
    const searchInput = document.getElementById('search-input');
    const searchTerm = searchInput?.value.trim();

    if (searchTerm) {
        window.location.href = `restaurants.php?search=${encodeURIComponent(searchTerm)}`;
    }
}

// Filter by cuisine
function filterByCuisine(cuisine) {
    const currentUrl = new URL(window.location);
    if (cuisine) {
        currentUrl.searchParams.set('cuisine', cuisine);
    } else {
        currentUrl.searchParams.delete('cuisine');
    }
    window.location.href = currentUrl.toString();
}

// Clear search and filters
function clearFilters() {
    window.location.href = 'restaurants.php';
}

// Smooth scroll to top
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Show/hide scroll to top button
window.addEventListener('scroll', function () {
    const scrollTopBtn = document.getElementById('scroll-top-btn');
    if (scrollTopBtn) {
        if (window.pageYOffset > 300) {
            scrollTopBtn.style.display = 'block';
        } else {
            scrollTopBtn.style.display = 'none';
        }
    }
});

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    return isValid;
}

// Password strength checker
function checkPasswordStrength(password) {
    let strength = 0;
    const feedback = [];

    if (password.length >= 8) strength++;
    else feedback.push('Mật khẩu phải có ít nhất 8 ký tự');

    if (/[a-z]/.test(password)) strength++;
    else feedback.push('Mật khẩu phải có chữ thường');

    if (/[A-Z]/.test(password)) strength++;
    else feedback.push('Mật khẩu phải có chữ hoa');

    if (/[0-9]/.test(password)) strength++;
    else feedback.push('Mật khẩu phải có số');

    if (/[^A-Za-z0-9]/.test(password)) strength++;
    else feedback.push('Mật khẩu phải có ký tự đặc biệt');

    return { strength, feedback };
}

// Update password strength indicator
function updatePasswordStrength(passwordInput) {
    const strengthIndicator = document.getElementById('password-strength');
    if (!strengthIndicator) return;

    const result = checkPasswordStrength(passwordInput.value);
    const strengthText = ['Rất yếu', 'Yếu', 'Trung bình', 'Mạnh', 'Rất mạnh'];
    const strengthClass = ['danger', 'warning', 'info', 'success', 'success'];

    strengthIndicator.className = `progress-bar bg-${strengthClass[result.strength - 1]}`;
    strengthIndicator.style.width = `${(result.strength / 5) * 100}%`;
    strengthIndicator.textContent = strengthText[result.strength - 1];

    const feedbackElement = document.getElementById('password-feedback');
    if (feedbackElement) {
        feedbackElement.innerHTML = result.feedback.map(msg => `<small class="text-danger d-block">${msg}</small>`).join('');
    }
}

// Initialize password strength checker
document.addEventListener('DOMContentLoaded', function () {
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(input => {
        if (input.id === 'new_password' || input.id === 'password') {
            input.addEventListener('input', function () {
                updatePasswordStrength(this);
            });
        }
    });
});

// Mobile menu toggle
function toggleMobileMenu() {
    const navbarCollapse = document.querySelector('.navbar-collapse');
    if (navbarCollapse) {
        navbarCollapse.classList.toggle('show');
    }
}

// Close mobile menu when clicking outside
document.addEventListener('click', function (event) {
    const navbar = document.querySelector('.navbar');
    const navbarCollapse = document.querySelector('.navbar-collapse');

    if (navbar && navbarCollapse && !navbar.contains(event.target)) {
        navbarCollapse.classList.remove('show');
    }
});

// Lazy loading for images
function lazyLoadImages() {
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));
}

// Initialize lazy loading
document.addEventListener('DOMContentLoaded', lazyLoadImages);
