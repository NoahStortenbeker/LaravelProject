import gsap from "gsap";
import { groupedCategories, getSizesForCategory } from "../data/categorySizes.js";

const initAccount = () => {
  const nav = document.getElementById('accountNav');
  if (nav) {
    const indicator = nav.querySelector('.nav_indicator');
    const items = Array.from(nav.querySelectorAll('.nav_item')).filter(i => !i.classList.contains('logout'));
    const panels = {};
    Array.from(document.querySelectorAll('.panel')).forEach(p => { panels[p.id] = p; });
    function moveIndicator(el) {
      if (!indicator || !el) return;
      indicator.style.height = '40px';
      const label = el.querySelector('span');
      if (!label) return;
      const navRect = nav.getBoundingClientRect();
      const labelRect = label.getBoundingClientRect();
      const y = Math.round(labelRect.top + (labelRect.height / 2) - navRect.top - 20);
      indicator.style.transform = `translateY(${y}px)`;
    }
    function setActive(el) {
      items.forEach(i => i.classList.remove('active'));
      el.classList.add('active');
      const target = el.getAttribute('data-target');
      const next = panels[target];
      const currentPanel = Object.values(panels).find(p => p && p.classList.contains('active'));
      if (next && currentPanel !== next) {
        next.classList.add('active');
        if (currentPanel) {
          currentPanel.classList.remove('active');
        }
      }
      if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(() => requestAnimationFrame(() => moveIndicator(el)));
      } else {
        setTimeout(() => moveIndicator(el), 50);
      }
    }
    let initial = items[0];
    const qp = new URLSearchParams(window.location.search).get('panel');
    if (qp) {
      const targetId = `panel-${qp}`;
      const found = items.find(i => i.getAttribute('data-target') === targetId);
      if (found) initial = found;
    }
    if (items.length) setActive(initial);
    items.forEach(i => {
      i.addEventListener('click', (e) => {
        e.preventDefault();
        setActive(i);
      });
    });
    window.addEventListener('resize', () => {
      const active = nav.querySelector('.nav_item.active');
      if (active) moveIndicator(active);
    });
  }

  // Country dropdown (custom, with API)
  const countryDropdown = document.getElementById('countryDropdown');
  if (countryDropdown) {
    const toggle = countryDropdown.querySelector('.dropdown_toggle');
    const menu = document.getElementById('countryMenu');
    const hidden = document.getElementById('country');
    const label = countryDropdown.querySelector('.dropdown_label');
    let loaded = false;
    async function loadCountries() {
      if (loaded) return;
      try {
        const res = await fetch('/api/countries', {
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          credentials: 'include'
        });
        if (!res.ok) throw new Error('Failed to load countries');
        const list = await res.json();
        if (Array.isArray(list) && menu) {
          menu.innerHTML = '';
          list.forEach((c) => {
            const item = document.createElement('div');
            item.className = 'dropdown_item';
            item.textContent = c;
            item.addEventListener('click', () => {
              hidden.value = c;
              if (label) label.textContent = c;
              countryDropdown.classList.remove('open');
              menu.setAttribute('aria-hidden', 'true');
            });
            menu.appendChild(item);
          });
          loaded = true;
          const curr = (hidden?.value || '').trim();
          if (curr && label) label.textContent = curr;
        }
      } catch (e) {
        console.error(e);
      }
    }
    function openDropdown() {
      countryDropdown.classList.add('open');
      menu?.setAttribute('aria-hidden', 'false');
    }
    function closeDropdown() {
      countryDropdown.classList.remove('open');
      menu?.setAttribute('aria-hidden', 'true');
    }
    toggle?.addEventListener('click', async (e) => {
      e.preventDefault();
      if (countryDropdown.classList.contains('open')) {
        closeDropdown();
      } else {
        await loadCountries();
        openDropdown();
      }
    });
    document.addEventListener('click', (e) => {
      if (!countryDropdown.contains(e.target)) closeDropdown();
    });
  }

  // Datepicker
  const dobWrapper = document.getElementById('dobWrapper');
  if (dobWrapper) {
    const input = dobWrapper.querySelector('#date_of_birth');
    const toggle = dobWrapper.querySelector('#dobToggle');
    const dropdown = dobWrapper.querySelector('#dobPicker');
    const grid = dropdown?.querySelector('.datepicker_grid');
    const label = dropdown?.querySelector('.month_label');
    const prev = dropdown?.querySelector('.prev');
    const next = dropdown?.querySelector('.next');
    const yearDropdown = dropdown?.querySelector('.year_dropdown');
    const yearGrid = yearDropdown?.querySelector('.year_grid');
    const yearRange = yearDropdown?.querySelector('.year_range');
    const yearPrev = yearDropdown?.querySelector('.year-prev');
    const yearNext = yearDropdown?.querySelector('.year-next');

    let current = new Date();
    current.setHours(0, 0, 0, 0);
    let yearStart = current.getFullYear() - (current.getFullYear() % 12);

    function fmt(d) {
      const y = d.getFullYear();
      const m = String(d.getMonth() + 1).padStart(2, '0');
      const day = String(d.getDate()).padStart(2, '0');
      return `${y}-${m}-${day}`;
    }
    function render() {
      if (!grid || !label) return;
      grid.innerHTML = '';
      const y = current.getFullYear();
      const m = current.getMonth();
      label.textContent = new Date(y, m, 1).toLocaleString(undefined, { month: 'long', year: 'numeric' });
      const first = new Date(y, m, 1);
      const startDay = first.getDay(); // 0-6
      const daysInMonth = new Date(y, m + 1, 0).getDate();
      // pad blanks
      for (let i = 0; i < startDay; i++) {
        const pad = document.createElement('div');
        pad.className = 'datepicker_day disabled';
        grid.appendChild(pad);
      }
      const today = new Date(); today.setHours(0, 0, 0, 0);
      for (let d = 1; d <= daysInMonth; d++) {
        const cell = document.createElement('div');
        cell.className = 'datepicker_day';
        cell.textContent = String(d);
        const cellDate = new Date(y, m, d);
        if (fmt(cellDate) === fmt(today)) cell.classList.add('today');
        cell.addEventListener('click', () => {
          input.value = fmt(cellDate);
          dropdown.classList.remove('open');
          dropdown.setAttribute('aria-hidden', 'true');
          toggle?.classList.remove('ri-arrow-up-s-line');
          toggle?.classList.add('ri-arrow-down-s-line');
        });
        grid.appendChild(cell);
      }
    }
    function renderYears() {
      if (!yearGrid || !yearRange) return;
      yearGrid.innerHTML = '';
      yearRange.textContent = `${yearStart}–${yearStart + 11}`;
      for (let y = yearStart; y <= yearStart + 11; y++) {
        const cell = document.createElement('div');
        cell.className = 'year_cell';
        cell.textContent = String(y);
        if (y === current.getFullYear()) cell.classList.add('selected');
        cell.addEventListener('click', () => {
          current.setFullYear(y);
          dropdown.classList.remove('mode-year');
          render();
        });
        yearGrid.appendChild(cell);
      }
    }
    function open() {
      dropdown.classList.add('open');
      dropdown.setAttribute('aria-hidden', 'false');
      toggle?.classList.remove('ri-arrow-down-s-line');
      toggle?.classList.add('ri-arrow-up-s-line');
      dropdown.classList.remove('mode-year');
      render();
    }
    function close() {
      dropdown.classList.remove('open');
      dropdown.setAttribute('aria-hidden', 'true');
      toggle?.classList.remove('ri-arrow-up-s-line');
      toggle?.classList.add('ri-arrow-down-s-line');
      dropdown.classList.remove('mode-year');
    }
    toggle?.addEventListener('click', (e) => {
      e.preventDefault();
      dropdown.classList.contains('open') ? close() : open();
    });
    input?.addEventListener('click', open);
    prev?.addEventListener('click', (e) => {
      e.preventDefault();
      current.setMonth(current.getMonth() - 1);
      render();
    });
    next?.addEventListener('click', (e) => {
      e.preventDefault();
      current.setMonth(current.getMonth() + 1);
      render();
    });
    label?.addEventListener('click', () => {
      if (!dropdown.classList.contains('open')) open();
      if (dropdown.classList.contains('mode-year')) {
        dropdown.classList.remove('mode-year');
      } else {
        yearStart = current.getFullYear() - (current.getFullYear() % 12);
        dropdown.classList.add('mode-year');
        renderYears();
      }
    });
    yearPrev?.addEventListener('click', (e) => {
      e.preventDefault();
      yearStart -= 12;
      renderYears();
    });
    yearNext?.addEventListener('click', (e) => {
      e.preventDefault();
      yearStart += 12;
      renderYears();
    });
    document.addEventListener('click', (e) => {
      if (!dobWrapper.contains(e.target)) close();
    });
  }

  const avatarClickable = document.getElementById('avatarClickable');
  const photoInput = document.getElementById('photoInput');
  if (avatarClickable && photoInput) {
    const uploadUrl = photoInput.getAttribute('data-photo-url');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const cropOverlay = document.getElementById('cropOverlay');
    const cropImage = document.getElementById('cropImage');
    const cropZoom = document.getElementById('cropZoom');
    const cropCancel = document.querySelector('.crop_cancel');
    const cropSave = document.querySelector('.crop_save');
    const cropViewport = document.querySelector('.crop_viewport');
    const VP_W = 320, VP_H = 320;
    let imgW = 0, imgH = 0;
    let baseScale = 1, zoom = 1, scale = 1;
    let offsetX = 0, offsetY = 0;
    let isDragging = false, startX = 0, startY = 0;
    function clampOffsets() {
      const dispW = imgW * scale;
      const dispH = imgH * scale;
      const maxX = Math.max(0, dispW / 2 - VP_W / 2);
      const maxY = Math.max(0, dispH / 2 - VP_H / 2);
      if (offsetX > maxX) offsetX = maxX;
      if (offsetX < -maxX) offsetX = -maxX;
      if (offsetY > maxY) offsetY = maxY;
      if (offsetY < -maxY) offsetY = -maxY;
    }
    function applyTransform() {
      cropImage.style.transform = `translate(-50%, -50%) translate(${offsetX}px, ${offsetY}px) scale(${scale})`;
    }
    function openCrop(file) {
      const url = URL.createObjectURL(file);
      cropImage.onload = () => {
        imgW = cropImage.naturalWidth;
        imgH = cropImage.naturalHeight;
        baseScale = Math.max(VP_W / imgW, VP_H / imgH);
        zoom = 1;
        scale = baseScale * zoom;
        offsetX = 0; offsetY = 0;
        clampOffsets();
        applyTransform();
        cropOverlay.classList.add('open');
        cropOverlay.setAttribute('aria-hidden', 'false');
        URL.revokeObjectURL(url);
      };
      cropImage.src = url;
    }
    function closeCrop() {
      cropOverlay.classList.remove('open');
      cropOverlay.setAttribute('aria-hidden', 'true');
    }
    function uploadBlob(blob) {
      const fd = new FormData();
      fd.append('photo', blob, 'avatar.jpg');
      return fetch(uploadUrl, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'include',
        body: fd
      }).then(res => {
        if (!res.ok) throw new Error('Upload failed');
        return res.json();
      }).then(data => {
        const avatar = document.getElementById('avatarClickable');
        if (avatar) {
          let img = avatar.querySelector('img');
          if (!img) {
            img = document.createElement('img');
            // keep overlay; do not clear innerHTML
            avatar.insertBefore(img, avatar.firstChild);
          }
          if (data.photo_url) img.src = data.photo_url;
          const icon = avatar.querySelector('i.ri-user-3-fill');
          if (icon) icon.remove();
        }
        const headerAvatar = document.querySelector('.account_summary .avatar_60');
        if (headerAvatar) {
          let hImg = headerAvatar.querySelector('img');
          if (!hImg) {
            hImg = document.createElement('img');
            headerAvatar.insertBefore(hImg, headerAvatar.firstChild);
          }
          if (data.photo_url) hImg.src = data.photo_url;
          const hIcon = headerAvatar.querySelector('i.ri-user-3-fill');
          if (hIcon) hIcon.remove();
        }
        typeof openStatusToast === 'function' ? openStatusToast() : null;
      }).catch(err => console.error(err));
    }
    avatarClickable.addEventListener('click', () => {
      photoInput.click();
    });
    photoInput.addEventListener('change', () => {
      if (!(photoInput.files && photoInput.files.length > 0) || !uploadUrl) return;
      openCrop(photoInput.files[0]);
    });
    cropZoom?.addEventListener('input', () => {
      zoom = Number(cropZoom.value);
      scale = baseScale * zoom;
      clampOffsets();
      applyTransform();
    });
    cropViewport?.addEventListener('mousedown', (e) => {
      isDragging = true; startX = e.clientX; startY = e.clientY; cropViewport.style.cursor = 'grabbing';
      e.preventDefault();
    });
    window.addEventListener('mousemove', (e) => {
      if (!isDragging) return;
      offsetX += e.clientX - startX;
      offsetY += e.clientY - startY;
      startX = e.clientX; startY = e.clientY;
      clampOffsets();
      applyTransform();
    });
    window.addEventListener('mouseup', () => {
      if (isDragging) { isDragging = false; cropViewport.style.cursor = 'grab'; }
    });
    cropCancel?.addEventListener('click', () => {
      closeCrop();
      photoInput.value = '';
    });
    cropSave?.addEventListener('click', async () => {
      // Render to canvas using same transform
      const outSize = 512;
      const canvas = document.createElement('canvas');
      canvas.width = outSize; canvas.height = outSize;
      const ctx = canvas.getContext('2d');
      if (!ctx) return;
      const scaleRatio = (outSize / VP_W);
      ctx.translate(outSize / 2 + offsetX * scaleRatio, outSize / 2 + offsetY * scaleRatio);
      ctx.scale(scale * scaleRatio, scale * scaleRatio);
      ctx.drawImage(cropImage, -imgW / 2, -imgH / 2);
      canvas.toBlob(async (blob) => {
        if (!blob) return;
        try {
          await uploadBlob(blob);
        } finally {
          closeCrop();
          photoInput.value = '';
        }
      }, 'image/jpeg', 0.92);
    });
  }

  const statusFlag = document.getElementById('statusUpdatedFlag');
  const statusToast = document.getElementById('statusToast');
  const statusToastClose = statusToast?.querySelector('.toast_close');
  let statusToastTimer;
  function openStatusToast() {
    if (!statusToast) return;
    clearTimeout(statusToastTimer);
    statusToast.classList.add('open');
    statusToast.setAttribute('aria-hidden', 'false');
    statusToastTimer = setTimeout(() => {
      closeStatusToast();
    }, 3000);
  }
  function closeStatusToast() {
    if (!statusToast) return;
    statusToast.classList.remove('open');
    statusToast.setAttribute('aria-hidden', 'true');
    clearTimeout(statusToastTimer);
  }
  statusToastClose?.addEventListener('click', closeStatusToast);
  if (statusFlag) {
    setTimeout(openStatusToast, 50);
  }
  // Allow account and address forms to submit directly without JS interference

  const cancelOrderModal = document.getElementById('cancelOrderModal');
  if (cancelOrderModal) {
    const box = cancelOrderModal.querySelector('.modal_box');
    const closeBtn = document.getElementById('cancelOrderClose');
    const text = document.getElementById('cancelOrderText');
    const form = document.getElementById('cancelOrderForm');
    const confirmBtn = document.getElementById('cancelOrderConfirm');
    let lastFocused = null;

    function open(action, orderId) {
      if (!form) return;
      lastFocused = document.activeElement;
      form.setAttribute('action', action);
      if (text) text.textContent = `Are you sure you want to cancel order #${orderId}?`;
      cancelOrderModal.classList.add('open');
      cancelOrderModal.setAttribute('aria-hidden', 'false');
      setTimeout(() => closeBtn?.focus(), 0);
    }

    function close() {
      cancelOrderModal.classList.remove('open');
      cancelOrderModal.setAttribute('aria-hidden', 'true');
      if (confirmBtn) confirmBtn.disabled = false;
      if (lastFocused && typeof lastFocused.focus === 'function') lastFocused.focus();
    }

    document.addEventListener('click', (e) => {
      const btn = e.target.closest('.order_cancel_btn');
      if (btn && btn.getAttribute('data-cancel-action')) {
        e.preventDefault();
        open(btn.getAttribute('data-cancel-action'), btn.getAttribute('data-order-id') || '');
        return;
      }

      if (e.target === cancelOrderModal) {
        close();
      }
    });

    closeBtn?.addEventListener('click', (e) => {
      e.preventDefault();
      close();
    });

    form?.addEventListener('submit', () => {
      if (confirmBtn) confirmBtn.disabled = true;
    });

    document.addEventListener('keydown', (e) => {
      if (cancelOrderModal.getAttribute('aria-hidden') === 'true') return;
      if (e.key === 'Escape') {
        e.preventDefault();
        close();
      }
      if (e.key === 'Tab' && box) {
        const focusable = Array.from(box.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex=\"-1\"])'))
          .filter(el => !el.hasAttribute('disabled'));
        if (!focusable.length) return;
        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        const active = document.activeElement;
        if (e.shiftKey) {
          if (active === first || active === box) {
            e.preventDefault();
            last.focus();
          }
        } else {
          if (active === last) {
            e.preventDefault();
            first.focus();
          }
        }
      }
    });
  }

  const adminList = document.querySelector('.admin_users_list');
  if (adminList) {
    const rows = Array.from(adminList.querySelectorAll('.user_row'));
    const searchInput = document.getElementById('adminSearchInput');
    const searchBtn = document.getElementById('adminSearchBtn');
    const pagesContainer = document.getElementById('adminPages');
    const PAGE_SIZE = 5;
    const GROUP_SIZE = 5;
    let currentPage = 1;
    rows.forEach(row => {
      const cell = row.querySelector('.user_username');
      if (cell && !cell.dataset.original) cell.dataset.original = cell.textContent;
    });
    function sortRows() {
      const visibleRows = rows.filter(r => r.style.display !== 'none');
      visibleRows.sort((a, b) => {
        const uA = (a.dataset.username || '').toLowerCase();
        const uB = (b.dataset.username || '').toLowerCase();
        return uA.localeCompare(uB);
      });
      visibleRows.forEach(r => adminList.appendChild(r));
    }
    function escapeHtml(s) {
      return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }
    function paginate() {
      const matching = rows.filter(r => r.dataset.match !== '0');
      const pages = Math.max(1, Math.ceil(matching.length / PAGE_SIZE));
      if (currentPage > pages) currentPage = pages;
      matching.forEach((row, idx) => {
        row.style.display = (idx >= (currentPage - 1) * PAGE_SIZE && idx < currentPage * PAGE_SIZE) ? '' : 'none';
      });
      renderPageButtons();
    }
    function renderPageButtons() {
      if (!pagesContainer) return;
      const matching = rows.filter(r => r.dataset.match !== '0');
      const pages = Math.max(1, Math.ceil(matching.length / PAGE_SIZE));
      pagesContainer.innerHTML = '';
      pagesContainer.style.visibility = pages > 1 ? 'visible' : 'hidden';
      const groupStart = Math.floor((currentPage - 1) / GROUP_SIZE) * GROUP_SIZE + 1;
      const groupEnd = Math.min(groupStart + GROUP_SIZE - 1, pages);
      if (groupStart > 1) {
        const prevEll = document.createElement('button');
        prevEll.type = 'button';
        prevEll.className = 'pg_ellipsis';
        prevEll.textContent = '...';
        prevEll.addEventListener('click', () => {
          currentPage = Math.max(1, groupStart - 1);
          paginate();
        });
        pagesContainer.appendChild(prevEll);
      }
      for (let i = groupStart; i <= groupEnd; i++) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'pg_number' + (i === currentPage ? ' active' : '');
        btn.textContent = String(i);
        btn.addEventListener('click', () => {
          currentPage = i;
          paginate();
        });
        pagesContainer.appendChild(btn);
      }
      if (groupEnd < pages) {
        const nextEll = document.createElement('button');
        nextEll.type = 'button';
        nextEll.className = 'pg_ellipsis';
        nextEll.textContent = '...';
        nextEll.addEventListener('click', () => {
          currentPage = Math.min(pages, groupEnd + 1);
          paginate();
        });
        pagesContainer.appendChild(nextEll);
      }
    }
    function applyFilters() {
      const term = (searchInput?.value || '').trim().toLowerCase();
      rows.forEach(row => {
        const username = (row.dataset.username || '').toLowerCase();
        const match = !term || username.startsWith(term);
        row.dataset.match = match ? '1' : '0';
        row.style.display = match ? '' : 'none';
        const cell = row.querySelector('.user_username');
        if (cell) {
          const original = cell.dataset.original || cell.textContent;
          if (term && match) {
            const before = original.slice(0, term.length);
            const after = original.slice(term.length);
            cell.innerHTML = '<span class=\"search_highlight\">' + escapeHtml(before) + '</span>' + escapeHtml(after);
          } else {
            cell.textContent = original;
          }
        }
      });
      sortRows();
      currentPage = 1;
      paginate();
    }
    searchBtn?.addEventListener('click', (e) => {
      e.preventDefault();
      const nowOpen = searchBtn.classList.toggle('open');
      if (nowOpen) {
        requestAnimationFrame(() => searchInput?.focus());
      } else {
        if (searchInput) {
          searchInput.value = '';
          applyFilters();
        }
      }
    });
    searchInput?.addEventListener('input', applyFilters);
    applyFilters();
  }

  const vacancyForm = document.getElementById('vacancyForm');
  if (vacancyForm) {
    const imageInput = document.getElementById('vacancyImages');
    const imageBtn = document.getElementById('vacancyImageBtn');
    const preview = document.getElementById('vacancyPreview');
    const maxImages = Number(imageInput?.getAttribute('data-max') || 3);
    const selected = [];
    const createBtn = document.getElementById('vacancyCreateBtn');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    function renderPreview() {
      if (!preview) return;
      preview.innerHTML = '';
      selected.forEach((item, idx) => {
        const thumb = document.createElement('div');
        thumb.className = 'vacancy_thumb';
        const img = document.createElement('img');
        img.src = item.url;
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'vacancy_remove';
        btn.textContent = '×';
        btn.addEventListener('click', () => {
          URL.revokeObjectURL(item.url);
          selected.splice(idx, 1);
          renderPreview();
        });
        thumb.appendChild(img);
        thumb.appendChild(btn);
        preview.appendChild(thumb);
      });
    }
    imageBtn?.addEventListener('click', () => imageInput?.click());
    imageInput?.addEventListener('change', () => {
      const files = Array.from(imageInput.files || []);
      for (const f of files) {
        if (selected.length >= maxImages) break;
        const url = URL.createObjectURL(f);
        selected.push({ file: f, url });
      }
      imageInput.value = '';
      renderPreview();
    });
    createBtn?.addEventListener('click', async () => {
      const fd = new FormData();
      const title = document.getElementById('vacancyTitle')?.value || '';
      const subtitle = document.getElementById('vacancySubtitle')?.value || '';
      const hours = document.getElementById('vacancyHours')?.value || '';
      const location = document.getElementById('vacancyLocation')?.value || '';
      const employment = document.getElementById('vacancyEmployment')?.value || '';
      const description = document.getElementById('vacancyDescription')?.value || '';
      fd.append('title', title);
      if (subtitle) fd.append('subtitle', subtitle);
      if (hours) fd.append('hours', hours);
      if (location) fd.append('location', location);
      if (employment) fd.append('employment_type', employment);
      if (description) fd.append('description', description);
      selected.forEach((item) => fd.append('images[]', item.file));
      try {
        const res = await fetch('/admin/vacancies', {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          credentials: 'include',
          body: fd
        });
        if (!res.ok) {
          const err = await res.json().catch(() => ({}));
          throw new Error(err.message || 'Failed to create vacancy');
        }
        const data = await res.json();
        vacancyForm.reset();
        selected.splice(0, selected.length);
        renderPreview();
        const textEl = document.querySelector('#statusToast .toast_text');
        if (textEl) textEl.textContent = 'Vacancy created';
        if (typeof openStatusToast === 'function') openStatusToast();
        console.log('Created vacancy', data);
      } catch (e) {
        console.error(e);
      }
    });
  }

  const productForm = document.getElementById('productForm');
  if (productForm) {
    const imageInput = document.getElementById('productImages');
    const imageBtn = document.getElementById('productImageBtn');
    const preview = document.getElementById('productPreview');
    const categoryDropdown = document.getElementById('productCategoryDropdown');
    const categoryMenu = document.getElementById('productCategoryMenu');
    const categoryHidden = document.getElementById('productCategory');
    const categoryGroupHidden = document.getElementById('productCategoryGroup');
    const categoryKeyHidden = document.getElementById('productCategoryKey');
    const subcategoryKeyHidden = document.getElementById('productSubcategoryKey');
    const sizesBtn = document.getElementById('sizesBtn');
    const sizesOverlay = document.getElementById('sizesOverlay');
    const sizesGroups = document.getElementById('sizesGroups');
    const sizesSaveBtn = document.getElementById('sizesSaveBtn');
    const sizesCancelBtn = document.querySelector('.sizes_cancel');
    let productSizes = [];
    const createBtn = document.getElementById('productCreateBtn');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const selectedImages = [];

    function renderImages() {
      if (!preview) return;
      preview.innerHTML = '';
      selectedImages.forEach((item, idx) => {
        const thumb = document.createElement('div');
        thumb.className = 'vacancy_thumb';
        const img = document.createElement('img');
        img.src = item.url;
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'vacancy_remove';
        btn.textContent = '×';
        btn.addEventListener('click', () => {
          URL.revokeObjectURL(item.url);
          selectedImages.splice(idx, 1);
          renderImages();
        });
        thumb.appendChild(img);
        thumb.appendChild(btn);
        preview.appendChild(thumb);
      });
    }
    imageBtn?.addEventListener('click', () => imageInput?.click());
    imageInput?.addEventListener('change', () => {
      const max = Number(imageInput?.getAttribute('data-max') || 6);
      const files = Array.from(imageInput.files || []);
      for (const f of files) {
        if (selectedImages.length >= max) break;
        const url = URL.createObjectURL(f);
        selectedImages.push({ file: f, url });
      }
      imageInput.value = '';
      renderImages();
    });

    function openDropdown(dd) {
      dd.classList.toggle('open');
      const menu = dd.querySelector('.dropdown_menu');
      if (menu) menu.setAttribute('aria-hidden', dd.classList.contains('open') ? 'false' : 'true');
    }
    categoryDropdown?.querySelector('.dropdown_toggle')?.addEventListener('click', () => openDropdown(categoryDropdown));

    function buildCategories() {
      categoryMenu.innerHTML = '';
      groupedCategories.forEach(group => {
        const title = document.createElement('div');
        title.className = 'dd_group_title';
        title.textContent = group.group;
        categoryMenu.appendChild(title);
        group.items.forEach(item => {
          const top = document.createElement('div');
          top.className = 'dropdown_item dd_item';
          top.textContent = item.name;
          top.addEventListener('click', () => {
            categoryHidden.value = item.name;
            categoryDropdown.dataset.group = group.group;
            if (categoryGroupHidden) categoryGroupHidden.value = String(group.group || '').toLowerCase();
            if (categoryKeyHidden) categoryKeyHidden.value = item.name;
            if (subcategoryKeyHidden) subcategoryKeyHidden.value = '';
            const label = categoryDropdown.querySelector('.dropdown_label');
            if (label) label.textContent = item.name;
            categoryDropdown.classList.remove('open');
            categoryMenu.setAttribute('aria-hidden','true');
          });
          categoryMenu.appendChild(top);
          item.subs.forEach(sub => {
            const subEl = document.createElement('div');
            subEl.className = 'dropdown_item dropdown_sub';
            subEl.textContent = sub.name;
            subEl.addEventListener('click', () => {
              categoryHidden.value = sub.name;
              categoryDropdown.dataset.group = group.group;
              if (categoryGroupHidden) categoryGroupHidden.value = String(group.group || '').toLowerCase();
              if (categoryKeyHidden) categoryKeyHidden.value = item.name;
              const isAll = /^all\s+/i.test(String(sub.name || '').trim());
              if (subcategoryKeyHidden) subcategoryKeyHidden.value = isAll ? '' : String(sub.name || '');
              const label = categoryDropdown.querySelector('.dropdown_label');
              if (label) label.textContent = sub.name;
              categoryDropdown.classList.remove('open');
              categoryMenu.setAttribute('aria-hidden','true');
            });
            categoryMenu.appendChild(subEl);
          });
        });
      });
    }
    buildCategories();

    const apparelSizes = ['XXS','XS','S','M','L','XL','XXL'];
    const pantsSizes = Array.from({length: 15}, (_,i)=>String(26+i));
    const shoeSizesEU = Array.from({length: 13}, (_,i)=>String(36+i));
    const kidsSizes = ['2Y','3Y','4Y','5Y','6Y','7Y','8Y','10Y','12Y','14Y'];
    const oneSize = ['ONE SIZE'];
    const sizeOptions = [
      { label: 'Apparel', type: 'apparel', values: apparelSizes },
      { label: 'Pants', type: 'pants', values: pantsSizes },
      { label: 'Shoes (EU)', type: 'shoes_eu', values: shoeSizesEU },
      { label: 'Kids', type: 'kids', values: kidsSizes },
      { label: 'Other', type: 'other', values: oneSize },
    ];
    function buildSizesModal() {
      if (!sizesGroups) return;
      sizesGroups.innerHTML = '';
      const groups = getSizesForCategory(categoryHidden.value || '', categoryDropdown?.dataset.group || '');
      groups.forEach(group => {
        const g = document.createElement('div');
        g.className = 'sizes_group';
        const h = document.createElement('h4');
        h.textContent = group.label;
        g.appendChild(h);
        group.values.forEach(v => {
          const item = document.createElement('div');
          item.className = 'size_item';
          const check = document.createElement('input');
          check.type = 'checkbox';
          check.className = 'size_check';
          const label = document.createElement('label');
          label.textContent = v;
          const qty = document.createElement('div');
          qty.className = 'size_qty';
          const dec = document.createElement('button');
          dec.type = 'button';
          dec.className = 'qty_btn';
          dec.textContent = '-';
          const input = document.createElement('input');
          input.type = 'number';
          input.min = '0';
          const inc = document.createElement('button');
          inc.type = 'button';
          inc.className = 'qty_btn';
          inc.textContent = '+';
          const found = productSizes.find(s => s.size_label === v && s.size_type === group.type);
          input.value = String(found?.amount ?? 0);
          check.checked = (found?.amount ?? 0) > 0;
          input.dataset.label = v;
          input.dataset.type = group.type;
          const sync = () => {
            const val = Math.max(0, Number(input.value || 0));
            input.value = String(val);
            check.checked = val > 0;
            item.classList.toggle('checked', check.checked);
          };
          input.addEventListener('input', sync);
          dec.addEventListener('click', () => {
            const val = Math.max(0, Number(input.value || 0) - 1);
            input.value = String(val);
            sync();
          });
          inc.addEventListener('click', () => {
            const val = Number(input.value || 0) + 1;
            input.value = String(val);
            sync();
          });
          check.addEventListener('change', () => {
            if (!check.checked) {
              input.value = '0';
            } else if (Number(input.value || 0) === 0) {
              input.value = '1';
            }
            item.classList.toggle('checked', check.checked);
          });
          if (check.checked) item.classList.add('checked');
          qty.appendChild(dec);
          qty.appendChild(input);
          qty.appendChild(inc);
          item.appendChild(check);
          item.appendChild(label);
          item.appendChild(qty);
          g.appendChild(item);
        });
        sizesGroups.appendChild(g);
      });
    }
    function openSizes() {
      if (!categoryHidden.value) {
        const textEl = document.querySelector('#statusToast .toast_text');
        if (textEl) textEl.textContent = 'Selecteer eerst een categorie';
        if (typeof openStatusToast === 'function') openStatusToast();
        return;
      }
      buildSizesModal();
      sizesOverlay?.classList.add('open');
      sizesOverlay?.setAttribute('aria-hidden','false');
    }
    function closeSizes() {
      sizesOverlay?.classList.remove('open');
      sizesOverlay?.setAttribute('aria-hidden','true');
    }
    sizesBtn?.addEventListener('click', openSizes);
    sizesCancelBtn?.addEventListener('click', closeSizes);
    sizesSaveBtn?.addEventListener('click', () => {
      const items = Array.from(sizesGroups?.querySelectorAll('.size_item') || []);
      const next = [];
      items.forEach(item => {
        const check = item.querySelector('.size_check');
        const inp = item.querySelector('input[type="number"]');
        const amt = Number(inp?.value || 0);
        const label = inp?.dataset.label || '';
        const type = inp?.dataset.type || '';
        if ((check?.checked ?? false) && amt > 0) {
          next.push({ size_label: label, size_type: type, amount: amt });
        }
      });
      productSizes = next;
      closeSizes();
    });

    // Modal replaces old size rows; no inline rows in form

    createBtn?.addEventListener('click', async () => {
      const fd = new FormData();
      const category = categoryHidden.value || '';
      const name = document.getElementById('productName')?.value || '';
      const sku = document.getElementById('productSku')?.value || '';
      const price = document.getElementById('productPrice')?.value || '';
      const description = document.getElementById('productDescription')?.value || '';
      fd.append('category', category);
      if (categoryGroupHidden?.value) fd.append('taxonomy_group', categoryGroupHidden.value);
      if (categoryKeyHidden?.value) fd.append('taxonomy_category', categoryKeyHidden.value);
      if (subcategoryKeyHidden?.value) fd.append('taxonomy_subcategory', subcategoryKeyHidden.value);
      fd.append('name', name);
      if (sku) fd.append('sku', sku);
      if (price) fd.append('price', price);
      if (description) fd.append('description', description);
      selectedImages.forEach(it => fd.append('images[]', it.file));
      productSizes.forEach((s, idx) => {
        fd.append(`sizes[${idx}][size_label]`, s.size_label);
        if (s.size_type) fd.append(`sizes[${idx}][size_type]`, s.size_type);
        fd.append(`sizes[${idx}][amount]`, String(s.amount));
      });
      try {
        const res = await fetch('/admin/products', {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          credentials: 'include',
          body: fd
        });
        if (!res.ok) {
          const err = await res.json().catch(() => ({}));
          throw new Error(err.message || 'Failed to create product');
        }
        const data = await res.json();
        productForm.reset();
        selectedImages.splice(0, selectedImages.length);
        preview.innerHTML = '';
        productSizes = [];
        closeSizes();
        sizesGroups.innerHTML = '';
        const label = categoryDropdown.querySelector('.dropdown_label');
        if (label) label.textContent = 'Select a category';
        const textEl = document.querySelector('#statusToast .toast_text');
        if (textEl) textEl.textContent = 'Product created';
        if (typeof openStatusToast === 'function') openStatusToast();
        console.log('Created product', data);
      } catch (e) {
        console.error(e);
      }
    });
  }
  const logoutBtn = document.querySelector('.logout_btn');
  const logoutText = logoutBtn?.querySelector('.logout_btn_text');
  const isAdminPage = !!document.querySelector('.admin_wrapper');
  const isUserPage = !!document.querySelector('.account_wrapper');

  // Product Overview
  const productOverviewList = document.getElementById('productOverviewList');
  if (productOverviewList) {
    const productPagination = document.getElementById('productPagination');
    const productOverviewSaveBtn = document.getElementById('productOverviewSaveBtn');
    const productOverviewDeleteBtn = document.getElementById('productOverviewDeleteBtn');
    const productOverviewFooter = document.getElementById('productOverviewFooter');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const PAGE_SIZE = 5;
    let products = [];
    let currentPage = 1;
    let selectedProduct = null;
    function updateFooterControls() {
      if (productOverviewDeleteBtn) productOverviewDeleteBtn.disabled = !selectedProduct;
    }
    function euro(n) {
      const v = Number(n ?? 0);
      return '€ ' + (Math.round(v) === v ? v.toString() : v.toFixed(2));
    }
    function buildCard(p) {
      const totalQty = Array.isArray(p.sizes) ? p.sizes.reduce((s, r) => s + Number(r.amount || 0), 0) : 0;
      const card = document.createElement('div');
      card.className = 'po_card';

      // Image
      const imgWrap = document.createElement('div');
      imgWrap.className = 'po_image';
      const img = document.createElement('img');
      img.src = (Array.isArray(p.images) && p.images.length ? p.images[0] : '');
      img.alt = p.name || p.category || 'product';
      imgWrap.appendChild(img);
      card.appendChild(imgWrap);

      // Category
      const cat = document.createElement('div');
      cat.className = 'po_text';
      cat.textContent = p.category || '';
      card.appendChild(cat);

      // SKU
      const sku = document.createElement('div');
      sku.className = 'po_text';
      sku.textContent = p.sku || '';
      card.appendChild(sku);

      // Price with original + discounted
      const basePrice = Number(p.price || 0);
      const price = document.createElement('div');
      price.className = 'po_price no_discount';
      const priceOrig = document.createElement('span');
      priceOrig.className = 'po_price_original';
      priceOrig.textContent = euro(basePrice);
      const priceDisc = document.createElement('span');
      priceDisc.className = 'po_price_discounted';
      priceDisc.style.display = 'none';
      price.appendChild(priceOrig);
      price.appendChild(priceDisc);
      card.appendChild(price);

      // Discount custom dropdown
      const ddDiscount = document.createElement('div');
      ddDiscount.className = 'dropdown dropdown_small dropdown_discount';
      const ddDiscToggle = document.createElement('button');
      ddDiscToggle.type = 'button';
      ddDiscToggle.className = 'dropdown_toggle';
      const ddDiscLabel = document.createElement('span');
      ddDiscLabel.className = 'dropdown_label';
      ddDiscLabel.textContent = 'No Discount';
      const ddDiscIcon = document.createElement('i');
      ddDiscIcon.className = 'ri-arrow-down-s-line';
      ddDiscToggle.appendChild(ddDiscLabel);
      ddDiscToggle.appendChild(ddDiscIcon);
      const ddDiscMenu = document.createElement('div');
      ddDiscMenu.className = 'dropdown_menu_small';
      function applyDiscountText(text, select = false) {
        ddDiscLabel.textContent = text;
        ddDiscount.classList.remove('open');
        ddDiscMenu.setAttribute('aria-hidden', 'true');
        ddDiscIcon.classList.remove('ri-arrow-up-s-line');
        ddDiscIcon.classList.add('ri-arrow-down-s-line');
        const m = text.match(/(\d+)%/);
        const perc = m ? Number(m[1]) : 0;
        card.dataset.discount = String(perc);
        if (select) {
          selectedProduct = Number(p.id);
          Array.from(productOverviewList.children).forEach(el => el.classList.remove('selected'));
          card.classList.add('selected');
          updateFooterControls();
        }
        if (perc > 0) {
          const discounted = Math.max(0, basePrice * (1 - perc / 100));
          price.classList.remove('no_discount');
          price.classList.add('discount_on');
          card.classList.add('discount_on');
          priceOrig.textContent = euro(basePrice);
          priceDisc.textContent = euro(discounted);
          priceDisc.style.display = '';
        } else {
          price.classList.remove('discount_on');
          price.classList.add('no_discount');
          card.classList.remove('discount_on');
          priceOrig.textContent = euro(basePrice);
          priceDisc.style.display = 'none';
        }
      }
      ['No Discount','20% Discount','40% Discount','50% Discount','70% Discount'].forEach(text => {
        const item = document.createElement('div');
        item.className = 'dropdown_item';
        const left = document.createElement('span');
        left.textContent = text;
        item.appendChild(left);
        item.addEventListener('click', () => applyDiscountText(text, true));
        ddDiscMenu.appendChild(item);
      });
      ddDiscToggle.addEventListener('click', (e) => {
        e.preventDefault();
        const nowOpen = ddDiscount.classList.toggle('open');
        ddDiscMenu.setAttribute('aria-hidden', nowOpen ? 'false' : 'true');
        ddDiscIcon.classList.toggle('ri-arrow-up-s-line', nowOpen);
        ddDiscIcon.classList.toggle('ri-arrow-down-s-line', !nowOpen);
      });
      const initialDisc = Number(p.discount_percent ?? 0);
      const initialDiscText = initialDisc === 20 ? '20% Discount'
        : initialDisc === 40 ? '40% Discount'
        : initialDisc === 50 ? '50% Discount'
        : initialDisc === 70 ? '70% Discount'
        : 'No Discount';
      applyDiscountText(initialDiscText, false);
      ddDiscount.appendChild(ddDiscToggle);
      ddDiscount.appendChild(ddDiscMenu);
      card.appendChild(ddDiscount);

      // Online/Offline custom dropdown
      const ddStatus = document.createElement('div');
      ddStatus.className = 'dropdown dropdown_small dropdown_online_offline';
      const ddStatToggle = document.createElement('button');
      ddStatToggle.type = 'button';
      ddStatToggle.className = 'dropdown_toggle';
      const ddStatLabel = document.createElement('span');
      ddStatLabel.className = 'dropdown_label';
      const ddStatDot = document.createElement('span');
      ddStatDot.className = 'dot online';
      const ddStatText = document.createElement('span');
      ddStatText.textContent = 'Online';
      const ddStatIcon = document.createElement('i');
      ddStatIcon.className = 'ri-arrow-down-s-line';
      ddStatLabel.appendChild(ddStatText);
      ddStatLabel.appendChild(ddStatDot);
      ddStatToggle.appendChild(ddStatLabel);
      ddStatToggle.appendChild(ddStatIcon);
      const ddStatMenu = document.createElement('div');
      ddStatMenu.className = 'dropdown_menu_small';
      [
        { text: 'Online', cls: 'online' },
        { text: 'Offline', cls: 'offline' },
      ].forEach(({ text, cls }) => {
        const item = document.createElement('div');
        item.className = 'dropdown_item';
        const leftWrap = document.createElement('span');
        leftWrap.style.display = 'inline-flex';
        leftWrap.style.alignItems = 'center';
        leftWrap.style.gap = '12px';
        const lbl = document.createElement('span');
        lbl.textContent = text;
        const dot = document.createElement('span');
        dot.className = 'dot ' + cls;
        leftWrap.appendChild(lbl);
        leftWrap.appendChild(dot);
        item.appendChild(leftWrap);
        item.addEventListener('click', () => {
          ddStatText.textContent = text;
          ddStatDot.classList.toggle('online', cls === 'online');
          ddStatDot.classList.toggle('offline', cls === 'offline');
          ddStatus.classList.remove('open');
          ddStatMenu.setAttribute('aria-hidden', 'true');
          ddStatIcon.classList.remove('ri-arrow-up-s-line');
          ddStatIcon.classList.add('ri-arrow-down-s-line');
          card.dataset.online = cls === 'online' ? 'true' : 'false';
          selectedProduct = Number(p.id);
          Array.from(productOverviewList.children).forEach(el => el.classList.remove('selected'));
          card.classList.add('selected');
          updateFooterControls();
        });
        ddStatMenu.appendChild(item);
      });
      ddStatToggle.addEventListener('click', (e) => {
        e.preventDefault();
        const nowOpen = ddStatus.classList.toggle('open');
        ddStatMenu.setAttribute('aria-hidden', nowOpen ? 'false' : 'true');
        ddStatIcon.classList.toggle('ri-arrow-up-s-line', nowOpen);
        ddStatIcon.classList.toggle('ri-arrow-down-s-line', !nowOpen);
      });
      const initialOnline = (p.is_online ?? true) ? 'online' : 'offline';
      ddStatText.textContent = initialOnline === 'online' ? 'Online' : 'Offline';
      ddStatDot.classList.toggle('online', initialOnline === 'online');
      ddStatDot.classList.toggle('offline', initialOnline === 'offline');
      card.dataset.online = initialOnline === 'online' ? 'true' : 'false';
      ddStatus.appendChild(ddStatToggle);
      ddStatus.appendChild(ddStatMenu);
      card.appendChild(ddStatus);

      // Qty total
      const qty = document.createElement('div');
      qty.className = 'po_qty';
      qty.textContent = 'Qty ' + totalQty;
      card.appendChild(qty);

      if (selectedProduct === Number(p.id)) {
        card.classList.add('selected');
      }
      card.addEventListener('click', (e) => {
        if (e.target.closest('.dropdown_small')) return;
        selectedProduct = Number(p.id);
        Array.from(productOverviewList.children).forEach(el => el.classList.remove('selected'));
        card.classList.add('selected');
        updateFooterControls();
      });

      return card;
    }
    function renderPagination() {
      if (!productPagination) return;
      productPagination.innerHTML = '';
      const totalPages = Math.max(1, Math.ceil((products.length || 0) / PAGE_SIZE));
      for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'page_link' + (i === currentPage ? ' active' : '');
        btn.textContent = String(i);
        btn.addEventListener('click', () => {
          currentPage = i;
          renderPage();
        });
        productPagination.appendChild(btn);
      }
    }
    function renderPage() {
      productOverviewList.innerHTML = '';
      const start = (currentPage - 1) * PAGE_SIZE;
      const slice = (Array.isArray(products) ? products : []).slice(start, start + PAGE_SIZE);
      slice.forEach(p => productOverviewList.appendChild(buildCard(p)));
      renderPagination();
      updateFooterControls();
    }
    async function saveCardState(card) {
      const isOnline = card ? (card.dataset.online === 'true') : true;
      const discountPercent = card ? Number(card.dataset.discount || '0') : 0;
      const payload = {
        is_online: isOnline ? 1 : 0,
        discount_percent: Math.max(0, Math.min(100, discountPercent)),
      };
      const url = `/admin/products/${selectedProduct}`;
      const res = await fetch(url, {
        method: 'PATCH',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'Content-Type': 'application/json',
        },
        credentials: 'include',
        body: JSON.stringify(payload),
      });
      if (!res.ok) throw new Error('Failed to save product');
      try {
        const list = await fetch('/api/products', { headers: { 'Accept': 'application/json' } }).then(r => r.json());
        products = Array.isArray(list) ? list : products;
      } catch (_) {}
      renderPage();
      const textEl = document.querySelector('#statusToast .toast_text');
      if (textEl) textEl.textContent = 'Changes saved';
      if (typeof openStatusToast === 'function') openStatusToast();
    }
    productOverviewSaveBtn?.addEventListener('click', async () => {
      if (!selectedProduct) {
        const textEl = document.querySelector('#statusToast .toast_text');
        if (textEl) textEl.textContent = 'Select a product';
        if (typeof openStatusToast === 'function') openStatusToast();
        return;
      }
      const selectedEl = Array.from(productOverviewList.children).find(el => el.classList.contains('selected'));
      try {
        await saveCardState(selectedEl || null);
      } catch (e) {
        const textEl = document.querySelector('#statusToast .toast_text');
        if (textEl) textEl.textContent = 'Failed to save changes';
        if (typeof openStatusToast === 'function') openStatusToast();
      }
    });
    productOverviewDeleteBtn?.addEventListener('click', async () => {
      if (!selectedProduct) {
        const textEl = document.querySelector('#statusToast .toast_text');
        if (textEl) textEl.textContent = 'Select a product';
        if (typeof openStatusToast === 'function') openStatusToast();
        return;
      }
      try {
        const res = await fetch(`/admin/products/${selectedProduct}`, {
          method: 'DELETE',
          headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          credentials: 'include'
        });
        if (!res.ok) throw new Error('Failed to delete product');
        const prevPage = currentPage;
        products = products.filter(p => Number(p.id) !== Number(selectedProduct));
        const totalPagesLocal = Math.max(1, Math.ceil((products.length || 0) / PAGE_SIZE));
        currentPage = Math.min(prevPage, totalPagesLocal);
        selectedProduct = null;
        renderPage();
        const list = await fetch('/api/products', { headers: { 'Accept': 'application/json' } }).then(r => r.json()).catch(() => []);
        products = Array.isArray(list) ? list : products;
        const totalPages = Math.max(1, Math.ceil((products.length || 0) / PAGE_SIZE));
        currentPage = Math.min(prevPage, totalPages);
        renderPage();
        const textEl = document.querySelector('#statusToast .toast_text');
        if (textEl) textEl.textContent = 'Product deleted';
        if (typeof openStatusToast === 'function') openStatusToast();
      } catch (e) {
        console.error(e);
      }
    });
    document.addEventListener('click', (e) => {
      const insideList = productOverviewList.contains(e.target);
      const insideFooter = productOverviewFooter?.contains(e.target) ?? false;
      if (!insideList && !insideFooter) {
        selectedProduct = null;
        Array.from(productOverviewList.children).forEach(el => el.classList.remove('selected'));
        updateFooterControls();
      }
    });
    fetch('/api/products', { headers: { 'Accept': 'application/json' } })
      .then(r => r.json())
      .then(list => {
        products = Array.isArray(list) ? list : [];
        currentPage = 1;
        renderPage();
      })
      .catch(() => {});
  }

  // Removed logout button hover/mousemove animations per request
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAccount);
} else {
  initAccount();
}
