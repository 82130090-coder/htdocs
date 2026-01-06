<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db.php';

$signup_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;


$message = "";
$messageType = ""; // success or error

// ✅ CHECK BLACKLIST BEFORE SAVING ORDER
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $signup_id = $_SESSION['user_id'];

    // جلب بيانات الـ user من جدول signup
    $userData = $con->query("SELECT username, phone_number FROM signup WHERE id = '$signup_id' LIMIT 1")->fetch_assoc();
    $username = mysqli_real_escape_string($con, $userData['username']);
    $phone    = mysqli_real_escape_string($con, $userData['phone_number']);

    // check if phone exists in blacklist
    $check = $con->query("SELECT * FROM blacklist WHERE phone = '$phone'");

    if ($check && $check->num_rows > 0) {
        $message = "You are blacklisted and cannot place orders.";
        $messageType = "error";
    } else {
        // ✅ Save order if not blacklisted
        $comments = mysqli_real_escape_string($con, $_POST['comments']);
        $orderContent = mysqli_real_escape_string($con, $_POST['orderContent']);

        $con->query("
            INSERT INTO orders (signup_id, username, phone, orderContent, comments, created_at)
            VALUES ('$signup_id', '$username', '$phone', '$orderContent', '$comments', NOW())
        ");

        $message = "Order placed successfully!";
        $messageType = "success";
    }
}

// ✅ فلترة حسب الـ category
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
if ($category === 'all') {
    $result = $con->query("SELECT * FROM products ORDER BY created_at DESC");
} else {
    $stmt = $con->prepare("SELECT * FROM products WHERE category = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Menu</title>
    <link rel="stylesheet" href="styles/menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        #msgBox {
            position: relative;
            width: 100%;
            text-align: center;
            margin-top: 20px;
            display: none;
            opacity: 0;
            transition: opacity 1s ease;
        }
        .msg-success {
            background: #00c853;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            display: inline-block;
        }
        .msg-error {
            background: #d50000;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            display: inline-block;
        }
    </style>
</head>
<body>

<div id="bar">
    <div class="logo-div">
        <img src="images/logo.jpg" alt="Logo">
    </div>
    <div class="navbar">
        <a href="index.php">Home</a>
        <a href="menu.php">Menu</a>
        <a href="book.php">Book Table</a>
    </div>
</div>

<div id="msgBox">
    <?php if (!empty($message)): ?>
        <div class="<?php echo $messageType === 'success' ? 'msg-success' : 'msg-error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    window.onload = function () {
        const box = document.getElementById("msgBox");
        if (box.innerText.trim() !== "") {
            box.style.display = "block";
            setTimeout(() => { box.style.opacity = 1; }, 50);
            setTimeout(() => { box.style.opacity = 0; }, 3000);
            setTimeout(() => { box.style.display = "none"; }, 3500);
        }
    };
</script>

<h1>Our Menu</h1>
<div id="bar2">
    <div class="navbar2">
        <a href="menu.php?category=all"><button>All</button></a>
        <a href="menu.php?category=Main Dishes"><button>Main Dishes</button></a>
        <a href="menu.php?category=Burger"><button>Burger</button></a>
        <a href="menu.php?category=Appetizers"><button>Appetizers</button></a>
        <a href="menu.php?category=Salads"><button>Salads</button></a>
        <a href="menu.php?category=Drinks"><button>Drinks</button></a>
        <a href="menu.php?category=Desserts"><button>Desserts</button></a>
    </div>
</div>

<div id="product-container">
<?php
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $name = htmlspecialchars($row['name']);
        $price = (int)$row['price'];
        $priceFormatted = number_format($price);
        $image = htmlspecialchars($row['image']);
        $description = htmlspecialchars($row['description']);
        $category = htmlspecialchars($row['category']);

        echo "
        <div class='product-card' data-category='$category'>
            <img src='images/$image' alt='$name'>
            <h3>$name</h3>
            <p class='description'>$description</p>
            <p class='price'>LBP $priceFormatted</p>

            <div class='quantity-control'>
                <button onclick=\"decreaseQty($id)\">−</button>
                <input type='number' id='qty-$id' value='1' min='1' onchange=\"updateQty($id)\">
                <button onclick=\"increaseQty($id)\">+</button>
            </div>

            <button class='add-to-cart'
                    onclick=\"addToCart($id, '$name', $price, '$description')\">
                Add to Cart
            </button>
        </div>
        ";
    }
} else {
    echo "<p>No products found.</p>";
}
?>
</div>

