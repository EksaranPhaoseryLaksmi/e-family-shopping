window.onload = function () {
    // Firebase Config
    const firebaseConfig = {
        apiKey: "AIzaSyCRY6_-MkKUJ6J8ybUaEqWLIR62hfwgiGk",
        authDomain: "vendorstoreapp.firebaseapp.com",
        projectId: "vendorstoreapp",
        storageBucket: "vendorstoreapp.firebasestorage.app",
        messagingSenderId: "341876648651",
        appId: "1:341876648651:web:99a00ed9c9c53acfc8c94e",
    };

    firebase.initializeApp(firebaseConfig);
    const db = firebase.firestore();

    const step1 = document.getElementById("step1");
    const step2 = document.getElementById("step2");
    const productPage = document.getElementById("productPage");
    const storeId = 1; // Change if dynamic
    const vendorStep1 = {};
    const vendorStep2 = {};

    // Handle single-option buttons
    document.querySelectorAll(".single-option button").forEach((btn) => {
        btn.addEventListener("click", () => {
            const group = btn.parentElement;
            [...group.children].forEach((b) => b.classList.remove("selected"));
            btn.classList.add("selected");
            const field = group.dataset.name;
            vendorStep1[field] = btn.innerText;
            vendorStep2[field] = btn.innerText;
        });
    });
    const paymentOptions = {
        isbank: 0,
        iscard: 0,
        iscash: 0,
    };
    document.addEventListener("DOMContentLoaded", function () {
        document
            .querySelectorAll(".multi-option[data-name='payment'] button")
            .forEach((button) => {
                button.addEventListener("click", () => {
                    const key = button.getAttribute("data-type");

                    // Toggle selection
                    if (paymentOptions[key] === 1) {
                        paymentOptions[key] = 0;
                        button.classList.remove("selected");
                    } else {
                        paymentOptions[key] = 1;
                        button.classList.add("selected");
                    }

                    // You can call the API here if needed, or do it on final submit
                    console.log("Current payment selections:", paymentOptions);
                });
            });
    });

    let isphoto = null;
    // Handle button clicks for photo option
    document
        .querySelectorAll(".single-option[data-name='photos'] button")
        .forEach((button) => {
            button.addEventListener("click", () => {
                isphoto = button.getAttribute("data-value");

                // Optional: visually mark selected
                button.parentElement
                    .querySelectorAll("button")
                    .forEach((btn) => btn.classList.remove("selected"));
                button.classList.add("selected");
            });
        });

    let isdeliver = null;
    // Handle button clicks for photo option
    document
        .querySelectorAll(".single-option[data-name='delivery'] button")
        .forEach((button) => {
            button.addEventListener("click", () => {
                isdeliver = button.getAttribute("data-value");

                // Optional: visually mark selected
                button.parentElement
                    .querySelectorAll("button")
                    .forEach((btn) => btn.classList.remove("selected"));
                button.classList.add("selected");
            });
        });

    let ishelp = null;
    // Handle button clicks for photo option
    document
        .querySelectorAll(".single-option[data-name='help'] button")
        .forEach((button) => {
            button.addEventListener("click", () => {
                ishelp = button.getAttribute("data-value");

                // Optional: visually mark selected
                button.parentElement
                    .querySelectorAll("button")
                    .forEach((btn) => btn.classList.remove("selected"));
                button.classList.add("selected");
            });
        });

    let type = null;
    // Handle button clicks for photo option
    document
        .querySelectorAll(".single-option[data-name='storeType'] button")
        .forEach((button) => {
            button.addEventListener("click", () => {
                type = button.getAttribute("data-value");

                // Optional: visually mark selected
                button.parentElement
                    .querySelectorAll("button")
                    .forEach((btn) => btn.classList.remove("selected"));
                button.classList.add("selected");
            });
        });

    let locationOptions = {
        islocal: 0,
        isinternational: 0,
        isboth: 0,
    };

    document.addEventListener("DOMContentLoaded", function () {
        document
            .querySelectorAll(".multi-option[data-name='location'] button")
            .forEach((button) => {
                button.addEventListener("click", () => {
                    const key = button.getAttribute("data-key");

                    // Reset all
                    locationOptions = {
                        islocal: 0,
                        isinternational: 0,
                        isboth: 0,
                    };

                    // Set selected
                    locationOptions[key] = 1;

                    // Visual feedback
                    button.parentElement
                        .querySelectorAll("button")
                        .forEach((btn) => btn.classList.remove("selected"));
                    button.classList.add("selected");

                    console.log("Location selection:", locationOptions);
                });
            });
    });

    // Handle multi-option buttons
    document.querySelectorAll(".multi-option button").forEach((btn) => {
        btn.addEventListener("click", () => {
            const group = btn.parentElement;
            btn.classList.toggle("selected");
            const field = group.dataset.name;
            const selected = [...group.children]
                .filter((b) => b.classList.contains("selected"))
                .map((b) => b.innerText);
            vendorStep1[field] = selected;
            vendorStep2[field] = selected;
        });
    });

    // Validate and go to Step 2
    document.getElementById("next1").onclick = () => {
        const requiredStep1 = ["location", "photos", "delivery"];
        for (let field of requiredStep1) {
            const value = vendorStep1[field];
            const isEmpty = Array.isArray(value) ? value.length === 0 : !value;
            if (isEmpty) {
                return alert(`Please select an option for "${field}"`);
            }
        }
        step1.classList.add("hidden");
        step2.classList.remove("hidden");
    };

    // Go back to Step 1
    document.getElementById("back").onclick = () => {
        step2.classList.add("hidden");
        step1.classList.remove("hidden");
    };

    // Validate and Create Store
    document.getElementById("createStore").onclick = async () => {
        const storeName = document.getElementById("storeName").value.trim();
        if (!storeName) return alert("Please enter your store’s name!");

        const requiredStep2 = ["payment", "help", "storeType"];
        for (let field of requiredStep2) {
            const value = vendorStep2[field];
            const isEmpty = Array.isArray(value) ? value.length === 0 : !value;
            if (isEmpty) {
                return alert(`Please select an option for "${field}"`);
            }
        }
        const apiUrl = "http://192.168.18.7:10000/store/store";
        const params = new URLSearchParams({
            name: storeName,
            isphoto: isphoto,
            islocal: locationOptions.islocal,
            isinternational: locationOptions.isinternational,
            isboth: locationOptions.isboth,
            isdeliver: isdeliver,
            isbank: paymentOptions.isbank,
            iscard: paymentOptions.iscard,
            iscash: paymentOptions.iscash,
            ishelp: ishelp,
            type: type,
        });

        fetch(`${apiUrl}?${params.toString()}`, {
            method: "POST",
            headers: {
                Accept: "application/json",
            },
            body: null, // no payload, as in your `-d ''`
        })
            .then((response) => response.json())
            .then((data) => {
                console.log("API response:", data);
                // Extract the ID
                storeId = data.id;
                console.log("Returned store ID:", storeId);
                // Display it in the page
                document.getElementById("api-data").textContent =
                    JSON.stringify(data, null, 2);
            })
            .catch((error) => {
                console.error("Error:", error);
                document.getElementById("api-data").textContent =
                    "Error: " + error;
            });
        vendorStep2.storeName = storeName;

        await db
            .collection("vendors")
            .doc("currentVendor")
            .set({
                step1: vendorStep1,
                step2: vendorStep2,
                products: Array.from({ length: 10 }, (_, i) => ({
                    id: i + 1,
                    name: `Product ${i + 1}`,
                    size: "M",
                    price: 10 + i,
                })),
            });

        showProductList1();
    };

    // Redirect to product page
    async function showProductList() {
        window.location.href = "/vendor/product/index.html";
    }
    function addImageUploadFeature(card) {
        const input = card.querySelector(".image-upload");
        const previewBox = card.querySelector(".image-preview");

        input.addEventListener("change", function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewBox.innerHTML = `<img src="${e.target.result}" alt="Product Image" />`;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Display product page on same screen (if used)
    async function showProductList1() {
        step1.classList.add("hidden");
        step2.classList.add("hidden");
        productPage.classList.remove("hidden");

        const doc = await db.collection("vendors").doc("currentVendor").get();
        const data = doc.data();

        const productList = document.getElementById("productList");
        productList.innerHTML = "";

        document.getElementById("displayStoreName").innerText =
            "👤 " + data.step2.storeName;
        document.getElementById("displayStoreType").innerText =
            data.step2.storeType;

        data.products.forEach((product) => {
            const card = document.createElement("div");
            card.className = "product-card";
            card.innerHTML = `
      <div class="image-preview">
        + Add Image
        <input type="file" class="image-upload" accept="image/*" />
      </div>
      <input class="input-field product-name" type="text" placeholder="Product Name" />
      <input class="input-field product-price" type="number" placeholder="Price" />
      <div class="wishlist">♡ Add to Wishlist</div>
      <div class="flex-row">
        <input class="input-field product-qty half" type="number" placeholder="Quantity" />
        <select class="input-field product-size half">
          <option value="">Size</option>
          <option>S</option>
          <option>M</option>
          <option>L</option>
          <option>XL</option>
        </select>
      </div>
      <button class="add-button">+ Add to Cart</button>
    `;

            addImageUploadFeature(card);

            productList.appendChild(card); // First append to ensure DOM is ready
            const addBtn = card.querySelector(".add-button");
            addBtn.addEventListener("click", async () => {
                const name = card.querySelector(".product-name")?.value.trim();
                const price = card.querySelector(".product-price")?.value;
                const quantity = card.querySelector(".product-qty")?.value;
                const size = card.querySelector(".product-size")?.value;
                const image1 =
                    card.querySelector(".image-preview img")?.src || "";
                const image = "imageurl";

                console.log({ name, price, quantity, size, image }); // 🔍 Debug values

                if (!name || !price || !quantity || !size || !image) {
                    alert(
                        "Please fill out all product fields and upload an image."
                    );
                    return;
                }

                const params = new URLSearchParams({
                    name,
                    image,
                    price,
                    quantity,
                    size,
                    storeid: storeId,
                });

                try {
                    const response = await fetch(
                        `http://192.168.18.7:10000/store/product?${params.toString()}`,
                        {
                            method: "POST",
                            headers: {
                                Accept: "application/json",
                            },
                        }
                    );

                    const data = await response.json();
                    alert("Product added successfully!");
                    console.log("Product response:", data);
                } catch (error) {
                    console.error("Error adding product:", error);
                    alert("Product added successfully!");
                }
            });
        });
    }

    // "Add more" placeholder
    // "Add more" dynamically adds 5 product cards
    document.getElementById("addMore").onclick = () => {
        for (let i = 0; i < 5; i++) {
            const card = document.createElement("div");
            card.className = "product-card";
            card.innerHTML = `
      <div class="image-preview">
        + Add Image
        <input type="file" class="image-upload" accept="image/*" />
      </div>
      <input class="input-field product-name" type="text" placeholder="Product Name" />
      <input class="input-field product-price" type="number" placeholder="Price" />
      <div class="wishlist">♡ Add to Wishlist</div>
      <div class="flex-row">
        <input class="input-field product-qty half" type="number" placeholder="Quantity" />
        <select class="input-field product-size half">
          <option value="">Size</option>
          <option>S</option>
          <option>M</option>
          <option>L</option>
          <option>XL</option>
        </select>
      </div>
      <button class="add-button">+ Add to Cart</button>
    `;

            addImageUploadFeature(card);

            const productList = document.getElementById("productList");
            productList.appendChild(card);

            const addBtn = card.querySelector(".add-button");
            addBtn.addEventListener("click", async () => {
                const name = card.querySelector(".product-name")?.value.trim();
                const price = card.querySelector(".product-price")?.value;
                const quantity = card.querySelector(".product-qty")?.value;
                const size = card.querySelector(".product-size")?.value;
                const image1 =
                    card.querySelector(".image-preview img")?.src || "";
                const image = "imageurl"; // Use actual image upload logic here

                console.log({ name, price, quantity, size, image });

                if (!name || !price || !quantity || !size || !image) {
                    alert(
                        "Please fill out all product fields and upload an image."
                    );
                    return;
                }

                const params = new URLSearchParams({
                    name,
                    image,
                    price,
                    quantity,
                    size,
                    storeid: storeId,
                });

                try {
                    const response = await fetch(
                        `http://192.168.18.7:10000/store/product?${params.toString()}`,
                        {
                            method: "POST",
                            headers: {
                                Accept: "application/json",
                            },
                        }
                    );

                    const data = await response.json();
                    alert("Product added successfully!");
                    console.log("Product response:", data);
                } catch (error) {
                    console.error("Error adding product:", error);
                    alert("Failed to add product.");
                }
            });
        }
    };
};
