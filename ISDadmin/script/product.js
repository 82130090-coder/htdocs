function addProductCard() {
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

    reader.onload = function(event) {
        const product = {
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
    };

    reader.readAsDataURL(file); 
}

function displayProducts(category = null) {
    const productContainer = document.getElementById('product-container');
    productContainer.innerHTML = ''; 

    let products = JSON.parse(localStorage.getItem('products')) || [];

    
    if (category) {
        products = products.filter(product => product.category === category);
    }

    products.forEach(product => {
        const productCard = document.createElement('div');
        productCard.className = 'product-card';
        productCard.innerHTML = `
            <img src="${product.imageUrl}" alt="${product.title}" class="product-image" />
            <h3 class="product-title">${product.title}</h3>
            <p class="product-description">${product.description}</p>
            <p class="product-price">${product.price}</p>
            <button class="place-an-order">place an order</button>
            <button class="recycle-bin-button">
                <i class="fas fa-trash-alt"></i>
            </button>
        `;
        const placeOrderButton = productCard.querySelector('.place-an-order');
        placeOrderButton.onclick = function() {
            openOrderModal();
        };
        
        const recycleBinButton = productCard.querySelector('.recycle-bin-button');
        recycleBinButton.onclick = function() {
            const index = products.indexOf(product);
            if (index > -1) {
                products.splice(index, 1);
                localStorage.setItem('products', JSON.stringify(products));
                productCard.remove();
            }
        };

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
window.onload = function() {
    showProducts('all'); 
};



function openOrderModal() {
    const productContainer = document.getElementById('product-checkboxes');
    productContainer.innerHTML = '';

    const listsContainer = document.createElement('div');
    listsContainer.className = 'lists-container';

    const firstRow = document.createElement('div');
    firstRow.className = 'list-row';

    const secondRow = document.createElement('div');
    secondRow.className = 'list-row';

    const mainDishesList = document.createElement('ul'); 
    const sideDishesList = document.createElement('ul'); 
    const appetizersList = document.createElement('ul');
    const saladsList = document.createElement('ul'); 
    const drinksList = document.createElement('ul');
    const dessertsList = document.createElement('ul'); 

    let products = JSON.parse(localStorage.getItem('products')) || [];

    let mainDishesCount = 0;
    let sideDishesCount = 0;
    let appetizersCount = 0;
    let saladsCount = 0;
    let drinksCount = 0;
    let dessertsCount = 0;

    products.forEach(product => {
        const listItem = document.createElement('li');

     
        const titleElement = document.createElement('span');
        titleElement.className = 'product-title-class'; 
        titleElement.textContent = product.title +":";

        const priceElement = document.createElement('span');
        priceElement.className = 'product-price-class'; 
        priceElement.textContent = `$${parseFloat(product.price).toFixed(2)}`;

        listItem.appendChild(titleElement);
        listItem.appendChild(priceElement);

        if (product.category === 'main-dishes' && mainDishesCount < 3) {
            mainDishesList.appendChild(listItem);
            mainDishesCount++;
        } else if (product.category === 'side-dishes' && sideDishesCount < 3) {
            sideDishesList.appendChild(listItem);
            sideDishesCount++;
        } else if (product.category === 'appetizers' && appetizersCount < 3) {
            appetizersList.appendChild(listItem);
            appetizersCount++;
        } else if (product.category === 'salads' && saladsCount < 3) {
            saladsList.appendChild(listItem);
            saladsCount++;
        } else if (product.category === 'drinks' && drinksCount < 3) {
            drinksList.appendChild(listItem);
            drinksCount++;
        } else if (product.category === 'desserts' && dessertsCount < 3) {
            dessertsList.appendChild(listItem);
            dessertsCount++;
        }
    });

    firstRow.appendChild(mainDishesList);
    firstRow.appendChild(sideDishesList);
    firstRow.appendChild(appetizersList);

    secondRow.appendChild(saladsList);
    secondRow.appendChild(drinksList);
    secondRow.appendChild(dessertsList);

    listsContainer.appendChild(firstRow);
    listsContainer.appendChild(secondRow);

    productContainer.appendChild(listsContainer);

    document.getElementById('productModal').style.display = 'block';
}
function closeModal() {
    document.getElementById('productModal').style.display = 'none';
}