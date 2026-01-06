// Cart Logic
let cart = JSON.parse(localStorage.getItem('cart')) || [];

function updateCartCount() {
    const count = cart.reduce((acc, item) => acc + item.quantity, 0);
    const badge = document.getElementById('cart-count');
    if (badge) badge.innerText = count;
}

function addToCart(product) {
    const existing = cart.find(item => item.title === product.title);
    if (existing) {
        existing.quantity++;
    } else {
        cart.push({ ...product, quantity: 1 });
    }
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    showToast("Added " + product.title + " to cart");
}

function showToast(message) {
    let toast = document.getElementById('toast-notification');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast-notification';
        toast.style.position = 'fixed';
        toast.style.bottom = '80px';
        toast.style.right = '20px';
        toast.style.backgroundColor = '#333';
        toast.style.color = '#fff';
        toast.style.padding = '10px 20px';
        toast.style.borderRadius = '5px';
        toast.style.zIndex = '2000';
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.5s ease-in-out';
        document.body.appendChild(toast);
    }
    toast.innerText = message;
    toast.style.opacity = '1';
    setTimeout(() => {
        toast.style.opacity = '0';
    }, 3000);
}

function removeFromCart(index) {
    cart.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cart));
    displayCart();
    updateCartCount();
}

function displayCart() {
    const cartItems = document.getElementById('cart-items');
    const cartTotal = document.getElementById('cart-total');
    cartItems.innerHTML = '';

    let total = 0;
    cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;

        const div = document.createElement('div');
        div.style.borderBottom = "1px solid #ddd";
        div.style.padding = "10px 0";
        div.innerHTML = `
            <div style="display:flex; justify-content:space-between;">
                <strong>${item.title}</strong>
                <span>$${parseFloat(item.price).toFixed(2)}</span>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:5px;">
                <span>Qty: ${item.quantity}</span>
                <span style="color:red; cursor:pointer;" onclick="removeFromCart(${index})">Remove</span>
            </div>
        `;
        cartItems.appendChild(div);
    });

    cartTotal.innerText = "Total: $" + total.toFixed(2);
}

function openCartModal() {
    console.log("Product.js: openCartModal called");
    const modal = document.getElementById('cartModal');
    if (modal) {
        modal.style.display = 'block';
        displayCart();
    } else {
        console.error("Cart Modal not found in DOM");
    }
}

window.closeCartModal = function () {
    document.getElementById('cartModal').style.display = 'none';
};


window.checkout = function () {
    try {
        if (!cart || cart.length === 0) {
            alert("Cart is empty!");
            return;
        }

        const productModal = document.getElementById('productModal');
        const cartModal = document.getElementById('cartModal');
        const orderContentInput = document.getElementById('orderContent');

        if (!productModal) {
            alert("Error: Product Modal not found!");
            return;
        }
        if (!orderContentInput) {
            alert("Error: Order Content Input not found!");
            return;
        }

        // Open the checkout modal
        productModal.style.display = 'block';

        // Close the cart modal if it exists
        if (cartModal) {
            cartModal.style.display = 'none';
        }

        // Auto-fill order content
        const orderContent = cart.map(item => `${item.title} (x${item.quantity})`).join(", ");
        orderContentInput.value = orderContent;

    } catch (e) {
        alert("Checkout Error: " + e.message);
        console.error(e);
    }
};


// Product Logic

