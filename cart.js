if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", ready);
} else {
    ready();
}

function ready() {
    const addToCartButtons = document.getElementsByClassName('btn-add');
    for (let i = 0; i < addToCartButtons.length; i++) {
        const button = addToCartButtons[i];
        button.addEventListener('click', addToCartClicked);
    }
}

function addToCartClicked(event) {
    const button = event.target;
    const shopItem = button.parentElement;
    const title = shopItem.getElementsByClassName('shop-item-title')[0].innerText;
    const price = shopItem.getElementsByClassName('shop-item-price')[0].innerText;
    const imageSrc = shopItem.getElementsByTagName('img')[0].src;
    addItemToCart(title, price, imageSrc);
    updateCartTotal();
}

function addItemToCart(title, price, imageSrc) {
    const cartItems = document.getElementsByClassName('cart-items')[0];
    const cartItemNames = cartItems.getElementsByClassName('cart-item-title');

    for (let i = 0; i < cartItemNames.length; i++) {
        if (cartItemNames[i].innerText === title) {
            alert("Item already in cart!");
            return;
        }
    }

    const cartRow = document.createElement('div');
    cartRow.classList.add('cart-row');
    cartRow.innerHTML = `
        <img src="${imageSrc}" alt="">
        <span class="cart-item-title">${title}</span>
        <span class="cart-price">${price}</span>
        <input class="cart-quantity-input" type="number" value="1">
        <button class="btn-remove">REMOVE</button>
    `;
    cartItems.appendChild(cartRow);

    cartRow.getElementsByClassName('btn-remove')[0].addEventListener('click', removeCartItem);
    cartRow.getElementsByClassName('cart-quantity-input')[0].addEventListener('change', quantityChanged);
}

function removeCartItem(event) {
    const buttonClicked = event.target;
    buttonClicked.parentElement.remove();
    updateCartTotal();
}

function quantityChanged(event) {
    const input = event.target;
    if (isNaN(input.value) || input.value <= 0) {
        input.value = 1;
    }
    updateCartTotal();
}

function updateCartTotal() {
    const cartItems = document.getElementsByClassName('cart-items')[0];
    const cartRows = cartItems.getElementsByClassName('cart-row');
    let total = 0;

    for (let i = 0; i < cartRows.length; i++) {
        const cartRow = cartRows[i];
        const priceElement = cartRow.getElementsByClassName('cart-price')[0];
        const quantityElement = cartRow.getElementsByClassName('cart-quantity-input')[0];

        const price = parseFloat(priceElement.innerText.replace("₱", "").replace(",", ""));
        const quantity = quantityElement.value;
        total += price * quantity;
    }

    total = Math.round(total * 100) / 100;
    document.getElementById('cart-total-price').innerText = total.toFixed(2);
}
