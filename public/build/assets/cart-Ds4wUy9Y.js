class r{constructor(){this.cart=JSON.parse(localStorage.getItem("f1_cart"))||[],this.init()}init(){this.updateCartCount(),this.initEventListeners()}initEventListeners(){document.addEventListener("click",t=>{t.target.classList.contains("add-to-cart")&&this.addItem(t.target.dataset),t.target.classList.contains("remove-from-cart")&&this.removeItem(t.target.dataset.productId),t.target.classList.contains("update-cart-quantity")&&this.updateQuantity(t.target.dataset.productId,parseInt(t.target.value))})}addItem(t){const a=this.cart.find(e=>e.id===t.productId);a?a.quantity+=1:this.cart.push({id:t.productId,name:t.productName,price:parseFloat(t.productPrice),image:t.productImage,quantity:1,category:t.productCategory}),this.saveCart(),this.showNotification(`${t.productName} agregado al carrito`)}removeItem(t){this.cart=this.cart.filter(a=>a.id!==t),this.saveCart(),this.updateCartDisplay()}updateQuantity(t,a){if(a<=0){this.removeItem(t);return}const e=this.cart.find(i=>i.id===t);e&&(e.quantity=a,this.saveCart(),this.updateCartDisplay())}clearCart(){this.cart=[],this.saveCart(),this.updateCartDisplay()}getTotal(){return this.cart.reduce((t,a)=>t+a.price*a.quantity,0)}getTotalItems(){return this.cart.reduce((t,a)=>t+a.quantity,0)}saveCart(){localStorage.setItem("f1_cart",JSON.stringify(this.cart)),this.updateCartCount(),this.dispatchCartUpdateEvent()}updateCartCount(){const t=document.querySelectorAll(".cart-count, .cart-badge"),a=this.getTotalItems();t.forEach(e=>{e.textContent=a,e.style.display=a>0?"flex":"none"})}updateCartDisplay(){const t=document.getElementById("cart-container");t&&this.renderCart(t)}renderCart(t){if(this.cart.length===0){t.innerHTML=`
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <p>Tu carrito está vacío</p>
                </div>
            `;return}t.innerHTML=this.cart.map(a=>`
            <div class="cart-item" data-product-id="${a.id}">
                <img src="${a.image}" alt="${a.name}" class="cart-item-image">
                <div class="cart-item-details">
                    <h4>${a.name}</h4>
                    <p>${window.app.formatPrice(a.price)} c/u</p>
                </div>
                <div class="cart-item-controls">
                    <input type="number" 
                           value="${a.quantity}" 
                           min="1" 
                           class="update-cart-quantity"
                           data-product-id="${a.id}">
                    <button class="remove-from-cart" data-product-id="${a.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="cart-item-total">
                    ${window.app.formatPrice(a.price*a.quantity)}
                </div>
            </div>
        `).join("")+`
            <div class="cart-total">
                <strong>Total: ${window.app.formatPrice(this.getTotal())}</strong>
            </div>
            <div class="cart-actions">
                <button class="btn btn-clear-cart">Vaciar Carrito</button>
                <button class="btn btn-checkout">Finalizar Compra</button>
            </div>
        `,t.querySelector(".btn-clear-cart").addEventListener("click",()=>this.clearCart()),t.querySelector(".btn-checkout").addEventListener("click",()=>this.checkout())}showNotification(t){const a=document.createElement("div");a.className="cart-notification",a.innerHTML=`
            <span>${t}</span>
            <button onclick="this.parentElement.remove()">×</button>
        `,document.body.appendChild(a),setTimeout(()=>{a.parentElement&&a.remove()},3e3)}dispatchCartUpdateEvent(){window.dispatchEvent(new CustomEvent("cartUpdated",{detail:{cart:this.cart}}))}checkout(){if(this.cart.length===0){alert("El carrito está vacío");return}window.app.showLoading(),setTimeout(()=>{window.app.hideLoading(),alert("¡Compra realizada con éxito!"),this.clearCart()},2e3)}}document.addEventListener("DOMContentLoaded",()=>{window.cartManager=new r});
