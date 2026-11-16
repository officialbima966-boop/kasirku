/**
 * KasirKu — Shopee POS & Marketplace Edition Client JavaScript
 */
document.addEventListener('DOMContentLoaded', () => {
  // 1. Mobile Sidebar Toggle
  const sidebar = document.getElementById('appSidebar');
  const openBtn = document.getElementById('sidebarOpenBtn');
  const closeBtn = document.getElementById('sidebarCloseBtn');
  const backdrop = document.getElementById('sidebarBackdrop');

  const openSidebar = () => {
    if (sidebar) sidebar.classList.add('open');
    if (backdrop) backdrop.classList.add('active');
    document.body.style.overflow = 'hidden';
  };

  const closeSidebar = () => {
    if (sidebar) sidebar.classList.remove('open');
    if (backdrop) backdrop.classList.remove('active');
    document.body.style.overflow = '';
  };

  document.querySelectorAll('.product-barcode-input').forEach(input => {
    input.addEventListener('keydown', event => {
      if (event.key === 'Enter') event.preventDefault();
    });
  });

  const printReceiptButton = document.getElementById('printReceiptButton');
  const receiptCard = document.getElementById('receiptCard');
  if (printReceiptButton && receiptCard) {
    const printModeClass = 'receipt-print-mode';
    const enablePrintMode = () => {
      document.documentElement.classList.add(printModeClass);
      document.body.classList.add(printModeClass);
    };
    const clearPrintMode = () => {
      document.documentElement.classList.remove(printModeClass);
      document.body.classList.remove(printModeClass);
    };

    window.addEventListener('beforeprint', enablePrintMode);
    window.addEventListener('afterprint', clearPrintMode);
    printReceiptButton.addEventListener('click', () => {
      enablePrintMode();
      window.print();
    });
  }

  if (openBtn) openBtn.addEventListener('click', openSidebar);
  if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
  if (backdrop) backdrop.addEventListener('click', closeSidebar);

  // 2. Global Page Search & Filter
  const searchInput = document.getElementById('globalPageSearch');
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      const q = e.target.value.toLowerCase().trim();
      
      // Filter product cards if present
      const cards = document.querySelectorAll('.shopee-product-card');
      cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(q) ? '' : 'none';
      });

      // Filter table rows if present
      const tableRows = document.querySelectorAll('tbody tr');
      tableRows.forEach(row => {
        if (row.querySelector('.empty')) return;
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(q) ? '' : 'none';
      });

      // Filter POS items grid if present
      const posItems = document.querySelectorAll('.pos-item-card');
      posItems.forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(q) ? '' : 'none';
      });
    });
  }

  // 3. Category Tab Filtering
  const catTabs = document.querySelectorAll('.category-tab-btn');
  if (catTabs.length > 0) {
    catTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        catTabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        const catId = tab.getAttribute('data-cat');

        const cards = document.querySelectorAll('.shopee-product-card');
        cards.forEach(card => {
          if (!catId || catId === 'all') {
            card.style.display = '';
          } else {
            const cardCat = card.getAttribute('data-cat');
            card.style.display = (cardCat === catId) ? '' : 'none';
          }
        });

        const rows = document.querySelectorAll('.product-table-row');
        rows.forEach(row => {
          if (!catId || catId === 'all') {
            row.style.display = '';
          } else {
            const rowCat = row.getAttribute('data-cat');
            row.style.display = (rowCat === catId) ? '' : 'none';
          }
        });
      });
    });
  }

  // 4. View Switcher (Grid vs Table)
  const viewBtns = document.querySelectorAll('.view-btn');
  const gridView = document.getElementById('catalogGridView');
  const tableView = document.getElementById('catalogTableView');
  if (viewBtns.length && gridView && tableView) {
    viewBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        viewBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const view = btn.getAttribute('data-view');
        if (view === 'table') {
          gridView.style.display = 'none';
          tableView.style.display = 'block';
        } else {
          gridView.style.display = 'grid';
          tableView.style.display = 'none';
        }
      });
    });
  }

  // 5. Classic Transaction Row Repeater (Backward compatibility)
  const addItemBtn = document.getElementById('add-item');
  if (addItemBtn) {
    addItemBtn.addEventListener('click', () => {
      const first = document.querySelector('.transaction-row');
      if (!first) return;
      const clone = first.cloneNode(true);
      const qtyInput = clone.querySelector('input[type="number"]');
      if (qtyInput) qtyInput.value = 1;
      document.getElementById('items-container').appendChild(clone);
    });
  }

  document.addEventListener('click', e => {
    if (e.target.classList.contains('remove-row') || e.target.closest('.remove-row')) {
      const rows = document.querySelectorAll('.transaction-row');
      if (rows.length > 1) {
        const targetRow = e.target.closest('.transaction-row');
        if (targetRow) targetRow.remove();
      } else {
        alert('Minimal harus ada 1 produk dalam transaksi.');
      }
    }
  });

  // 6. Interactive Shopee POS Kasir System
  window.posSystem = (function() {
    const cart = new Map(); // id -> { id, name, price, stock, qty }
    const itemsContainer = document.getElementById('pos-hidden-inputs');
    const cartListEl = document.getElementById('pos-cart-list');
    const subtotalEl = document.getElementById('pos-subtotal');
    const discountInput = document.getElementById('pos-discount-input');
    const taxEl = document.getElementById('pos-tax');
    const totalEl = document.getElementById('pos-total');
    const paymentInput = document.getElementById('pos-cash-input');
    const changeRow = document.getElementById('pos-change-row');
    const changeLabel = document.getElementById('pos-change-label');
    const changeEl = document.getElementById('pos-change');
    const transactionForm = document.getElementById('transaction-form');
    const barcodeInput = document.getElementById('pos-barcode-input');
    const barcodeStatus = document.getElementById('pos-barcode-status');
    const emptyNotice = document.getElementById('pos-cart-empty');

    if (!cartListEl) return null;

    const formatRp = (num) => 'Rp ' + Number(num).toLocaleString('id-ID');
    let totalDue = 0;

    const updateTotals = () => {
      let subtotal = 0;
      cart.forEach(item => {
        subtotal += item.price * item.qty;
      });

      const diskon = parseFloat(discountInput ? discountInput.value : 0) || 0;
      const taxable = Math.max(0, subtotal - diskon);
      const pajak = Math.round(taxable * 0.11);
      const total = taxable + pajak;
      totalDue = total;

      if (subtotalEl) subtotalEl.textContent = formatRp(subtotal);
      if (taxEl) taxEl.textContent = formatRp(pajak);
      if (totalEl) totalEl.textContent = formatRp(total);

      if (paymentInput && changeRow && changeLabel && changeEl) {
        const payment = Number(paymentInput.value) || 0;
        const difference = payment - total;
        const underpaid = difference < 0;
        changeRow.classList.toggle('is-underpaid', underpaid);
        changeLabel.textContent = underpaid ? 'Kurang Bayar' : 'Kembalian';
        changeEl.textContent = formatRp(Math.abs(difference));
      }

      // Render hidden form inputs
      if (itemsContainer) {
        itemsContainer.innerHTML = '';
        cart.forEach(item => {
          const prodInput = document.createElement('input');
          prodInput.type = 'hidden';
          prodInput.name = 'produk[]';
          prodInput.value = item.id;

          const qtyInput = document.createElement('input');
          qtyInput.type = 'hidden';
          qtyInput.name = 'jumlah[]';
          qtyInput.value = item.qty;

          itemsContainer.appendChild(prodInput);
          itemsContainer.appendChild(qtyInput);
        });
      }

      if (emptyNotice) {
        emptyNotice.style.display = cart.size === 0 ? 'block' : 'none';
      }
    };

    const renderCart = () => {
      if (!cartListEl) return;
      cartListEl.innerHTML = '';

      cart.forEach((item, id) => {
        const row = document.createElement('div');
        row.className = 'cart-item-row';
        row.innerHTML = `
          <div class="cart-item-info">
            <div class="cart-item-name">${item.name}</div>
            <div class="cart-item-unit-price">${formatRp(item.price)} × ${item.qty}</div>
          </div>
          <div class="cart-qty-ctrl">
            <button type="button" class="qty-btn btn-minus" data-id="${id}">-</button>
            <input type="text" class="qty-input" value="${item.qty}" readonly>
            <button type="button" class="qty-btn btn-plus" data-id="${id}">+</button>
          </div>
          <div class="cart-item-subtotal">${formatRp(item.price * item.qty)}</div>
          <button type="button" class="cart-remove-btn" data-id="${id}" title="Hapus"><i class="fa-solid fa-trash"></i></button>
        `;
        cartListEl.appendChild(row);
      });

      updateTotals();
    };

    const addItem = (id, name, price, stock) => {
      id = parseInt(id, 10);
      price = parseFloat(price);
      stock = parseInt(stock, 10);

      if (cart.has(id)) {
        const existing = cart.get(id);
        if (existing.qty < stock) {
          existing.qty++;
        } else {
          alert('Stok maksimum produk tercapai (' + stock + ')');
          return false;
        }
      } else {
        if (stock > 0) {
          cart.set(id, { id, name, price, stock, qty: 1 });
        } else {
          alert('Maaf, stok produk habis.');
          return false;
        }
      }
      renderCart();
      return true;
    };

    if (barcodeInput) {
      barcodeInput.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter' && event.key !== 'Tab') return;
        event.preventDefault();
        const barcode = barcodeInput.value.trim().toUpperCase();
        const productButton = Array.from(document.querySelectorAll('.btn-add-pos-item'))
          .find(button => button.getAttribute('data-barcode') === barcode);

        if (!barcode) {
          if (barcodeStatus) barcodeStatus.textContent = 'Barcode belum terbaca.';
        } else if (!productButton) {
          if (barcodeStatus) barcodeStatus.textContent = 'Produk dengan barcode tersebut tidak ditemukan atau stok habis.';
        } else {
          const added = addItem(
            productButton.getAttribute('data-id'),
            productButton.getAttribute('data-name'),
            productButton.getAttribute('data-price'),
            productButton.getAttribute('data-stock')
          );
          if (barcodeStatus) {
            barcodeStatus.textContent = added
              ? productButton.getAttribute('data-name') + ' ditambahkan ke keranjang.'
              : 'Jumlah produk tersebut sudah mencapai stok yang tersedia.';
          }
        }

        barcodeInput.value = '';
        barcodeInput.focus();
      });
    }

    const changeQty = (id, delta) => {
      id = parseInt(id, 10);
      if (!cart.has(id)) return;
      const item = cart.get(id);
      const newQty = item.qty + delta;
      if (newQty <= 0) {
        cart.delete(id);
      } else if (newQty > item.stock) {
        alert('Stok tersisa: ' + item.stock);
      } else {
        item.qty = newQty;
      }
      renderCart();
    };

    const removeItem = (id) => {
      id = parseInt(id, 10);
      cart.delete(id);
      renderCart();
    };

    // Event delegation on cart list
    if (cartListEl) {
      cartListEl.addEventListener('click', (e) => {
        const plusBtn = e.target.closest('.btn-plus');
        const minusBtn = e.target.closest('.btn-minus');
        const removeBtn = e.target.closest('.cart-remove-btn');

        if (plusBtn) changeQty(plusBtn.getAttribute('data-id'), 1);
        if (minusBtn) changeQty(minusBtn.getAttribute('data-id'), -1);
        if (removeBtn) removeItem(removeBtn.getAttribute('data-id'));
      });
    }

    if (discountInput) {
      discountInput.addEventListener('input', updateTotals);
    }

    if (paymentInput) {
      paymentInput.addEventListener('input', () => {
        paymentInput.setCustomValidity('');
        updateTotals();
      });
    }

    if (transactionForm && paymentInput) {
      transactionForm.addEventListener('submit', (event) => {
        const payment = Number(paymentInput.value);
        if (!Number.isSafeInteger(payment) || payment < totalDue) {
          event.preventDefault();
          paymentInput.setCustomValidity('Uang dibayar kurang dari total pembayaran.');
          paymentInput.reportValidity();
          return;
        }
        paymentInput.setCustomValidity('');
      });
    }

    // Attach click to pos cards and product buttons
    document.querySelectorAll('.btn-add-pos-item').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');
        const price = btn.getAttribute('data-price');
        const stock = btn.getAttribute('data-stock');
        addItem(id, name, price, stock);
      });
    });

    return { addItem, changeQty, removeItem };
  })();
});
