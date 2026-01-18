// public/js/script.js

document.addEventListener('DOMContentLoaded', () => {
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    const cartCountSpan = document.getElementById('cart-count'); // This is now the badge
    const categoryNavbarLinks = document.querySelectorAll('.category-navbar ul li a');

    // New elements for the Cart Modal
    const cartIconButton = document.getElementById('cart-icon-button');
    const cartModal = document.getElementById('cartModal');
    const closeButton = document.querySelector('.modal .close-button');
    const modalCartItemsContainer = document.getElementById('modal-cart-items');
    const modalCartTotalSpan = document.getElementById('modal-cart-total');
    const continueShoppingButton = document.getElementById('continue-shopping-button');
    const secureCheckoutButton = document.getElementById('secure-checkout-button');

    // Function to initialize cart from localStorage
    function initializeCart() {
        const cart = JSON.parse(localStorage.getItem('shoppingCart')) || [];
        updateCartCount(cart);
        // No need to render modal cart items here, only when modal is opened
    }

    // Function to update cart count in the header badge
    function updateCartCount(cart) {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        cartCountSpan.textContent = totalItems;
    }

    // Function to add item to cart
    function addItemToCart(product) {
        let cart = JSON.parse(localStorage.getItem('shoppingCart')) || [];

        const existingItemIndex = cart.findIndex(item => item.id === product.id && item.size === product.size);

        if (existingItemIndex > -1) {
            cart[existingItemIndex].quantity += product.quantity;
        } else {
            cart.push(product);
        }

        localStorage.setItem('shoppingCart', JSON.stringify(cart));
        updateCartCount(cart);
                // Flash the cart icon visually
        cartIconButton.classList.add('cart-flash');
        setTimeout(() => {
            cartIconButton.classList.remove('cart-flash');
        }, 600); // Match animation duration

        // console.log(`Added ${product.quantity} x ${product.name} (Size: ${product.size || 'N/A'}) to cart.`);
        // No alert message here anymore
    }

    // Function to render cart items inside the modal
    function renderModalCartItems() {
        let cart = JSON.parse(localStorage.getItem('shoppingCart')) || [];
        modalCartItemsContainer.innerHTML = ''; // Clear previous items

        if (cart.length === 0) {
            modalCartItemsContainer.innerHTML = '<p class="empty-cart-message">Your shopping bag is empty.</p>';
            modalCartTotalSpan.textContent = '0.00';
            return;
        }

        let total = 0;
        cart.forEach(item => {
            const itemDiv = document.createElement('div');
            itemDiv.classList.add('modal-cart-item');
            itemDiv.dataset.id = item.id;
            itemDiv.dataset.size = item.size; // Store size for removal/quantity update

            // Use item.image directly as it's already an asset path
            const imageSrc = item.image || 'https://placehold.co/80x80/444/white?text=No+Image'; // Fallback

            itemDiv.innerHTML = `
                <img src="${imageSrc}" alt="${item.name}">
                <div class="modal-item-details">
                    <h4>${item.name}</h4>
                    ${item.size && item.size !== 'N/A' ? `<p>Size: ${item.size}</p>` : ''}
                    <p class="modal-item-price">$${(item.price * item.quantity).toFixed(2)}</p>
                </div>
                <div class="modal-item-quantity">
                    <button class="quantity-decrease" data-id="${item.id}" data-size="${item.size}">-</button>
                    <span>${item.quantity}</span>
                    <button class="quantity-increase" data-id="${item.id}" data-size="${item.size}">+</button>
                </div>
                <button class="remove-from-cart" data-id="${item.id}" data-size="${item.size}">Remove</button>
            `;
            modalCartItemsContainer.appendChild(itemDiv);
            total += item.price * item.quantity;
        });

        modalCartTotalSpan.textContent = total.toFixed(2);

        // Add event listeners for quantity buttons and remove button inside the modal
        modalCartItemsContainer.querySelectorAll('.quantity-decrease').forEach(button => {
            button.addEventListener('click', (event) => {
                const id = event.target.dataset.id;
                const size = event.target.dataset.size;
                updateQuantityInModal(id, size, -1);
            });
        });

        modalCartItemsContainer.querySelectorAll('.quantity-increase').forEach(button => {
            button.addEventListener('click', (event) => {
                const id = event.target.dataset.id;
                const size = event.target.dataset.size;
                updateQuantityInModal(id, size, 1);
            });
        });

        modalCartItemsContainer.querySelectorAll('.remove-from-cart').forEach(button => {
            button.addEventListener('click', (event) => {
                const id = event.target.dataset.id;
                const size = event.target.dataset.size;
                removeItemFromCart(id, size);
            });
        });
    }

    // Function to update item quantity from within the modal
    function updateQuantityInModal(id, size, change) {
        let cart = JSON.parse(localStorage.getItem('shoppingCart')) || [];
        const itemIndex = cart.findIndex(item => item.id === id && item.size === size);

        if (itemIndex > -1) {
            cart[itemIndex].quantity += change;
            if (cart[itemIndex].quantity <= 0) {
                cart.splice(itemIndex, 1); // Remove if quantity is 0 or less
            }
            localStorage.setItem('shoppingCart', JSON.stringify(cart));
            updateCartCount(cart);
            renderModalCartItems(); // Re-render modal to reflect changes
        }
    }

    // Function to remove item from cart from within the modal
    function removeItemFromCart(id, size) {
        let cart = JSON.parse(localStorage.getItem('shoppingCart')) || [];
        cart = cart.filter(item => !(item.id === id && item.size === size));
        localStorage.setItem('shoppingCart', JSON.stringify(cart));
        updateCartCount(cart);
        renderModalCartItems(); // Re-render modal to reflect changes
    }


    // --- Event Listeners ---

    // Event listener for "Add to Cart" buttons
addToCartButtons.forEach(button => {
    button.addEventListener('click', (event) => {
        const productItem = event.target.closest('.product-item');
        if (!productItem) return;

        const productId = productItem.dataset.id;
        const productName = productItem.dataset.name;
        const productImage = productItem.dataset.image;
        const vendorid = productItem.dataset.vendor;

        // ✅ Price from UI (updates when size changes)
        const productPrice = parseFloat(
            document.getElementById('priceDisplay').textContent.replace('$', '')
        );

        // ✅ Quantity
        const quantityInput = document.getElementById('quantityInput');
        const selectedQuantity = parseInt(quantityInput.value, 10);

        // ✅ FIXED SIZE LOGIC
        const sizeSelect = document.getElementById('sizeSelect');
        const selectedSize = sizeSelect ? sizeSelect.value : null;

        const product = {
            id: productId,
            name: productName,
            price: productPrice,
            image: productImage,
            quantity: selectedQuantity,
            size: selectedSize, // ✅ REAL SIZE
            vendor_id: vendorid,
        };

        addItemToCart(product);
    });
});

    // Event listener for the Cart Icon Button
    cartIconButton.addEventListener('click', () => {
        cartModal.style.display = 'flex'; // Show the modal
        renderModalCartItems(); // Populate modal with current cart items
    });

    // Event listener for the Close button inside the modal
    closeButton.addEventListener('click', () => {
        cartModal.style.display = 'none'; // Hide the modal
    });

    // Event listener for "Continue Shopping" button in modal
    continueShoppingButton.addEventListener('click', () => {
        cartModal.style.display = 'none'; // Hide the modal
    });

    // Event listener for "Secure Checkout" button in modal
    secureCheckoutButton.addEventListener('click', () => {
        window.location.href = "{{ url('/payment') }}"; // Redirect to the payment page
    });

    // Close the modal if user clicks outside of the modal content
    window.addEventListener('click', (event) => {
        if (event.target == cartModal) {
            cartModal.style.display = 'none';
        }
    });

    // Smooth scroll and active link for category navigation (existing logic)
    categoryNavbarLinks.forEach(link => {
        link.addEventListener('click', (event) => {
            //event.preventDefault();

            categoryNavbarLinks.forEach(l => l.classList.remove('active-category-link'));
            event.target.classList.add('active-category-link');

            const targetId = event.target.getAttribute('href');
            const targetSection = document.querySelector(targetId);

            if (targetSection) {
                const headerHeight = document.querySelector('.header').offsetHeight || 0;
                const categoryNavHeight = document.querySelector('.category-navbar').offsetHeight || 0;
                const offset = headerHeight + categoryNavHeight + 20;

                window.scrollTo({
                    top: targetSection.offsetTop - offset,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Initialize cart count on page load
    initializeCart();
});