// Product Logic
function addProductCard() {
    // Only allow if admin
    if (typeof isAdmin === 'undefined' || !isAdmin) {
        alert("Unauthorized action: You are not recognized as an admin.");
        return;
    }
    const title = document.getElementById('titleInput').value;
    const description = document.getElementById('descriptionInput').value;
    const price = document.getElementById('priceInput').value;
    const category = document.getElementById('categorySelect').value;
    const imageInput = document.getElementById('imageInput');

    if (!title || !description || !price || !category || !imageInput.files.length) {
        alert("Please fill in all fields.");
        return;
    }

    const file = imageInput.files[0];
    const reader = new FileReader();

    reader.onload = function (event) {
        try {
            const product = {
                id: Date.now(), // Unique ID
                title,
                description,
                price,
                category,
                imageUrl: event.target.result
            };

            let products = JSON.parse(localStorage.getItem('products')) || [];
            products.push(product);
            localStorage.setItem('products', JSON.stringify(products));

            document.getElementById('titleInput').value = '';
            document.getElementById('descriptionInput').value = '';
            document.getElementById('priceInput').value = '';
            document.getElementById('categorySelect').value = '';
            imageInput.value = '';

            displayProducts();
            alert("Product added successfully!");
        } catch (e) {
            console.error("Error saving product:", e);
            if (e.name === 'QuotaExceededError') {
                alert("Storage full! Please delete some items or clear cache.");
            } else {
                alert("Failed to save product: " + e.message);
            }
        }
    };

    reader.readAsDataURL(file);
}

function displayProducts(category = null) {
    const productContainer = document.getElementById('product-container');
    if (!productContainer) return; // Guard clause
    productContainer.innerHTML = '';

    let products = [];
    try {
        products = JSON.parse(localStorage.getItem('products')) || [];
    } catch (e) {
        console.error("Error reading products:", e);
        products = [];
    }

    if (category) {
        products = products.filter(product => product.category === category);
    }

    products.forEach((product, index) => {
        const productCard = document.createElement('div');
        productCard.className = 'product-card';

        // Image
        const img = document.createElement('img');
        img.src = product.imageUrl;
        img.alt = product.title;
        img.className = 'product-image';

        // Title
        const h3 = document.createElement('h3');
        h3.className = 'product-title';
        h3.textContent = product.title;

        // Description
        const pDesc = document.createElement('p');
        pDesc.className = 'product-description';
        pDesc.textContent = product.description;

        // Price
        const pPrice = document.createElement('p');
        pPrice.className = 'product-price';
        // Ensure price is a number
        const priceVal = parseFloat(product.price);
        pPrice.textContent = isNaN(priceVal) ? product.price : priceVal.toFixed(2);

        // Add to Cart Button
        const btnAdd = document.createElement('button');
        btnAdd.className = 'place-an-order';
        btnAdd.textContent = 'Add to Cart';
        btnAdd.onclick = function () {
            addToCart(product);
        };

        // Assemble Card
        productCard.appendChild(img);
        productCard.appendChild(h3);
        productCard.appendChild(pDesc);
        productCard.appendChild(pPrice);
        productCard.appendChild(btnAdd);

        // Delete Button (Admin Only)
        // Check if global isAdmin is explicitly true
        if (typeof isAdmin !== 'undefined' && isAdmin === true) {
            const btnDelete = document.createElement('button');
            btnDelete.className = 'recycle-bin-button';
            btnDelete.innerHTML = '<i class="fas fa-trash-alt"></i>';
            btnDelete.onclick = function () {
                if (!confirm("Are you sure you want to delete this product?")) return;

                try {
                    let allProducts = JSON.parse(localStorage.getItem('products')) || [];
                    // Filter out the product by ID if it exists, otherwise fallback to index/content
                    if (product.id) {
                        allProducts = allProducts.filter(p => p.id !== product.id);
                    } else {
                        // Fallback for old items without IDs
                        const realIndex = allProducts.findIndex(p => p.title === product.title && p.description === product.description);
                        if (realIndex > -1) allProducts.splice(realIndex, 1);
                    }

                    localStorage.setItem('products', JSON.stringify(allProducts));
                    // Refresh view - keep current category view
                    if (category) showProducts(category);
                    else showProducts('all');
                } catch (e) {
                    alert("Error deleting product: " + e.message);
                }
            };
            productCard.appendChild(btnDelete);
        }

        productContainer.appendChild(productCard);
    });
}


function showProducts(category) {
    if (category === 'all') {
        displayProducts();
    } else {
        displayProducts(category);
    }
}
function refreshStorage() {
    localStorage.removeItem('products');
    location.reload();
}
window.onload = function () {
    showProducts('all');
    updateCartCount();
};

function closeModal() {
    document.getElementById('productModal').style.display = 'none';
}