<!-- Floating Cart Button -->
<div id="floating-cart-btn" onclick="openCartModal()" 
     style="position: fixed; bottom: 20px; right: 20px; background: #FFD700; padding: 15px; border-radius: 50%; cursor: pointer; box-shadow: 0 4px 8px rgba(0,0,0,0.2); z-index: 1000;">
    <i class="fas fa-shopping-cart" style="font-size: 24px;"></i>
    <span id="cart-count" 
          style="position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px;">
        0
    </span>
</div>

<!-- DARK OVERLAY -->
<div id="overlay" 
     style="display:none; position:fixed; left:0; top:0; width:100%; height:100%;
            background:rgba(0,0,0,0.7); z-index:1500;"
     onclick="closeAllModals()"></div>

<!-- CART POPUP -->
<div id="cartModal" 
     style="display:none; position: fixed; left: 50%; top: 50%; transform: translate(-50%, -50%);
            width: 450px; max-height: 70vh; background: #222; padding: 20px; border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.7); z-index: 2000; overflow-y:auto;">

    <span onclick="closeCartModal()" 
          style="float:right; cursor:pointer; font-size:24px; color:white;">&times;</span>

    <h2 style="color:#FFD700; margin-top:0;">Your Cart</h2>

    <div id="cart-items"></div>

    <div id="cart-total"
         style="margin-top: 20px; font-weight: bold; font-size: 18px; color:#00ff88;"></div>

    <button onclick="openCheckoutModal()" 
            style="width: 100%; padding: 10px; background: #00c853; color: white; border: none; margin-top: 20px; cursor: pointer; border-radius:5px;">
        Checkout
    </button>
</div>

<!-- CHECKOUT MODAL -->
<div id="checkoutModal" 
     style="display:none; position: fixed; left: 50%; top: 50%; transform: translate(-50%, -50%);
            width: 450px; background: #222; padding: 20px; border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.7); z-index: 3000;">

    <span onclick="closeCheckoutModal()" 
          style="float:right; cursor:pointer; font-size:24px; color:white;">&times;</span>

    <h2 style="color:#FFD700; margin-top:0;">Complete Your Order</h2>

    <form id="orderForm" method="POST" action="menu.php">
        <input type="hidden" id="signup_id" name="signup_id" value="<?php echo $signup_id; ?>">
        <input type="hidden" id="orderContent" name="orderContent">
        <label style="color:white;">Comments:</label>
        <textarea name="comments"
                  style="width:100%; padding:8px; border-radius:5px; border:none; margin-bottom:10px;"></textarea>
        <button type="submit" onclick="prepareOrderContent()" 
                style="width: 100%; padding: 10px; background: #FFD700; border: none; cursor: pointer; border-radius:5px;">
            Place Order
        </button>
    </form>
</div>

<script>
// ========= Persistent cart via localStorage =========
let cart = [];

// Load cart from localStorage on page load
function loadCart() {
    try {
        const saved = localStorage.getItem('gp_cart');
        cart = saved ? JSON.parse(saved) : [];
    } catch (e) {
        cart = [];
    }
    updateCartCount();
}

// Save cart to localStorage whenever it changes
function saveCart() {
    localStorage.setItem('gp_cart', JSON.stringify(cart));
}

// ====== Quantity on product cards ======
function increaseQty(id) {
    let input = document.getElementById("qty-" + id);
    input.value = parseInt(input.value) + 1;
}

