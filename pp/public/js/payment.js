// public/js/payment.js

document.addEventListener("DOMContentLoaded", () => {
    const cartCountSpan = document.getElementById("cart-count"); // From header (global)

    const productListContainerPayment = document.getElementById(
        "product-list-container-payment"
    );
    const cartSubtotalSpan = document.getElementById("cart-subtotal");
    const cartTotalPaymentSpan = document.getElementById("cart-total-payment");
    // const editCartButton = document.getElementById('edit-cart-button'); // Removed: No longer needed

    const bankButtons = document.querySelectorAll(".bank-button");
    const payNowButton = document.querySelector(".pay-now-button");
    const abaModal = document.getElementById("abaModal");
    const acledaModal = document.getElementById("acledaModal");
    const abaModalAmountSpan = document.getElementById("aba-modal-amount");
    const acledaModalAmountSpan = document.getElementById(
        "acleda-modal-amount"
    );
    const modalBackButtons = document.querySelectorAll(".modal-back-button");

    const transactionCompleteModal = document.getElementById(
        "transactionCompleteModal"
    );
    const closeTransactionModalButton = document.querySelector(
        ".close-transaction-modal-button"
    );
    const scanCompleteButtons = document.querySelectorAll(
        ".scan-complete-button"
    );

    // Input fields for validation and formatting
    const contactEmail = document.getElementById("contact-email");
    const deliveryCountry = document.getElementById("delivery-country");
    const deliveryFirstName = document.getElementById("delivery-first-name");
    const deliveryLastName = document.getElementById("delivery-last-name");
    const deliveryAddress = document.getElementById("delivery-address");
    const deliveryCity = document.getElementById("delivery-city");

    const cardNumberInput = document.getElementById("card-number");
    const cardExpirationInput = document.getElementById("card-expiration");
    const cardSecurityInput = document.getElementById("card-security"); // FIXED: Changed from document('card-security')
    const cardNameInput = document.getElementById("card-name");

    // Cart Modal elements (on payment page)
    const cartIconButtonPayment = document.getElementById(
        "cart-icon-button-payment"
    );
    const cartModal = document.getElementById("cartModal");
    const closeButton = document.querySelector(".modal .close-button"); // This is the 'x' button for the cart modal
    const modalCartItemsContainer = document.getElementById(
        "modal-cart-items-payment"
    );
    const modalCartTotalSpan = document.getElementById(
        "modal-cart-total-payment"
    );
    const continueShoppingButtonModal = document.getElementById(
        "continue-shopping-button-modal"
    );
    // const secureCheckoutButtonModal = document.getElementById('secure-checkout-button-modal'); // No longer needed as it's a direct link in Blade

    let shoppingCart = []; // Local representation of the cart

    // --- Cart Management Functions ---
    function loadCartAndRender() {
        // This function retrieves the cart from localStorage and calls rendering functions.
        // If the cart is empty, it initializes an empty array.
        shoppingCart = JSON.parse(localStorage.getItem("shoppingCart")) || [];
        renderCartSummaryItems(); // Renders items in the payment page summary
        updateHeaderCartCount(); // Updates the header badge
    }

    function renderCartSummaryItems() {
        // Clears the current list of items in the payment page's order summary.
        // Then, it iterates through the shoppingCart array and creates HTML elements
        // for each item, displaying its quantity, name, size (if applicable), and total price.
        // Finally, it updates the subtotal and total display.
        productListContainerPayment.innerHTML = ""; // Clear existing items

        if (shoppingCart.length === 0) {
            productListContainerPayment.innerHTML =
                '<p class="empty-cart-message">Your cart is empty.</p>';
            cartSubtotalSpan.textContent = "0.00";
            cartTotalPaymentSpan.textContent = "0.00";
            return;
        }

        let subtotal = 0;
        shoppingCart.forEach((item) => {
            const itemDiv = document.createElement("div");
            itemDiv.classList.add("product-summary-item");

            const sizeDisplay =
                item.size && item.size !== "N/A"
                    ? `<span class="item-details">Size: ${item.size}</span>`
                    : "";
            const itemTotalPrice = item.price * item.quantity;

            itemDiv.innerHTML = `
                <div class="item-info">
                    <span class="item-name">${item.quantity} x ${
                item.name
            }</span>
                    ${sizeDisplay}
                </div>
                <span class="item-price">$${itemTotalPrice.toFixed(2)}</span>
            `;
            productListContainerPayment.appendChild(itemDiv);
            subtotal += itemTotalPrice;
        });

        cartSubtotalSpan.textContent = subtotal.toFixed(2);
        cartTotalPaymentSpan.textContent = subtotal.toFixed(2); // No shipping cost for now
    }

    function updateHeaderCartCount() {
        // Calculates the total number of items in the cart and updates the badge
        // in the header navigation (next to the shopping cart icon).
        const totalItems = shoppingCart.reduce(
            (sum, item) => sum + item.quantity,
            0
        );
        if (cartCountSpan) {
            cartCountSpan.textContent = totalItems;
        }
    }

    // --- Input Formatting Functions ---
    function formatCardNumber() {
        // Formats the credit card number input as "XXXX-XXXX-XXXX-XXXX" as the user types.
        // It removes non-digit characters and inserts hyphens every four digits.
        let value = cardNumberInput.value.replace(/\D/g, ""); // Remove non-digits
        let formattedValue = "";
        for (let i = 0; i < value.length; i++) {
            if (i > 0 && i % 4 === 0) {
                formattedValue += "-";
            }
            formattedValue += value[i];
        }
        cardNumberInput.value = formattedValue.substring(0, 19); // Max 16 digits + 3 dashes
    }

    function formatExpirationDate() {
        // Formats the expiration date input as "MM/YY".
        // It inserts a slash after the month digits.
        let value = cardExpirationInput.value.replace(/\D/g, ""); // Remove non-digits
        if (value.length > 2) {
            value = value.substring(0, 2) + "/" + value.substring(2);
        }
        cardExpirationInput.value = value.substring(0, 5); // Max MM/YY
    }

    function formatSecurityCode() {
        // Limits the security code (CVV) input to a maximum of 4 digits.
        let value = cardSecurityInput.value.replace(/\D/g, ""); // Remove non-digits
        cardSecurityInput.value = value.substring(0, 4); // Max 4 digits (for Amex), 3 for others
    }

    // --- Validation Functions ---
    function validateDeliveryFields() {
        // Checks if all required delivery information fields are filled out.
        // Adds an 'invalid' class to empty/incorrect fields.
        let isValid = true;
        const requiredFields = [
            //contactEmail,
            deliveryCountry,
            deliveryFirstName,
            deliveryLastName,
            deliveryAddress,
            deliveryCity,
        ];

        requiredFields.forEach((field) => {
            if (
                field.value.trim() === "" ||
                (field.type === "email" && !field.value.includes("@"))
            ) {
                field.classList.add("invalid");
                isValid = false;
            } else {
                field.classList.remove("invalid");
            }
        });
        return isValid;
    }

    function validatePaymentFields() {
        // Validates all required payment information fields, including specific format checks
        // for card number, expiration date, and security code.
        let isValid = true;
        const requiredFields = [
            cardNumberInput,
            cardExpirationInput,
            cardSecurityInput,
            cardNameInput,
        ];

        requiredFields.forEach((field) => {
            if (field.value.trim() === "") {
                field.classList.add("invalid");
                isValid = false;
            } else {
                field.classList.remove("invalid");
            }
        });

        // Card number: must be 16 digits (allowing for dashes in input)
        if (!/^\d{4}-\d{4}-\d{4}-\d{4}$/.test(cardNumberInput.value)) {
            cardNumberInput.classList.add("invalid");
            isValid = false;
        }

        // Expiration date: MM/YY
        if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(cardExpirationInput.value)) {
            cardExpirationInput.classList.add("invalid");
            isValid = false;
        }

        // CVV: 3 or 4 digits
        if (!/^\d{3,4}$/.test(cardSecurityInput.value)) {
            cardSecurityInput.classList.add("invalid");
            isValid = false;
        }

        return isValid;
    }

    // --- Modal Logic ---
    function openModal(modalElement) {
        // Closes any currently active modals and then displays the specified modal.
        // Updates bank modal amounts based on the current cart total.
        document
            .querySelectorAll(".payment-modal.active, .modal.active")
            .forEach((modal) => {
                modal.classList.remove("active");
                modal.style.display = "none";
            });
        modalElement.classList.add("active");
        modalElement.style.display = "flex";

        if (
            modalElement.id === "abaModal" ||
            modalElement.id === "acledaModal"
        ) {
            const totalAmount = parseFloat(cartTotalPaymentSpan.textContent);
            modalElement.querySelector(".account-amount span").textContent =
                totalAmount.toFixed(2);
        }
        if (modalElement.id === "cartModal") {
            renderModalCartItems();
        }
    }

    function closeModal() {
        // Hides all currently active modals.
        document
            .querySelectorAll(".payment-modal.active, .modal.active")
            .forEach((modal) => {
                modal.classList.remove("active");
                modal.style.display = "none";
            });
    }

    function renderModalCartItems() {
        // Renders the detailed list of items inside the shopping cart modal.
        // Includes quantity buttons and a remove button for each item.
        let cart = JSON.parse(localStorage.getItem("shoppingCart")) || [];
        modalCartItemsContainer.innerHTML = "";

        if (cart.length === 0) {
            modalCartItemsContainer.innerHTML =
                '<p class="empty-cart-message">Your shopping bag is empty.</p>';
            modalCartTotalSpan.textContent = "0.00";
            return;
        }

        let total = 0;
        cart.forEach((item) => {
            const itemDiv = document.createElement("div");
            itemDiv.classList.add("modal-cart-item");
            itemDiv.dataset.id = item.id;
            itemDiv.dataset.size = item.size;

            const imageSrc =
                item.image ||
                "https://placehold.co/80x80/444/white?text=No+Image";

            itemDiv.innerHTML = `
                <img src="${imageSrc}" alt="${item.name}">
                <div class="modal-item-details">
                    <h4>${item.name}</h4>
                    ${
                        item.size && item.size !== "N/A"
                            ? `<p>Size: ${item.size}</p>`
                            : ""
                    }
                    <p class="modal-item-price">$${(
                        item.price * item.quantity
                    ).toFixed(2)}</p>
                </div>
                <div class="modal-item-quantity">
                    <button class="quantity-decrease" data-id="${
                        item.id
                    }" data-size="${item.size}">-</button>
                    <span>${item.quantity}</span>
                    <button class="quantity-increase" data-id="${
                        item.id
                    }" data-size="${item.size}">+</button>
                </div>
                <button class="remove-from-cart" data-id="${
                    item.id
                }" data-size="${item.size}">Remove</button>
            `;
            modalCartItemsContainer.appendChild(itemDiv);
            total += item.price * item.quantity;
        });

        modalCartTotalSpan.textContent = total.toFixed(2);

        // Re-attach event listeners for quantity and remove buttons in modal
        modalCartItemsContainer
            .querySelectorAll(".quantity-decrease")
            .forEach((button) => {
                button.addEventListener("click", (event) => {
                    const id = event.target.dataset.id;
                    const size = event.target.dataset.size;
                    updateQuantityInModal(id, size, -1);
                });
            });

        modalCartItemsContainer
            .querySelectorAll(".quantity-increase")
            .forEach((button) => {
                button.addEventListener("click", (event) => {
                    const id = event.target.dataset.id;
                    const size = event.target.dataset.size;
                    updateQuantityInModal(id, size, 1);
                });
            });

        modalCartItemsContainer
            .querySelectorAll(".remove-from-cart")
            .forEach((button) => {
                button.addEventListener("click", (event) => {
                    const id = event.target.dataset.id;
                    const size = event.target.dataset.size;
                    removeItemFromCartAndRefresh(id, size);
                });
            });
    }

    function updateQuantityInModal(id, size, change) {
        // Updates the quantity of a specific item in the cart from within the modal.
        // Removes the item if its quantity drops to 0 or below.
        // Refreshes the cart display in both the modal and the payment summary.
        let cart = JSON.parse(localStorage.getItem("shoppingCart")) || [];
        const itemIndex = cart.findIndex(
            (item) => item.id === id && item.size === size
        );

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
        // Removes a specific item from the shopping cart.
        // Refreshes the cart display in both the modal and the payment summary.
        let cart = JSON.parse(localStorage.getItem("shoppingCart")) || [];
        cart = cart.filter((item) => !(item.id === id && item.size === size));
        localStorage.setItem("shoppingCart", JSON.stringify(cart));
        updateHeaderCartCount();
        renderModalCartItems();
        renderCartSummaryItems();
    }

    function handleSuccessfulTransaction(orderId = null) {

        const receiptUploadABA = document.getElementById("receipt-upload-ABA");
        const receiptUploadACLEDA = document.getElementById("receipt-upload-ACLEDA");

        const firstName = document.getElementById('delivery-first-name')?.value.trim() || '';
        const lastName = document.getElementById('delivery-last-name')?.value.trim() || '';
        const fullName = `${firstName} ${lastName}`.trim();

        const address = document.getElementById('delivery-address')?.value.trim() || '';
        const apartment = document.getElementById('delivery-apartment')?.value.trim() || '';
        const city = document.getElementById('delivery-city')?.value.trim() || '';
        const fullAddress = `${address}${apartment ? ', ' + apartment : ''}, ${city}`;

        const email = document.getElementById('contact-email')?.value.trim() || 'no@email.com';
        const cart = JSON.parse(localStorage.getItem('shoppingCart')) || [];

        if (cart.length === 0) {
            alert("Your cart is empty.");
            return;
        }

        // =========================
        // PAYMENT MODE DETECTION
        // =========================
        const isKhqrFlow = !!orderId;

        let receiptFile = null;

        if (!isKhqrFlow) {
            // only required for manual upload flow
            const abaReceipt = receiptUploadABA?.files?.[0] || null;
            const acledaReceipt = receiptUploadACLEDA?.files?.[0] || null;
            receiptFile = abaReceipt || acledaReceipt;

            if (!receiptFile) {
                alert("Please upload your receipt image.");
                return;
            }
        }

        // =========================
        // FORM DATA
        // =========================
        const formData = new FormData();

        formData.append("delivery_name", fullName);
        formData.append("delivery_address", fullAddress);
        formData.append("delivery_email", email);
        formData.append("cart", JSON.stringify(cart));

        // KHQR mode
        if (isKhqrFlow) {
            formData.append("payment_ref", orderId);
            formData.append("payment_method", "khqr");
        } else {
            formData.append("receipt_image", receiptFile);
            formData.append("payment_method", "manual");
        }

        // =========================
        // SUBMIT
        // =========================
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

            console.error("Order submission error:", error);

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

  function handleSuccessfulTransaction1() {
    const receiptUploadABA = document.getElementById("receipt-upload-ABA");
    const receiptUploadACLEDA = document.getElementById("receipt-upload-ACLEDA");
    const firstName = document.getElementById('delivery-first-name')?.value.trim() || '';
    const lastName = document.getElementById('delivery-last-name')?.value.trim() || '';
    const fullName = `${firstName} ${lastName}`.trim();

    const address = document.getElementById('delivery-address')?.value.trim() || '';
    const apartment = document.getElementById('delivery-apartment')?.value.trim() || '';
    const city = document.getElementById('delivery-city')?.value.trim() || '';
    const fullAddress = `${address}${apartment ? ', ' + apartment : ''}, ${city}`;

    const email = document.getElementById('contact-email')?.value.trim() || 'no@email.com';
    const cart = JSON.parse(localStorage.getItem('shoppingCart')) || [];

    if (cart.length === 0) {
        alert("Your cart is empty.");
        return;
    }
    // Determine which receipt image is uploaded
    const abaReceipt = receiptUploadABA?.files?.[0] || null;
    const acledaReceipt = receiptUploadACLEDA?.files?.[0] || null;
    const receiptFile = abaReceipt || acledaReceipt;

    if (!receiptFile) {
        alert("Please upload your receipt image.");
        return;
    }

    // Create FormData for file + JSON data
    const formData = new FormData();
    formData.append("delivery_name", fullName);
    formData.append("delivery_address", fullAddress);
    formData.append("delivery_email", email);
    formData.append("receipt_image", receiptFile); // 👈 file image
    formData.append("cart", JSON.stringify(cart));  // 👈 cart array

    fetch("/submit-order", {
    method: "POST",
    headers: {
        "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
    },
    body: formData,
    })
    .then(async (res) => {
    if (!res.ok) {
        // Parse error JSON from Laravel
        const errorData = await res.json();
        // Throw error with message so it reaches catch
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
    console.error("Order submission error:", error);
    if (error.errors) {
        // ✅ Show field-specific errors
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


    // --- Event Listeners ---

    // Input formatting event listeners for credit card fields
    cardNumberInput.addEventListener("input", formatCardNumber);
    cardExpirationInput.addEventListener("input", formatExpirationDate);
    cardSecurityInput.addEventListener("input", formatSecurityCode);

    let currentMD5 = null;
    // Event listeners for bank buttons to open their respective payment modals
    bankButtons.forEach((button) => {
        button.addEventListener("click", () => {

            if (shoppingCart.length === 0) {
                alert("Cart empty");
                return;
            }

            if (!validateDeliveryFields()) {
                alert("Fill delivery info");
                return;
            }

            const bankType = button.dataset.bank;
            const total = parseFloat(cartTotalPaymentSpan.textContent) || 0;

            if (total <= 0) {
                alert("Invalid amount");
                return;
            }

        });
    });

    // Event listener for the "Pay Now" button to trigger validation and transaction
    payNowButton.addEventListener("click", () => {
        if (shoppingCart.length === 0) {
            alert(
                "Your cart is empty. Please add items to your cart before proceeding."
            );
            return;
        }

        const isDeliveryValid = validateDeliveryFields();
        const isPaymentValid = validatePaymentFields();

        //if (isDeliveryValid && isPaymentValid) {
        if (isDeliveryValid) {
            handleSuccessfulTransaction();
        } else {
            alert(
                //"Please fill in all required Delivery and Payment information correctly."
                "Please fill in all required Delivery information correctly."
            );
        }
    });

    // Event listeners for "Scan Complete" buttons in bank modals to confirm payment
    scanCompleteButtons.forEach((button) => {
        button.addEventListener("click", () => {
            handleSuccessfulTransaction();
        });
    });

    // Event listeners for "Back" buttons in payment modals to close them
    modalBackButtons.forEach((button) => {
        button.addEventListener("click", closeModal);
    });

    // Event listener for the "Continue Shopping" button on the transaction complete modal
    // This redirects the user to the home page.
    closeTransactionModalButton.addEventListener("click", () => {
        closeModal();
        window.location.href = "/"; // FIXED: Removed extra semicolon
    });

    // Event listener for the Cart Icon Button in the header on the payment page
    cartIconButtonPayment.addEventListener("click", () => {
        openModal(cartModal); // Opens the detailed shopping cart modal
    });

    // Event listener for "Continue Shopping" button inside the main cart modal
    continueShoppingButtonModal.addEventListener("click", () => {
        closeModal(); // Simply closes the cart modal
    });

    // Removed the secureCheckoutButtonModal listener as it's now a direct link in blade
    // secureCheckoutButtonModal.addEventListener('click', () => {
    //     closeModal();
    //     const checkoutFormSection = document.querySelector('.checkout-form');
    //     if (checkoutFormSection) {
    //         checkoutFormSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    //     }
    // });

    // Removed the editCartButton listener
    // editCartButton.addEventListener('click', () => {
    //     openModal(cartModal);
    // });

    // Event listener to close modals if the user clicks outside their content area
    window.addEventListener("click", (event) => {
        if (
            event.target == abaModal ||
            event.target == acledaModal ||
            event.target == transactionCompleteModal ||
            event.target == cartModal
        ) {
            closeModal();
        }
    });
    function startCheckPayment() {

        if (!window.currentMd5) return;

        const interval = setInterval(() => {

            fetch(`/check-payment?md5=${window.currentMd5}`)
            .then(res => res.json())
            .then(data => {

                console.log("CHECK:", data);

                if (data.status === "paid") {

                    clearInterval(interval);

                    status.innerText = "✅ Payment Success";

                    // 🔥 CALL ORDER API HERE
                    handleSuccessfulTransaction(data.payment_ref);
                }

            });

        }, 3000); // check every 3 sec
    }

    // Initial load for cart items in the summary when the page loads
    loadCartAndRender();
});
