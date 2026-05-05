// Frontend Main JavaScript

document.addEventListener('DOMContentLoaded', () => {
    // Quantity Selector Logic
    const qtyInputs = document.querySelectorAll('.qty-input');
    const minusBtns = document.querySelectorAll('.qty-btn.minus');
    const plusBtns = document.querySelectorAll('.qty-btn.plus');

    if (qtyInputs.length > 0) {
        minusBtns.forEach((btn, index) => {
            btn.addEventListener('click', () => {
                let input = qtyInputs[index];
                let val = parseInt(input.value);
                if (val > 1) {
                    input.value = val - 1;
                    updateCartQuantity(input);
                }
            });
        });

        plusBtns.forEach((btn, index) => {
            btn.addEventListener('click', () => {
                let input = qtyInputs[index];
                let val = parseInt(input.value);
                if (val < 99) {
                    input.value = val + 1;
                    updateCartQuantity(input);
                }
            });
        });

        qtyInputs.forEach(input => {
            input.addEventListener('change', () => {
                let val = parseInt(input.value);
                if (isNaN(val) || val < 1) {
                    input.value = 1;
                } else if (val > 99) {
                    input.value = 99;
                }
                updateCartQuantity(input);
            });
        });
    }

    // Update cart via AJAX when quantity changes on cart page
    function updateCartQuantity(inputElement) {
        // Only fire AJAX if we are on the cart page (checking for data attributes)
        if (inputElement.hasAttribute('data-product-id')) {
            const productId = inputElement.getAttribute('data-product-id');
            const qty = inputElement.value;
            
            fetch('cart-action.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update&product_id=${productId}&quantity=${qty}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update header cart count
                    document.getElementById('header-cart-count').innerText = data.total_items;
                    // Reload page to reflect new totals (for simplicity)
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }

    // Add to cart from Product Card or Details Page
    const addToCartForms = document.querySelectorAll('.add-to-cart-form');
    addToCartForms.forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const formData = new FormData(form);
            formData.append('action', 'add');

            fetch('cart-action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('header-cart-count').innerText = data.total_items;
                    
                    // Show a simple toast or alert (could be improved with a custom toast UI)
                    const btn = form.querySelector('button');
                    const originalText = btn.innerHTML;
                    
                    if (btn.classList.contains('add-to-cart-btn')) {
                        // Small button on cards
                        btn.innerHTML = '<i class="fa-solid fa-check"></i>';
                        btn.style.backgroundColor = 'var(--success-color)';
                        setTimeout(() => {
                            btn.innerHTML = originalText;
                            btn.style.backgroundColor = '';
                        }, 2000);
                    } else {
                        // Large button on details page
                        btn.innerHTML = '<i class="fa-solid fa-check"></i> Added to Cart';
                        btn.classList.replace('btn-primary', 'btn-success');
                        btn.style.backgroundColor = 'var(--success-color)';
                        setTimeout(() => {
                            btn.innerHTML = originalText;
                            btn.style.backgroundColor = '';
                        }, 2000);
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

    // Remove from cart
    const removeBtns = document.querySelectorAll('.remove-btn');
    removeBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const productId = btn.getAttribute('data-product-id');
            
            if (confirm('Remove this item from cart?')) {
                fetch('cart-action.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=remove&product_id=${productId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    });
});
