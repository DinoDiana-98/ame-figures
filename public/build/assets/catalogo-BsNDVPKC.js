class n{constructor(){this.filters={category:"",priceRange:"",search:""},this.currentPage=1,this.productsPerPage=12,this.init()}init(){this.initEventListeners(),this.initFilters(),this.initProductInteractions(),this.loadProducts()}initEventListeners(){document.getElementById("categoryFilter")?.addEventListener("change",t=>{this.filters.category=t.target.value,this.applyFilters()}),document.getElementById("priceFilter")?.addEventListener("change",t=>{this.filters.priceRange=t.target.value,this.applyFilters()}),document.getElementById("searchInput")?.addEventListener("input",this.debounce(t=>{this.filters.search=t.target.value,this.applyFilters()},300)),document.getElementById("sortSelect")?.addEventListener("change",t=>{this.sortProducts(t.target.value)}),document.addEventListener("click",t=>{t.target.classList.contains("page-link")&&(t.preventDefault(),this.goToPage(parseInt(t.target.dataset.page)))})}initFilters(){const t=new URLSearchParams(window.location.search);this.filters={category:t.get("category")||"",priceRange:t.get("price")||"",search:t.get("search")||""},this.filters.category&&(document.getElementById("categoryFilter").value=this.filters.category),this.filters.priceRange&&(document.getElementById("priceFilter").value=this.filters.priceRange),this.filters.search&&(document.getElementById("searchInput").value=this.filters.search)}initProductInteractions(){document.addEventListener("click",t=>{t.target.classList.contains("quick-view-btn")&&this.showQuickView(t.target.dataset.productId),t.target.classList.contains("favorite-btn")&&this.toggleFavorite(t.target.dataset.productId)}),window.addEventListener("scroll",this.debounce(()=>{this.isNearBottom()&&this.loadMoreProducts()},100))}async loadProducts(){try{const i=await(await fetch("/api/products")).json();this.displayProducts(i)}catch(t){console.error("Error loading products:",t),this.displayProducts(this.getMockProducts())}}displayProducts(t){const i=document.getElementById("productsGrid");if(!i)return;const e=this.applyFiltersToProducts(t),r=this.paginateProducts(e);i.innerHTML=r.map(a=>`
            <div class="product-card" data-product-id="${a.id}">
                <div class="product-image-container">
                    <img src="${a.image}" alt="${a.name}" class="product-image">
                    <div class="product-actions">
                        <button class="quick-view-btn" data-product-id="${a.id}">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="favorite-btn" data-product-id="${a.id}">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                </div>
                <div class="product-info">
                    <h3 class="product-name">${a.name}</h3>
                    <p class="product-description">${a.description}</p>
                    <div class="product-price">${window.app.formatPrice(a.price)}</div>
                    <button class="btn btn-add-to-cart add-to-cart"
                            data-product-id="${a.id}"
                            data-product-name="${a.name}"
                            data-product-price="${a.price}"
                            data-product-image="${a.image}"
                            data-product-category="${a.category}">
                        Agregar al Carrito
                    </button>
                </div>
            </div>
        `).join(""),this.updatePagination(e.length)}applyFilters(){this.updateURL(),this.loadProducts()}applyFiltersToProducts(t){return t.filter(i=>{if(this.filters.category&&i.category!==this.filters.category)return!1;if(this.filters.priceRange){const[e,r]=this.filters.priceRange.split("-").map(Number);if(r&&(i.price<e||i.price>r)||!r&&i.price<e)return!1}if(this.filters.search){const e=this.filters.search.toLowerCase();if(!i.name.toLowerCase().includes(e)&&!i.description.toLowerCase().includes(e))return!1}return!0})}sortProducts(t){console.log("Sorting by:",t)}updateURL(){const t=new URL(window.location);Object.entries(this.filters).forEach(([i,e])=>{e?t.searchParams.set(i,e):t.searchParams.delete(i)}),window.history.replaceState({},"",t)}updatePagination(t){const i=Math.ceil(t/this.productsPerPage),e=document.getElementById("pagination");if(!e)return;let r="";i>1&&(r=`
                <nav>
                    <ul class="pagination">
                        ${this.generatePaginationLinks(i)}
                    </ul>
                </nav>
            `),e.innerHTML=r}generatePaginationLinks(t){let i="";for(let e=1;e<=t;e++)i+=`
                <li class="page-item ${e===this.currentPage?"active":""}">
                    <a class="page-link" href="#" data-page="${e}">${e}</a>
                </li>
            `;return i}goToPage(t){this.currentPage=t,this.loadProducts(),window.scrollTo(0,0)}showQuickView(t){console.log("Quick view for product:",t)}toggleFavorite(t){console.log("Toggle favorite:",t)}debounce(t,i){let e;return function(...a){const s=()=>{clearTimeout(e),t(...a)};clearTimeout(e),e=setTimeout(s,i)}}isNearBottom(){return window.innerHeight+window.scrollY>=document.body.offsetHeight-500}loadMoreProducts(){this.currentPage++}getMockProducts(){return[{id:1,name:"Camiseta Ferrari F1",price:25e3,image:"/images/products/ferrari-shirt.jpg",category:"clothing",description:"Camiseta oficial del equipo Ferrari"},{id:2,name:"Gorra Red Bull Racing",price:12e3,image:"/images/products/redbull-cap.jpg",category:"accessories",description:"Gorra oficial Red Bull Racing"}]}}document.addEventListener("DOMContentLoaded",()=>{document.getElementById("productsGrid")&&(window.catalogoManager=new n)});
