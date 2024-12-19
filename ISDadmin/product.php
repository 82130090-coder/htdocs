<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<script src="script/product.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="styles/product.css">
<body>
    <div class="inputs">
        <input type="file" id="imageInput" accept="image/*">
        <input type="text" id="titleInput" placeholder="Product Title">
        <input type="text" id="descriptionInput" placeholder="Product Description">
        <input type="text" id="priceInput" placeholder="Product Price">
        <select id="categorySelect">
            <option value="" disabled selected>Select a category</option>
            <option value="main-dishes">Main dishes</option>
            <option value="side-dishes">Side dishes</option>
            <option value="appetizers">Appetizers</option>
            <option value="salads">Salads</option>
            <option value="drinks">Drinks</option>
            <option value="desserts">Desserts</option>
        </select>
        <button onclick=" addProductCard()">Add Product</button> 
        <button id="refresh-storage" onclick="refreshStorage()">
            <i class="fas fa-sync-alt"></i> Refresh Local Storage
        </button>
        <a href="menu.php">Go to menu</a> 
    </div>
    <div id="product-container"></div> 


</body>
</html>