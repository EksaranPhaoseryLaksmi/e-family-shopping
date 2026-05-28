// public/js/payment.js

document.addEventListener("DOMContentLoaded", () => {
    const cartCountSpan = document.getElementById("cart-count"); // From header (global)

    const productListContainerPayment = document.getElementById("product-list-container-payment");
    const cartSubtotalSpan = document.getElementById("cart-subtotal");
    const cartTotalPaymentSpan = document.getElementById("cart-total-payment");

    const khqrBtn = document.getElementById("khqr-btn");
    const payNowButton = document.querySelector(".pay-now-button");
    const khqrModal = document.getElementById("khqrModal");

    const transactionCompleteModal = document.getElementById("transactionCompleteModal");
    const closeTransactionModalButton = document.querySelector(".close-transaction-modal-button");

    // Input fields for validation and formatting
    const contactEmail = document.getElementById("contact-email");
    const deliveryCountry = document.getElementById("delivery-country");
    const deliveryFirstName = document.getElementById("delivery-first-name");
    const deliveryLastName = document.getElementById("delivery-last-name");
    const deliveryAddress = document.getElementById("delivery-address");
    const deliveryCity = document.getElementById("delivery-city");
    const deliveryPhone = document.getElementById("delivery-phone");

    // Cart Modal elements (on payment page)
    const cartIconButtonPayment = document.getElementById("cart-icon-button-payment");
    const cartModal = document.getElementById("cartModal");
    const modalCartItemsContainer = document.getElementById("modal-cart-items-payment");
    const modalCartTotalSpan = document.getElementById("modal-cart-total-payment");
    const continueShoppingButtonModal = document.getElementById("continue-shopping-button-modal");

    // Inject styles dynamically to handle active feedback for validation inputs smoothly
    if (!document.getElementById("validation-styles")) {
        const style = document.createElement("style");
        style.id = "validation-styles";
        style.innerHTML = `
            .invalid { border: 2px solid #d9534f !important; background-color: #fff8f8 !important; }
            .validation-error-msg { color: #d9534f; font-size: 13px; margin-top: 4px; font-weight: bold; display: block; }
        `;
        document.head.appendChild(style);
    }

    let shoppingCart = []; // Local representation of the cart

    // --- Cart Management Functions ---
    function loadCartAndRender() {
        shoppingCart = JSON.parse(localStorage.getItem("shoppingCart")) || [];
        renderCartSummaryItems(); // Renders items in the payment page summary
        updateHeaderCartCount(); // Updates the header badge
    }

    function renderCartSummaryItems() {
        productListContainerPayment.innerHTML = ""; // Clear existing items

        if (shoppingCart.length === 0) {
            productListContainerPayment.innerHTML = '<p class="empty-cart-message">Your cart is empty.</p>';
            cartSubtotalSpan.textContent = "0.00";
            cartTotalPaymentSpan.textContent = "0.00";
            return;
        }

        let subtotal = 0;
        shoppingCart.forEach((item) => {
            const itemDiv = document.createElement("div");
            itemDiv.classList.add("product-summary-item");

            const sizeDisplay = item.size && item.size !== "N/A" ? `<span class="item-details">Size: ${item.size}</span>` : "";
            const itemTotalPrice = item.price * item.quantity;

            itemDiv.innerHTML = `
                <div class="item-info">
                    <span class="item-name">${item.quantity} x ${item.name}</span>
                    ${sizeDisplay}
                </div>
                <span class="item-price">$${itemTotalPrice.toFixed(2)}</span>
            `;
            productListContainerPayment.appendChild(itemDiv);
            subtotal += itemTotalPrice;
        });

        cartSubtotalSpan.textContent = subtotal.toFixed(2);
        cartTotalPaymentSpan.textContent = subtotal.toFixed(2);
    }

    function updateHeaderCartCount() {
        const totalItems = shoppingCart.reduce((sum, item) => sum + item.quantity, 0);
        if (cartCountSpan) {
            cartCountSpan.textContent = totalItems;
        }
    }

    // --- Dynamic Intercepting & Validation Fields ---
    function validateDeliveryFields() {
        let isValid = true;

        // Remove existing custom error text messages before checking
        document.querySelectorAll(".validation-error-msg").forEach(el => el.remove());

        const requiredFields = [
            { element: deliveryCountry, name: "Country" },
            { element: deliveryFirstName, name: "First Name" },
            { element: deliveryLastName, name: "Last Name" },
            { element: deliveryAddress, name: "Address" },
            { element: deliveryCity, name: "City" },
            { element: deliveryPhone, name: "Phone Number" }
        ];

        requiredFields.forEach((field) => {
            if (!field.element || field.element.value.trim() === "") {
                field.element.classList.add("invalid");
                isValid = false;

                // Append helpful alert text node below fields
                const errorMsg = document.createElement("span");
                errorMsg.className = "validation-error-msg";
                errorMsg.innerText = `Please enter your ${field.name}`;
                field.element.parentNode.insertBefore(errorMsg, field.element.nextSibling);
            } else {
                field.element.classList.remove("invalid");
            }
        });

        return isValid;
    }

    // Remove invalid red borders dynamically when user corrects typing values
    document.querySelectorAll(".delivery-section input, .delivery-section select").forEach(input => {
        input.addEventListener("input", function() {
            if (this.value.trim() !== "") {
                this.classList.remove("invalid");
                if (this.nextSibling && this.nextSibling.className === "validation-error-msg") {
                    this.nextSibling.remove();
                }
            }
        });
    });

    // --- Modal Logic ---
    function openModal(modalElement) {
        document.querySelectorAll(".payment-modal.active, .modal.active").forEach((modal) => {
            modal.classList.remove("active");
            modal.style.display = "none";
        });
        modalElement.classList.add("active");
        modalElement.style.display = "flex";

        if (modalElement.id === "cartModal") {
            renderModalCartItems();
        }
    }

    function closeModal() {
        document.querySelectorAll(".payment-modal.active, .modal.active").forEach((modal) => {
            modal.classList.remove("active");
            modal.style.display = "none";
        });
    }

    function renderModalCartItems() {
        let cart = JSON.parse(localStorage.getItem("shoppingCart")) || [];
        modalCartItemsContainer.innerHTML = "";

        if (cart.length === 0) {
            modalCartItemsContainer.innerHTML = '<p class="empty-cart-message">Your shopping bag is empty.</p>';
            modalCartTotalSpan.textContent = "0.00";
            return;
        }

        let total = 0;
        cart.forEach((item) => {
            const itemDiv = document.createElement("div");
            itemDiv.classList.add("modal-cart-item");
            itemDiv.dataset.id = item.id;
            itemDiv.dataset.size = item.size;

            const imageSrc = item.image || "https://placehold.co/80x80/444/white?text=No+Image";

            itemDiv.innerHTML = `
                <img src="${imageSrc}" alt="${item.name}">
                <div class="modal-item-details">
                    <h4>${item.name}</h4>
                    ${item.size && item.size !== "N/A" ? `<p>Size: ${item.size}</p>` : ""}
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

        // Re-attach listeners for item management
        modalCartItemsContainer.querySelectorAll(".quantity-decrease").forEach((button) => {
            button.addEventListener("click", (event) => {
                updateQuantityInModal(event.target.dataset.id, event.target.dataset.size, -1);
            });
        });

        modalCartItemsContainer.querySelectorAll(".quantity-increase").forEach((button) => {
            button.addEventListener("click", (event) => {
                updateQuantityInModal(event.target.dataset.id, event.target.dataset.size, 1);
            });
        });

        modalCartItemsContainer.querySelectorAll(".remove-from-cart").forEach((button) => {
            button.addEventListener("click", (event) => {
                removeItemFromCartAndRefresh(event.target.dataset.id, event.target.dataset.size);
            });
        });
    }

    function updateQuantityInModal(id, size, change) {
        let cart = JSON.parse(localStorage.getItem("shoppingCart")) || [];
        const itemIndex = cart.findIndex((item) => item.id === id && item.size === size);

        if (itemIndex > -1) {
            cart[itemIndex].quantity += change;
            if (cart[itemIndex].quantity <= 0) {
                cart.splice(itemIndex, 1);
            }
            localStorage.setItem("shoppingCart", JSON.stringify(cart));
            updateHeaderCartCount();
            renderModalCartItems();
            renderCartSummaryItems();
        }
    }

    function removeItemFromCartAndRefresh(id, size) {
        let cart = JSON.parse(localStorage.getItem("shoppingCart")) || [];
        cart = cart.filter((item) => !(item.id === id && item.size === size));
        localStorage.setItem("shoppingCart", JSON.stringify(cart));
        updateHeaderCartCount();
        renderModalCartItems();
        renderCartSummaryItems();
    }

    // --- Order Placement Handling Functions ---
    function handleSuccessfulTransaction(orderId = null) {
        const firstName = document.getElementById('delivery-first-name')?.value.trim() || '';
        const lastName = document.getElementById('delivery-last-name')?.value.trim() || '';
        const fullName = `${firstName} ${lastName}`.trim();

        const address = document.getElementById('delivery-address')?.value.trim() || '';
        const apartment = document.getElementById('delivery-apartment')?.value.trim() || '';
        const city = document.getElementById('delivery-city')?.value.trim() || '';
        const fullAddress = `${address}${apartment ? ', ' + apartment : ''}, ${city}`;

        const email = document.getElementById('contact-email')?.value.trim() || 'no@email.com';
        const phone = document.getElementById('delivery-phone')?.value.trim() || '';
        const mapLink = document.getElementById('delivery-map')?.value.trim() || '';
        const cart = JSON.parse(localStorage.getItem('shoppingCart')) || [];

        if (cart.length === 0) {
            alert("Your cart is empty.");
            return;
        }

        const formData = new FormData();
        formData.append("delivery_name", fullName);
        formData.append("delivery_address", fullAddress);
        formData.append("delivery_email", email);
        formData.append("delivery_phone", phone);
        formData.append("delivery_map", mapLink);
        formData.append("cart", JSON.stringify(cart));

        if (orderId) {
            formData.append("payment_ref", orderId);
            formData.append("payment_method", "khqr");
        } else {
            formData.append("payment_method", "manual");
        }

        fetch("/submit-order", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
            },
            body: formData,
        })
        .then(async (res) => {
            if (!res.ok) {
                const errorData = await res.json();
                throw errorData;
            }
            return res.json();
        })
        .then((data) => {
            localStorage.removeItem("shoppingCart");
            shoppingCart = [];

            renderCartSummaryItems();
            updateHeaderCartCount();
            closeModal();

            openModal(transactionCompleteModal);
        })
        .catch((error) => {
            console.error("Order submission failure status:", error);
            if (error.errors) {
                const errorMessages = Object.entries(error.errors)
                    .map(([field, messages]) => `${field}: ${messages.join(", ")}`)
                    .join("\n");
                alert("Validation failed:\n" + errorMessages);
            } else if (error.message) {
                alert("Order submission failed: " + error.message);
            } else {
                alert("Order submission failed. Please try again.");
            }
        });
    }

    // --- Intercepting & Validation Execution ---
    if (khqrBtn) {
        khqrBtn.addEventListener("click", function (event) {
            // Cart length conditional barrier
            if (shoppingCart.length === 0) {
                alert("Your cart is empty. Please add items before checking out.");
                event.stopImmediatePropagation(); // Prevents Blade Inline Generation Request Execution
                return;
            }

            // Input field checking barrier
            if (!validateDeliveryFields()) {
                alert("Please fill out all required Delivery Details before scanning.");
                event.stopImmediatePropagation(); // Prevents Blade Inline Generation Request Execution
                return;
            }

            const total = parseFloat(cartTotalPaymentSpan.textContent) || 0;
            if (total <= 0) {
                alert("Invalid checkout transaction total amount.");
                event.stopImmediatePropagation();
                return;
            }
        }, true); // Capture step validation sequence first
    }

    // Event listener for standard manual Pay Now configurations if needed
    if (payNowButton) {
        payNowButton.addEventListener("click", () => {
            if (shoppingCart.length === 0) {
                alert("Your cart is empty.");
                return;
            }

            if (validateDeliveryFields()) {
                handleSuccessfulTransaction();
            } else {
                alert("Please fill in all required delivery information fields.");
            }
        });
    }

    // --- Global Interface Listeners ---
    cartIconButtonPayment.addEventListener("click", () => {
        openModal(cartModal);
    });

    continueShoppingButtonModal.addEventListener("click", () => {
        closeModal();
    });

    window.addEventListener("click", (event) => {
        if (event.target == khqrModal || event.target == transactionCompleteModal || event.target == cartModal) {
            closeModal();
        }
    });

    // Handle incoming automatic callback from checkPayment polling inside Blade script
    window.handleSuccessfulTransaction = handleSuccessfulTransaction;

    // Initial processing layout run
    loadCartAndRender();
});
