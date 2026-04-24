import { menData, menSubData } from '../data/menData';
import { womenData, womenSubData } from '../data/womenData';
import { kidsData, kidsSubData } from '../data/kidsData';

document.addEventListener("DOMContentLoaded", () => {
  const productsToggle = document.getElementById("products_toggle");
  const collectionsToggle = document.getElementById("collections_toggle");
  const header = document.querySelector("header");
  const sliderWrapper = document.querySelector(".dropdown_menu--products .dropdown_slider_wrapper");
  const dropdownIcon = productsToggle ? productsToggle.querySelector("i") : null;
  const collectionsIcon = collectionsToggle ? collectionsToggle.querySelector("i") : null;
  const breadcrumbs = document.getElementById("breadcrumbs");
  let npDragLockUntil = 0;

  function setHeaderMode(mode) {
    if (!header) return;

    header.classList.remove("products_open", "collections_open");

    if (dropdownIcon) {
      dropdownIcon.classList.remove("ri-arrow-up-s-line");
      dropdownIcon.classList.add("ri-arrow-down-s-line");
    }
    if (collectionsIcon) {
      collectionsIcon.classList.remove("ri-arrow-up-s-line");
      collectionsIcon.classList.add("ri-arrow-down-s-line");
    }

    if (mode === "products") {
      header.classList.add("products_open");
      if (dropdownIcon) {
        dropdownIcon.classList.remove("ri-arrow-down-s-line");
        dropdownIcon.classList.add("ri-arrow-up-s-line");
      }
      return;
    }

    if (mode === "collections") {
      header.classList.add("collections_open");
      if (collectionsIcon) {
        collectionsIcon.classList.remove("ri-arrow-down-s-line");
        collectionsIcon.classList.add("ri-arrow-up-s-line");
      }
      if (sliderWrapper) {
        setTimeout(() => {
          sliderWrapper.classList.remove("slide_active");
        }, 300);
      }
      return;
    }

    if (sliderWrapper) {
      setTimeout(() => {
        sliderWrapper.classList.remove("slide_active");
      }, 300);
    }
  }

  // Toggle Dropdown
  if (productsToggle && header && dropdownIcon && sliderWrapper) {
    productsToggle.addEventListener("click", (e) => {
      e.preventDefault();
      setHeaderMode(header.classList.contains("products_open") ? null : "products");
    });
  }

  if (collectionsToggle && header && collectionsIcon) {
    collectionsToggle.addEventListener("click", (e) => {
      e.preventDefault();
      setHeaderMode(header.classList.contains("collections_open") ? null : "collections");
    });
  }

  // Category Data - nu geïmporteerd
  const categoryData = {
    men: menData,
    women: womenData,
    kids: kidsData,
  };

  // Render Categories
  function renderCategories() {
    const containers = {
      men: document.getElementById("list_men"),
      women: document.getElementById("list_women"),
      kids: document.getElementById("list_kids"),
    };

    for (const [key, items] of Object.entries(categoryData)) {
      const container = containers[key];
      if (container) {
        container.innerHTML = "";
        items.forEach((item) => {
          const a = document.createElement("a");
          a.href = `/products/${key}${item.name ? `?category=${encodeURIComponent(item.name)}` : ''}`;
          a.className = "cat_link";
          if (item.target) a.dataset.target = item.target;
          if (item.isSale) {
            a.classList.add("sale");
            a.innerHTML = `${item.name} <i class="ri-arrow-right-up-line"></i>`;
          } else {
            a.textContent = item.name;
          }
          container.appendChild(a);
        });
      }
    }
  }

  // Render Subcategories
  function renderSubCategories(target, container) {
    const allSubData = {
      ...menSubData,
      ...womenSubData,
      ...kidsSubData,
    };

    const subItems = allSubData[target];
    if (!subItems || !container) return;

    const links = container.querySelectorAll(".sub_link");
    links.forEach((link) => link.remove());

    const group = (target || '').split('-')[0] || '';
    const current = container.querySelector('.breadcrumb_current')?.textContent?.trim() || '';
    const all = document.createElement("a");
    if (current.toUpperCase() === 'SALE') {
      all.href = group ? `/products/${group}?discounted=1` : '/products?discounted=1';
    } else {
      all.href = group ? `/products/${group}${current ? `?category=${encodeURIComponent(current)}` : ''}` : '/products';
    }
    all.className = "sub_link";
    all.textContent = "All Items";
    container.appendChild(all);

    subItems.forEach((item) => {
      const a = document.createElement("a");
      const name = (item?.name || '').trim();
      const isAll = /^all\s+/i.test(name);
      const m = name.match(/(\d+)\s*%/);
      if (current.toUpperCase() === 'SALE') {
        if (isAll) {
          a.href = group ? `/products/${group}?discounted=1` : '/products?discounted=1';
        } else if (m) {
          a.href = group ? `/products/${group}?discounts[]=${encodeURIComponent(m[1])}` : `/products?discounts[]=${encodeURIComponent(m[1])}`;
        } else {
          a.href = group ? `/products/${group}?discounted=1` : '/products?discounted=1';
        }
      } else if (isAll) {
        a.href = group ? `/products/${group}${current ? `?category=${encodeURIComponent(current)}` : ''}` : '/products';
      } else {
        a.href = group
          ? `/products/${group}${current ? `?category=${encodeURIComponent(current)}&subcategory=${encodeURIComponent(name)}` : `?subcategory=${encodeURIComponent(name)}`}`
          : `/products?subcategory=${encodeURIComponent(name)}`;
      }
      a.className = "sub_link";
      a.textContent = item.name;
      container.appendChild(a);
    });
  }

  // Initial Render
  try {
    renderCategories();
  } catch (error) {
    console.error("Error rendering categories:", error);
  }

  // Tab Switching Logic
  const tabLinks = document.querySelectorAll(".tab_link");
  const tabSlider = document.querySelector(".tab_slider");

  tabLinks.forEach((link, index) => {
    link.addEventListener("click", (e) => {
      e.preventDefault();
      tabLinks.forEach((l) => l.classList.remove("active"));
      link.classList.add("active");

      if (tabSlider) {
        const translateValue = index * -33.333;
        tabSlider.style.transform = `translateX(${translateValue}%)`;
      }

      if (sliderWrapper) {
        sliderWrapper.classList.remove("slide_active");
      }
    });
  });

  // Slide to Sub-Categories
  if (tabSlider && sliderWrapper) {
    tabSlider.addEventListener("click", (e) => {
      const link = e.target.closest(".cat_link[data-target]");
      if (link) {
        e.preventDefault();
        const targetKey = link.dataset.target;
        const subPanel = document.querySelector(".panel_sub");

        if (targetKey && subPanel) {
          renderSubCategories(targetKey, subPanel);

          if (breadcrumbs) {
            const activeTab = document.querySelector(".tab_link.active");
            const parentName = activeTab ? activeTab.textContent.trim() : "MENU";
            const currentName = link.textContent.trim();

            breadcrumbs.innerHTML = `
              <span class="breadcrumb_link" data-action="back">${parentName}</span>
              <span class="breadcrumb_separator"><i class="ri-arrow-right-s-line"></i></span>
              <span class="breadcrumb_current">${currentName}</span>
            `;
          }

          sliderWrapper.classList.add("slide_active");
        }
      }
    });
  }

  // Back to Main Categories
  if (breadcrumbs && sliderWrapper) {
    breadcrumbs.addEventListener("click", (e) => {
      if (e.target.closest('[data-action="back"]')) {
        sliderWrapper.classList.remove("slide_active");
      }
    });
  }

  // Close menu when clicking outside
  document.addEventListener("click", (e) => {
    if (header && !header.contains(e.target)) {
      if (header.classList.contains("products_open") || header.classList.contains("collections_open")) {
        setHeaderMode(null);
      }
    }
  });

  const productFiltersForm = document.getElementById('productFiltersForm');
  if (productFiltersForm) {
    const groupFromPath = (() => {
      const parts = window.location.pathname.split('/').filter(Boolean);
      const idx = parts.indexOf('products');
      const g = idx >= 0 ? (parts[idx + 1] || '') : '';
      return ['men', 'women', 'kids'].includes(g) ? g : '';
    })();

    const categoryInput = document.getElementById('productsCategoryInput');
    const subcategoryInput = document.getElementById('productsSubcategoryInput');

    const groupLinks = document.getElementById('productsGroupLinks');
    const categoryLinks = document.getElementById('productsCategoryLinks');
    const subcategoryLinks = document.getElementById('productsSubcategoryLinks');

    if (groupLinks) {
      const groups = [
        { key: '', label: 'ALL' },
        { key: 'men', label: 'MEN' },
        { key: 'women', label: 'WOMEN' },
        { key: 'kids', label: 'CHILDREN' },
      ];
      groupLinks.innerHTML = '';
      groups.forEach((g) => {
        const a = document.createElement('a');
        a.className = 'filter_link' + ((groupFromPath || '') === g.key ? ' active' : '');
        a.textContent = g.label;
        a.href = g.key ? `/products/${g.key}` : '/products';
        groupLinks.appendChild(a);
      });
    }

    const buildLink = (label, href, active, filter, value) => {
      const a = document.createElement('a');
      a.className = 'filter_link' + (active ? ' active' : '');
      a.textContent = label;
      a.href = href;
      if (filter) a.dataset.filter = filter;
      if (value !== undefined) a.dataset.value = value;
      return a;
    };

    const renderCategoryLinks = () => {
      if (!categoryLinks) return;
      const selectedCategory = (categoryInput?.value || '').trim();
      const selectedSubcategory = (subcategoryInput?.value || '').trim();
      categoryLinks.innerHTML = '';
      if (!groupFromPath) {
        const empty = document.createElement('div');
        empty.className = 'filter_empty';
        empty.textContent = 'Select main category';
        categoryLinks.appendChild(empty);
        return;
      }
      const allHref = `/products/${groupFromPath}`;
      categoryLinks.appendChild(buildLink('ALL', allHref, selectedCategory === '' && selectedSubcategory === '', 'category', ''));
      (categoryData[groupFromPath] || []).forEach((item) => {
        const name = (item?.name || '').trim();
        if (!name) return;
        if (item.isSale) {
          categoryLinks.appendChild(buildLink(name, `/products/${groupFromPath}?discounted=1`, false, 'sale', '1'));
        } else {
          categoryLinks.appendChild(buildLink(name, `/products/${groupFromPath}?category=${encodeURIComponent(name)}`, name.toUpperCase() === selectedCategory.toUpperCase(), 'category', name));
        }
      });
    };

    const renderSubcategoryLinks = () => {
      if (!subcategoryLinks) return;
      const selectedCategory = (categoryInput?.value || '').trim();
      const selectedSubcategory = (subcategoryInput?.value || '').trim();
      subcategoryLinks.innerHTML = '';
      if (!groupFromPath || !selectedCategory) {
        const empty = document.createElement('div');
        empty.className = 'filter_empty';
        empty.textContent = 'Select category';
        subcategoryLinks.appendChild(empty);
        return;
      }
      const allSubData = { ...menSubData, ...womenSubData, ...kidsSubData };
      const items = (categoryData[groupFromPath] || []);
      const current = items.find((it) => String(it.name || '').toUpperCase() === selectedCategory.toUpperCase());
      const target = current?.target || '';
      const subs = target ? (allSubData[target] || []) : [];
      if (!subs.length) {
        const empty = document.createElement('div');
        empty.className = 'filter_empty';
        empty.textContent = 'No sub categories';
        subcategoryLinks.appendChild(empty);
        return;
      }
      subs.forEach((it) => {
        const name = String(it?.name || '').trim();
        if (!name) return;
        const isAll = /^all\s+/i.test(name);
        const m = name.match(/(\d+)\s*%/);
        if (selectedCategory.toUpperCase() === 'SALE') {
          if (isAll) {
            subcategoryLinks.appendChild(buildLink(name, `/products/${groupFromPath}?discounted=1`, false, 'discounted', '1'));
          } else if (m) {
            subcategoryLinks.appendChild(buildLink(name, `/products/${groupFromPath}?discounts[]=${encodeURIComponent(m[1])}`, false, 'discount', m[1]));
          }
        } else if (isAll) {
          subcategoryLinks.appendChild(buildLink(name, `/products/${groupFromPath}?category=${encodeURIComponent(selectedCategory)}`, selectedSubcategory === '', 'subcategory', ''));
        } else {
          subcategoryLinks.appendChild(buildLink(name, `/products/${groupFromPath}?category=${encodeURIComponent(selectedCategory)}&subcategory=${encodeURIComponent(name)}`, name.toUpperCase() === selectedSubcategory.toUpperCase(), 'subcategory', name));
        }
      });
    };

    renderCategoryLinks();
    renderSubcategoryLinks();

    productFiltersForm.addEventListener('click', (e) => {
      const link = e.target.closest('a[data-filter]');
      if (!link) return;
      const filter = link.dataset.filter || '';
      if (filter === 'category') {
        e.preventDefault();
        if (categoryInput) categoryInput.value = link.dataset.value || '';
        if (subcategoryInput) subcategoryInput.value = '';
        const discounted = productFiltersForm.querySelector('input[name="discounted"]');
        if (discounted) discounted.checked = false;
        productFiltersForm.querySelectorAll('input[name="discounts[]"]').forEach((el) => { el.checked = false; });
        renderCategoryLinks();
        renderSubcategoryLinks();
      } else if (filter === 'subcategory') {
        e.preventDefault();
        if (subcategoryInput) subcategoryInput.value = link.dataset.value || '';
        const discounted = productFiltersForm.querySelector('input[name="discounted"]');
        if (discounted) discounted.checked = false;
        productFiltersForm.querySelectorAll('input[name="discounts[]"]').forEach((el) => { el.checked = false; });
        renderSubcategoryLinks();
      } else if (filter === 'sale' || filter === 'discounted') {
        e.preventDefault();
        if (categoryInput) categoryInput.value = '';
        if (subcategoryInput) subcategoryInput.value = '';
        const discounted = productFiltersForm.querySelector('input[name="discounted"]');
        if (discounted) discounted.checked = true;
        productFiltersForm.querySelectorAll('input[name="discounts[]"]').forEach((el) => { el.checked = false; });
        renderCategoryLinks();
        renderSubcategoryLinks();
      } else if (filter === 'discount') {
        e.preventDefault();
        if (categoryInput) categoryInput.value = '';
        if (subcategoryInput) subcategoryInput.value = '';
        const discounted = productFiltersForm.querySelector('input[name="discounted"]');
        if (discounted) discounted.checked = false;
        const d = String(link.dataset.value || '').trim();
        productFiltersForm.querySelectorAll('input[name="discounts[]"]').forEach((el) => {
          el.checked = String(el.value) === d;
        });
        renderCategoryLinks();
        renderSubcategoryLinks();
      }
    });
  }

  // Cart Drawer toggle
  const cartDrawer = document.getElementById('cartDrawer');
  const cartBackdrop = document.getElementById('cartBackdrop');
  const cartToggleBtn = document.getElementById('cartToggleBtn');
  const cartCloseBtn = document.getElementById('cartCloseBtn');
  const cartContinueBtn = document.getElementById('cartContinueBtn');
  const cartCheckoutBtn = document.getElementById('cartCheckoutBtn');

  function openCart(e) {
    if (e) e.preventDefault();
    cartDrawer?.classList.add('open');
    cartBackdrop?.classList.add('open');
    cartDrawer?.setAttribute('aria-hidden', 'false');
    cartBackdrop?.setAttribute('aria-hidden', 'false');
  }
  function closeCart(e) {
    if (e) e.preventDefault();
    cartDrawer?.classList.remove('open');
    cartBackdrop?.classList.remove('open');
    cartDrawer?.setAttribute('aria-hidden', 'true');
    cartBackdrop?.setAttribute('aria-hidden', 'true');
  }
  cartToggleBtn?.addEventListener('click', openCart);
  cartCloseBtn?.addEventListener('click', closeCart);
  cartBackdrop?.addEventListener('click', closeCart);
  cartContinueBtn?.addEventListener('click', closeCart);
  cartCheckoutBtn?.addEventListener('click', (e) => {
    e.preventDefault();
    window.location.href = '/checkout';
  });
 
  function readCart() {
    try {
      const raw = localStorage.getItem('cart');
      const arr = raw ? JSON.parse(raw) : [];
      return Array.isArray(arr) ? arr : [];
    } catch (_) { return []; }
  }
  function setCart(items) {
    localStorage.setItem('cart', JSON.stringify(items));
  }
  function addToCart(item) {
    const cart = readCart();
    const idx = cart.findIndex(x => String(x.id) === String(item.id));
    if (idx >= 0) {
      cart[idx].qty = Number(cart[idx].qty || 0) + 1;
    } else {
      cart.push({ id: String(item.id), name: item.name || '', image: item.image || '', price: Number(item.price || 0), qty: 1 });
    }
    setCart(cart);
    renderCart();
  }
  function removeFromCart(id) {
    const cart = readCart().filter(x => String(x.id) !== String(id));
    setCart(cart);
    renderCart();
  }
  function updateQty(id, delta) {
    const cart = readCart();
    const idx = cart.findIndex(x => String(x.id) === String(id));
    if (idx >= 0) {
      const next = Math.max(0, Number(cart[idx].qty || 0) + delta);
      cart[idx].qty = next;
      if (next === 0) cart.splice(idx, 1);
      setCart(cart);
      renderCart();
    }
  }
  function renderCart() {
    const drawer = document.getElementById('cartDrawer');
    if (!drawer) return;
    const body = drawer.querySelector('.cart_body');
    if (!body) return;
    const empty = body.querySelector('.cart_empty');
    let list = body.querySelector('.cart_items');
    if (!list) {
      list = document.createElement('div');
      list.className = 'cart_items';
      body.insertBefore(list, body.querySelector('.cart_divider'));
    }
    list.innerHTML = '';
    const items = readCart();
    if (!items.length) {
      if (empty) empty.style.display = 'block';
    } else {
      if (empty) empty.style.display = 'none';
    }
    items.forEach(it => {
      const row = document.createElement('div');
      row.className = 'cart_item';
      row.dataset.id = String(it.id);
      const img = document.createElement('img');
      img.className = 'cart_item_img';
      if (it.image) img.src = it.image;
      const meta = document.createElement('div');
      meta.className = 'cart_item_meta';
      const name = document.createElement('div');
      name.className = 'cart_item_name';
      name.textContent = it.name || '';
      const price = document.createElement('div');
      price.className = 'cart_item_price';
      price.textContent = '€ ' + Number(it.price || 0).toLocaleString('nl-NL');
      meta.appendChild(name);
      meta.appendChild(price);
      const qty = document.createElement('div');
      qty.className = 'cart_item_qty';
      const count = document.createElement('div');
      count.className = 'qty_count';
      count.textContent = String(it.qty || 1) + 'x';
      const controls = document.createElement('div');
      controls.className = 'qty_controls';
      const dec = document.createElement('button');
      dec.type = 'button';
      dec.className = 'cart_qty_btn';
      dec.innerHTML = '<i class=\"ri-subtract-line\"></i>';
      const inc = document.createElement('button');
      inc.type = 'button';
      inc.className = 'cart_qty_btn';
      inc.innerHTML = '<i class=\"ri-add-line\"></i>';
      const pulse = (btn) => {
        const icon = btn.querySelector('i');
        if (!icon) return;
        icon.classList.add('btn_pulse');
        setTimeout(() => icon.classList.remove('btn_pulse'), 180);
      };
      dec.addEventListener('click', () => {
        pulse(dec);
        setTimeout(() => updateQty(it.id, -1), 120);
      });
      inc.addEventListener('click', () => {
        pulse(inc);
        setTimeout(() => updateQty(it.id, 1), 120);
      });
      controls.appendChild(dec);
      controls.appendChild(inc);
      qty.appendChild(count);
      qty.appendChild(controls);
      row.appendChild(img);
      row.appendChild(meta);
      row.appendChild(qty);
      list.appendChild(row);
    });
    const sum = items.reduce((acc, x) => acc + Number(x.price || 0) * Number(x.qty || 0), 0);
    const actions = body.querySelector('.cart_actions');
    let totalEl = body.querySelector('.cart_total');
    if (!totalEl) {
      totalEl = document.createElement('div');
      totalEl.className = 'cart_total';
      if (actions) body.insertBefore(totalEl, actions);
    }
    totalEl.innerHTML = `<span>Total</span><span>€ ${sum.toLocaleString('nl-NL')}</span>`;
  }
  renderCart();

  try {
    const npGrid = document.querySelector('.new_products_grid');
    if (npGrid) {
      const styles = window.getComputedStyle(npGrid);
      const gap = parseFloat(styles.columnGap || styles.gap || '0');
      const visible = parseInt(styles.getPropertyValue('--np-visible')) || 6;
      let cards = Array.from(npGrid.querySelectorAll('.new_product_card'));
      let cardW = cards.length ? cards[0].getBoundingClientRect().width : 0;
      let maxStart = Math.max(0, cards.length - visible);
      let maxOffset = maxStart * (cardW + gap);
      let start = 0;

      function readOffset() {
        const v = npGrid.style.getPropertyValue('--np-offset') || getComputedStyle(npGrid).getPropertyValue('--np-offset') || '0';
        const n = parseFloat(v);
        return isNaN(n) ? 0 : n;
      }
      function updateSlider() {
        const offset = Math.min(maxOffset, Math.max(0, start * (cardW + gap)));
        npGrid.style.setProperty('--np-offset', `${offset}px`);
      }
      function recalc() {
        cards = Array.from(npGrid.querySelectorAll('.new_product_card'));
        cardW = cards.length ? cards[0].getBoundingClientRect().width : cardW;
        maxStart = Math.max(0, cards.length - visible);
        maxOffset = maxStart * (cardW + gap);
        start = Math.min(maxStart, Math.max(0, start));
        updateSlider();
      }
      recalc();
      window.addEventListener('resize', () => {
        recalc();
      });

      let dragging = false;
      let startX = 0;
      let offsetStart = 0;

      npGrid.addEventListener('pointerdown', (e) => {
        const onControls = !!e.target.closest('.overlay_actions');
        if (onControls) return;
        dragging = true;
        startX = e.clientX;
        offsetStart = readOffset();
        npGrid.classList.add('dragging');
        npGrid.setPointerCapture?.(e.pointerId);
      });

      window.addEventListener('pointermove', (e) => {
        if (!dragging) return;
        const dx = e.clientX - startX;
        const next = Math.min(maxOffset, Math.max(0, offsetStart - dx));
        npGrid.style.setProperty('--np-offset', `${next}px`);
      });

      function endDrag() {
        if (!dragging) return;
        dragging = false;
        npGrid.classList.remove('dragging');
        const cur = readOffset();
        start = Math.min(maxStart, Math.max(0, Math.round(cur / (cardW + gap))));
        updateSlider();
        npDragLockUntil = Date.now() + 180;
      }
      window.addEventListener('pointerup', endDrag);
      window.addEventListener('pointercancel', endDrag);
    }
  } catch (_) {}

  function animateAddToCart(sourceImg) {
    return new Promise((resolve) => {
      const icon = document.querySelector('#cartToggleBtn i');
      if (!sourceImg || !icon) { resolve(); return; }
      const s = sourceImg.getBoundingClientRect();
      const t = icon.getBoundingClientRect();
      const clone = sourceImg.cloneNode(true);
      clone.className = 'fly_clone';
      clone.style.left = s.left + 'px';
      clone.style.top = s.top + 'px';
      clone.style.width = s.width + 'px';
      clone.style.height = s.height + 'px';
      clone.style.transform = 'translate(0,0) scale(1)';
      document.body.appendChild(clone);
      const dx = (t.left + t.width / 2) - (s.left + s.width / 2);
      const dy = (t.top + t.height / 2) - (s.top + s.height / 2);
      requestAnimationFrame(() => {
        clone.style.transform = `translate(${dx}px, ${dy}px) scale(0.2)`;
        clone.style.opacity = '0.2';
      });
      const iconEl = icon;
      clone.addEventListener('transitionend', () => {
        clone.remove();
        iconEl.classList.add('cart_bounce');
        setTimeout(() => {
          iconEl.classList.remove('cart_bounce');
          resolve();
        }, 320);
      }, { once: true });
    });
  }

  document.addEventListener('click', (e) => {
    if (Date.now() < npDragLockUntil) return;
    const basket = e.target.closest('.overlay_actions .ri-shopping-basket-line');
    if (!basket) return;
    e.preventDefault();
    e.stopPropagation();
    const card = basket.closest('.new_product_card');
    if (!card) return;
    const id = card.dataset.id;
    if (!id) return;
    const item = {
      id: String(id),
      name: card.dataset.name || '',
      image: card.dataset.image || '',
      price: Number(card.dataset.price || 0),
    };
    addToCart(item);
    const imgEl = card.querySelector('.new_product_media img');
    animateAddToCart(imgEl).then(() => openCart(e));
  });

  const productAddToCartBtn = document.getElementById('productAddToCartBtn');
  if (productAddToCartBtn) {
    productAddToCartBtn.addEventListener('click', (e) => {
      e.preventDefault();
      const id = productAddToCartBtn.dataset.id;
      if (!id) return;
      const item = {
        id: String(id),
        name: productAddToCartBtn.dataset.name || '',
        image: productAddToCartBtn.dataset.image || '',
        price: Number(productAddToCartBtn.dataset.price || 0),
      };
      addToCart(item);
      const imgEl = document.querySelector('.product_show_media img');
      animateAddToCart(imgEl).then(() => openCart(e));
    });
  }

  const productWishlistToggle = document.getElementById('productWishlistToggle');

  function readAuthMeta(name) {
    return document.querySelector(`meta[name="${name}"]`)?.getAttribute('content') || '';
  }
  const authLoggedIn = readAuthMeta('auth-logged-in') === '1';
  const authUserId = readAuthMeta('auth-user-id');
  const authIsAdmin = readAuthMeta('auth-is-admin') === '1';
  const canUseWishlist = authLoggedIn && !authIsAdmin && String(authUserId || '').trim() !== '';
  const wishlistKey = canUseWishlist ? `wishlist:${String(authUserId).trim()}` : '';

  function readWishlistRaw() {
    if (!wishlistKey) return [];
    try {
      const raw = localStorage.getItem(wishlistKey);
      const arr = raw ? JSON.parse(raw) : [];
      return Array.isArray(arr) ? arr : [];
    } catch (_) { return []; }
  }
  function getWishlistIds() {
    return readWishlistRaw()
      .map(x => (typeof x === 'object' && x !== null ? x.id : x))
      .filter(Boolean)
      .map(String);
  }
  function setWishlist(items) {
    if (!wishlistKey) return;
    localStorage.setItem(wishlistKey, JSON.stringify(items));
  }
  function inWishlist(id) {
    if (!wishlistKey) return false;
    return getWishlistIds().includes(String(id));
  }

  if (canUseWishlist) {
    try {
      const legacyRaw = localStorage.getItem('wishlist');
      if (legacyRaw && !localStorage.getItem(wishlistKey)) {
        const parsed = JSON.parse(legacyRaw);
        if (Array.isArray(parsed)) {
          localStorage.setItem(wishlistKey, JSON.stringify(parsed));
          localStorage.removeItem('wishlist');
        }
      }
    } catch (_) {}
  }

  if (productWishlistToggle) {
    if (!canUseWishlist) {
      productWishlistToggle.style.display = 'none';
    } else {
    const icon = productWishlistToggle.querySelector('i');
    const id = productWishlistToggle.dataset.id;
    if (id && icon && inWishlist(id)) {
      icon.classList.remove('ri-heart-3-line');
      icon.classList.add('ri-heart-fill');
    }
    productWishlistToggle.addEventListener('click', (e) => {
      e.preventDefault();
      const pid = productWishlistToggle.dataset.id;
      if (!pid || !icon) return;
      let wl = readWishlistRaw().map(x => (typeof x === 'object' ? x : { id: String(x) }));
      const idx = wl.findIndex(x => String((x.id ?? x)) === String(pid));
      if (idx >= 0) {
        wl.splice(idx, 1);
        icon.classList.remove('ri-heart-fill');
        icon.classList.add('ri-heart-3-line');
      } else {
        wl.push({
          id: String(pid),
          name: productWishlistToggle.dataset.name || '',
          image: productWishlistToggle.dataset.image || '',
          price: Number(productWishlistToggle.dataset.price || '0'),
          category: productWishlistToggle.dataset.category || ''
        });
        icon.classList.remove('ri-heart-3-line');
        icon.classList.add('ri-heart-fill');
      }
      setWishlist(wl);
    });
    }
  }
 
  const wishlistPanel = document.getElementById('panel-wishlist');
  async function hydrateWishlist() {
    const ids = getWishlistIds();
    if (!ids.length) return [];
    try {
      const res = await fetch('/api/products', { headers: { 'Accept': 'application/json' } });
      const data = await res.json();
      const list = Array.isArray(data) ? data : (Array.isArray(data?.data) ? data.data : []);
      const map = {};
      list.forEach(p => { map[String(p.id)] = p; });
      const items = ids
        .map(id => {
          const p = map[String(id)];
          if (!p) return null;
          const img = Array.isArray(p.images) && p.images.length ? p.images[0] : (p.image || '');
          const base = Number(p.price || 0);
          const disc = Number(p.discount_percent || 0);
          const final = disc > 0 ? Math.max(0, base * (1 - disc / 100)) : base;
          return { id: String(p.id), name: p.name || '', image: img || '', price: final, category: p.category || '' };
        })
        .filter(Boolean);
      if (items.length) setWishlist(items);
      return items;
    } catch (_) {
      const raw = readWishlistRaw().filter(x => typeof x === 'object' && x && x.id);
      return raw.map(x => ({ id: String(x.id), name: x.name || '', image: x.image || '', price: Number(x.price || 0), category: x.category || '' }));
    }
  }
  document.addEventListener('click', (e) => {
    if (Date.now() < npDragLockUntil) return;
    const heart = e.target.closest('.overlay_actions .ri-heart-3-line, .overlay_actions .ri-heart-fill');
    if (!heart) return;
    if (!canUseWishlist) return;
    e.preventDefault();
    e.stopPropagation();
    const card = heart.closest('.new_product_card');
    if (!card) return;
    const id = card.dataset.id;
    if (!id) return;
    let wl = readWishlistRaw().map(x => (typeof x === 'object' ? x : { id: String(x) }));
    const idx = wl.findIndex(x => String((x.id ?? x)) === String(id));
    if (idx >= 0) {
      wl.splice(idx, 1);
      heart.classList.remove('ri-heart-fill');
      heart.classList.add('ri-heart-3-line');
    } else {
      wl.push({
        id: String(id),
        name: card.dataset.name || '',
        image: card.dataset.image || '',
        price: Number(card.dataset.price || '0'),
        category: card.dataset.category || ''
      });
      heart.classList.remove('ri-heart-3-line');
      heart.classList.add('ri-heart-fill');
    }
    setWishlist(wl);
    if (wishlistPanel) renderWishlist();
  });
  document.querySelectorAll('.new_product_card').forEach(card => {
    const id = card.dataset.id;
    const heart = card.querySelector('.overlay_actions .ri-heart-3-line, .overlay_actions .ri-heart-fill');
    if (!heart) return;
    if (!canUseWishlist) {
      heart.remove();
      return;
    }
    if (id && inWishlist(id)) {
      heart.classList.remove('ri-heart-3-line');
      heart.classList.add('ri-heart-fill');
    }
  });
  async function renderWishlist() {
    if (!wishlistPanel) return;
    wishlistPanel.innerHTML = `
      <div class="wishlist_title"><h2>Wishlist</h2></div>
      <div class="wishlist_grid" id="wishlistGrid" aria-live="polite"></div>
      <div class="wishlist_empty" id="wishlistEmpty">Nog geen favorieten</div>
    `;
    const grid = wishlistPanel.querySelector('#wishlistGrid');
    const empty = wishlistPanel.querySelector('#wishlistEmpty');
    if (!canUseWishlist) {
      if (empty) empty.style.display = 'block';
      return;
    }
    const items = await hydrateWishlist();
    if (!items.length) {
      if (empty) empty.style.display = 'block';
      return;
    }
    if (empty) empty.style.display = 'none';
    items.forEach(p => {
      const img = p.image || '';
      const final = Number(p.price || 0);
      const card = document.createElement('div');
      card.className = 'new_product_card';
      card.dataset.id = String(p.id);
      card.dataset.name = p.name || '';
      card.dataset.image = img || '';
      card.dataset.price = String(final);
      card.dataset.category = p.category || '';
      card.innerHTML = `
        <div class="new_product_media">
          ${img ? `<img src="${img}" alt="${p.name || ''}">` : ''}
          <div class="overlay_actions">
            <i class="ri-shopping-basket-line"></i>
            <i class="ri-heart-fill"></i>
          </div>
        </div>
        <div class="wish_meta">
          <div class="wish_left">
            <div class="wish_title">${p.name || ''}</div>
            <div class="wish_subtitle">${p.category || ''}</div>
          </div>
          <div class="wish_price">€ ${final.toLocaleString('nl-NL')}</div>
        </div>
      `;
      grid?.appendChild(card);
    });
  }
  renderWishlist();
});