function decreaseQty(id) {
    let input = document.getElementById("qty-" + id);
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

function updateQty(id) {
    let input = document.getElementById("qty-" + id);
    if (parseInt(input.value) < 1 || isNaN(parseInt(input.value))) {
        input.value = 1;
    }
}

// ✅ Show message (success/error)
function showMessage(text, type) {
    const box = document.getElementById("msgBox");
    box.innerHTML = `<div class="${type === 'success' ? 'msg-success' : 'msg-error'}">${text}</div>`;
    box.style.display = "block";

    setTimeout(() => { box.style.opacity = 1; }, 50);
    setTimeout(() => { box.style.opacity = 0; }, 3000);
    setTimeout(() => { box.style.display = "none"; }, 3500);
}

// ====== Cart logic ======
function addToCart(id, name, price, description) {
    let qty = parseInt(document.getElementById("qty-" + id).value);
    if (isNaN(qty) || qty < 1) qty = 1;

    let existing = cart.find(item => item.id === id);

    if (existing) {
        existing.qty += qty;
    } else {
        cart.push({
            id: id,
            name: name,
            price: price,
            description: description,
            qty: qty
        });
    }

    saveCart();           // ✅ persist
    updateCartCount();
    showMessage(name + " added to cart!", "success");
}

function updateCartCount() {
    const count = cart.reduce((sum, item) => sum + item.qty, 0);
    document.getElementById("cart-count").innerText = count;
}

function openCartModal() {
    displayCart();
    document.getElementById("cartModal").style.display = "block";
    document.getElementById("overlay").style.display = "block";
}

function closeCartModal() {
    document.getElementById("cartModal").style.display = "none";
    document.getElementById("overlay").style.display = "none";
}

function closeAllModals() {
    document.getElementById("cartModal").style.display = "none";
    document.getElementById("checkoutModal").style.display = "none";
    document.getElementById("overlay").style.display = "none";
}

function displayCart() {
    let container = document.getElementById("cart-items");
    container.innerHTML = "";

    let total = 0;

    if (cart.length === 0) {
        container.innerHTML = "<p style='color:#ccc;'>Your cart is empty.</p>";
        document.getElementById("cart-total").innerHTML = "";
        return;
    }

    cart.forEach(item => {
        total += item.price * item.qty;

        container.innerHTML += `
            <div style="background:#333; padding:10px; border-radius:8px; margin-bottom:10px;">
                <h4 style="color:#FFD700; margin:0;">${item.name}</h4>
                <p style="color:#ccc; margin:5px 0;">${item.description}</p>
                <p style="color:#00ff88; font-weight:bold;">LBP ${item.price.toLocaleString()}</p>

                <div style="display:flex; align-items:center; margin-top:5px;">
                    <button onclick="changeCartQty(${item.id}, -1)"
                            style="background:#FFD700; border:none; padding:5px 10px; border-radius:5px;">−</button>
                    <input type="number" value="${item.qty}" min="1"
                           onchange="setCartQty(${item.id}, this.value)"
                           style="width:50px; text-align:center; margin:0 10px; border-radius:5px; border:none;">
                    <button onclick="changeCartQty(${item.id}, 1)"
                            style="background:#FFD700; border:none; padding:5px 10px; border-radius:5px;">+</button>
                </div>

                <button onclick="removeFromCart(${item.id})"
                        style="background:red; border:none; padding:5px 10px; color:white; border-radius:5px; margin-top:10px;">
                    Remove
                </button>
            </div>
        `;
    });

    document.getElementById("cart-total").innerHTML =
        "Total: LBP " + total.toLocaleString();
}

function changeCartQty(id, amount) {
    let item = cart.find(i => i.id === id);
    if (!item) return;

    item.qty += amount;
    if (item.qty < 1) item.qty = 1;

    saveCart();           // ✅ persist
    updateCartCount();
    displayCart();
}

function setCartQty(id, value) {
    let item = cart.find(i => i.id === id);
    if (!item) return;

    let qty = parseInt(value);
    if (isNaN(qty) || qty < 1) qty = 1;

    item.qty = qty;

    saveCart();           // ✅ persist
    updateCartCount();
    displayCart();
}

function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    saveCart();           // ✅ persist
    updateCartCount();
    displayCart();
}

// ====== Checkout logic ======
function openCheckoutModal() {
    if (cart.length === 0) {
        showMessage("Your cart is empty!", "error");
        return;
    }

    const isLoggedIn = <?php echo $signup_id ? 'true' : 'false'; ?>;
    if (!isLoggedIn) {
        // ✅ تحويل مباشر لصفحة تسجيل الدخول
        window.location.href = "login.php?return=" + encodeURIComponent(window.location.pathname + window.location.search);
        return;
    }

    document.getElementById("checkoutModal").style.display = "block";
    document.getElementById("overlay").style.display = "block";
}


function closeCheckoutModal() {
    document.getElementById("checkoutModal").style.display = "none";
    document.getElementById("overlay").style.display = "none";
}

function prepareOrderContent() {
    // ✅ Always send the latest persisted cart
    document.getElementById("orderContent").value = JSON.stringify(cart);
}

// Initialize cart from storage on load
document.addEventListener('DOMContentLoaded', loadCart);
</script>

</body>
</html>
