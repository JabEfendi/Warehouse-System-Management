<x-layout>
  <x-slot:title>{{ $title ?? 'Product & Item Master' }}</x-slot:title>
  <div class="tbl overflow-hidden w-full">
    <!-- Filter & Actions -->
    <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
      <div class="flex flex-wrap items-center gap-2">
        <input id="search" type="text" placeholder="Search product name..."
          class="border rounded px-3 py-2 w-64" />

        <select id="status" class="border rounded px-3 py-2">
          <option value="">All Status</option>
          <option value="ready">Ready</option>
          <option value="low">Low</option>
          <option value="empty">Empty</option>
        </select>

        <select id="per_page" class="border rounded px-3 py-2">
          <option value="10">10 / page</option>
          <option value="25">25 / page</option>
          <option value="50">50 / page</option>
        </select>

        <button id="reset" class="border rounded px-3 py-2 bg-gray-200 hover:bg-gray-300">Reset</button>
      </div>

      <button onclick="openAddProductModal()" class="bg-blue-600 hover:scale-90 transition-transform duration-300 text-white font-semibold py-2 px-4 rounded-lg shadow-md">
        <b>+</b> Add Product
      </button>
    </div>


    <table class="table-auto overflow-hidden rounded-lg border-collapse border border-black w-full text-left">
      <thead class="bg-gray-100">
        <tr>
          <th class="border-b px-4 py-2">Kode</th>
          <th class="border-b px-4 py-2">Nama</th>
          <th class="border-b px-4 py-2">Harga Jual</th>
          <th class="border-b px-4 py-2">Stok</th>
          <th class="border-b px-4 py-2">Status</th>
          <th class="border-b text-center px-4 py-2">Aksi</th>
        </tr>
      </thead>
      <tbody id="tbody" class="[&>tr:nth-child(even)]:bg-gray-200 [&>tr:hover]:bg-gray-100">
        <tr><td colspan="6" class="border-b px-4 py-6 text-center text-gray-500">Loading...</td></tr>
      </tbody>
    </table>
    <div id="pager" class="mt-4 flex items-center gap-2"></div>
  </div>

  <!-- Add Product Modal -->
    <div id="addProductModal"
      class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50"
      role="dialog" aria-modal="true" aria-labelledby="addProductTitle">

      <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3 border-b">
          <h2 id="addProductTitle" class="text-lg font-semibold">Tambah Produk</h2>
          <button id="closeAddProduct" class="text-gray-500 hover:text-black text-xl leading-none">×</button>
        </div>

        <form id="addProductForm" class="p-5 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-gray-600">Kode Produk</label>
              <input type="text" name="kode_product" class="w-full border rounded p-2" required>
              <!-- <input type="text" name="kode_product" class="w-full border rounded p-2 bg-gray-100" readonly placeholder="Auto-generated"> -->
            </div>

            <div>
              <label class="block text-gray-600">Nama Produk</label>
              <input type="text" name="name" class="w-full border rounded p-2" required>
            </div>

            <div class="col-span-2">
              <label class="block text-gray-600">Deskripsi</label>
              <textarea name="description" class="w-full border rounded p-2" rows="2"></textarea>
            </div>

            <div>
              <label class="block text-gray-600">Harga Beli</label>
              <input type="number" step="0.01" name="harga_beli" class="w-full border rounded p-2">
            </div>

            <div>
              <label class="block text-gray-600">Harga Jual</label>
              <input type="number" step="0.01" name="harga_jual" class="w-full border rounded p-2">
            </div>

            <div>
              <label class="block text-gray-600">Stok Awal</label>
              <input type="number" name="stock_quantity" class="w-full border rounded p-2" min="0" required>
            </div>

            <div>
              <label class="block text-gray-600">Stok Minimum</label>
              <input type="number" name="stok_minimum" class="w-full border rounded p-2" min="0">
            </div>

            <div>
              <label class="block text-gray-600">Kategori</label>
              <select id="add_category_id" name="category_id" class="w-full border rounded p-2 select2_category">
                <option value="">-- Pilih Kategori --</option>
              </select>
            </div>

            <div>
              <label class="block text-gray-600">Satuan (UOM)</label>
              <select id="add_uom_id" name="uom_id" class="w-full border rounded p-2 select2_uom">
                <option value="">-- Pilih Satuan --</option>
              </select>
            </div>

            <div class="col-span-2">
              <label class="block text-gray-600">Supplier</label>
              <select id="add_supplier_id" name="supplier_id" class="w-full border rounded p-2 select2_supplier">
                <option value="">-- Pilih Supplier --</option>
              </select>
            </div>

            <div class="col-span-2 flex items-center space-x-2">
              <input type="checkbox" name="is_active" value="1" checked>
              <label class="text-gray-600">Aktif</label>
            </div>
          </div>

          <div class="flex justify-end pt-4 border-t mt-3">
            <button type="button" id="cancelAddProduct" class="px-4 py-2 border rounded mr-2">Batal</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
          </div>
        </form>
      </div>
    </div>


  <!-- Edit Product Modal -->
    <div id="editProductModal"
      class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50"
      role="dialog" aria-modal="true" aria-labelledby="editProductTitle">

      <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3 border-b">
          <h2 id="editProductTitle" class="text-lg font-semibold">Edit Produk</h2>
          <button id="closeEditProduct" class="text-gray-500 hover:text-black text-xl leading-none">×</button>
        </div>

        <form id="editProductForm" class="p-5 space-y-4">
          <input type="hidden" name="id" id="edit_id">

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-gray-600">Kode Produk</label>
              <input type="text" name="kode_product" id="edit_kode_product" class="w-full border rounded p-2" readonly>
            </div>

            <div>
              <label class="block text-gray-600">Nama Produk</label>
              <input type="text" name="name" id="edit_name" class="w-full border rounded p-2" required>
            </div>

            <div class="col-span-2">
              <label class="block text-gray-600">Deskripsi</label>
              <textarea name="description" id="edit_description" class="w-full border rounded p-2" rows="2"></textarea>
            </div>

            <div>
              <label class="block text-gray-600">Harga Beli</label>
              <input type="number" step="0.01" name="harga_beli" id="edit_harga_beli" class="w-full border rounded p-2">
            </div>

            <div>
              <label class="block text-gray-600">Harga Jual</label>
              <input type="number" step="0.01" name="harga_jual" id="edit_harga_jual" class="w-full border rounded p-2">
            </div>

            <div>
              <label class="block text-gray-600">Stok Sekarang</label>
              <input type="number" name="stock_quantity" id="edit_stock_quantity" class="w-full border rounded p-2" min="0">
            </div>

            <div>
              <label class="block text-gray-600">Stok Minimum</label>
              <input type="number" name="stok_minimum" id="edit_stok_minimum" class="w-full border rounded p-2" min="0">
            </div>

            <div>
              <label class="block text-gray-600">Kategori</label>
              <select name="category_id" id="edit_category_id" class="w-full border rounded p-2 select2_category">
                <option value="">-- Pilih Kategori --</option>
              </select>
            </div>

            <div>
              <label class="block text-gray-600">Satuan (UOM)</label>
              <select name="uom_id" id="edit_uom_id" class="w-full border rounded p-2 select2_uom">
                <option value="">-- Pilih Satuan --</option>
              </select>
            </div>

            <div class="col-span-2">
              <label class="block text-gray-600">Supplier</label>
              <select name="supplier_id" id="edit_supplier_id" class="w-full border rounded p-2 select2_supplier">
                <option value="">-- Pilih Supplier --</option>
              </select>
            </div>

            <div class="col-span-2 flex items-center space-x-2">
              <input type="checkbox" name="is_active" id="edit_is_active" value="1">
              <label class="text-gray-600">Aktif</label>
            </div>
          </div>

          <div class="flex justify-end pt-4 border-t mt-3">
            <button type="button" id="cancelEditProduct" class="px-4 py-2 border rounded mr-2">Batal</button>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Update</button>
          </div>
        </form>
      </div>
    </div>


  <script>
  // === 🔧 Elemen DOM yang dipakai ===
    const tbody = document.getElementById('tbody');           // Tempat isi tabel produk
    const pager = document.getElementById('pager');           // Tempat tombol pagination
    const inputQ = document.getElementById('search');         // Input pencarian
    const selS = document.getElementById('status');           // Dropdown filter status
    const selPP = document.getElementById('per_page');        // Dropdown jumlah data per halaman
    const btnReset = document.getElementById('reset');        // Tombol reset filter

    // Modal untuk tambah dan edit produk
    const addModal = document.getElementById('addProductModal');
    const editModal = document.getElementById('editProductModal');

    // === 📦 State untuk menyimpan kondisi filter, pagination, dll ===
    let state = { page: 1, q: '', status: '', per_page: 10, loading: false };

    // === ⏳ Helper: debounce() ===
    // Digunakan agar fungsi hanya dijalankan setelah user berhenti mengetik selama beberapa milidetik.
    const debounce = (fn, ms = 400) => { 
      let t; 
      return (...args) => { 
        clearTimeout(t); 
        t = setTimeout(() => fn(...args), ms); 
      }; 
    };

    // === 🏷️ Helper: badge() ===
    // Menentukan warna badge (label status) berdasarkan status produk.
    const badge = (status) => ({
      ready: 'bg-green-100 text-green-700',   // Barang siap
      low: 'bg-orange-100 text-orange-700',   // Barang hampir habis
      empty: 'bg-red-200 text-red-800'        // Barang habis
    }[status] || 'bg-gray-100 text-gray-700'); // Default jika status tidak dikenal

    // === 📥 Ambil data produk dari API ===
    async function fetchData() {
      // const res = await fetch(`/api/products?${params.toString()}`, {credentials: 'same-origin'});
      if (state.loading) return; // Hindari request ganda
      state.loading = true;

      // Tampilkan pesan loading di tabel
      tbody.innerHTML = `<tr><td colspan="6" class="text-center py-6">Loading...</td></tr>`;

      try {
        // Buat query string (misal: ?page=1&q=laptop&status=ready)
        const params = new URLSearchParams({
          page: state.page, q: state.q, status: state.status, per_page: state.per_page
        });

        // Ambil data dari API /api/products
        const res = await fetch(`/api/products?${params.toString()}`, {credentials: 'include'});
        const json = await res.json();

        // Render hasil data dan pagination
        renderRows(json.data || []);
        renderPager(json.meta || {});
      } catch (e) {
        // Jika error, tampilkan pesan error di tabel
        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-red-600 py-6">Gagal memuat data</td></tr>`;
      } finally {
        state.loading = false;
      }
    }

    // === 🧾 Render isi tabel produk ===
    function renderRows(items) {
      if (!items.length) {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-6 text-gray-500">No data</td></tr>`;
        return;
      }

      // Buat baris tabel untuk setiap produk
      tbody.innerHTML = items.map(p => `
        <tr class="hover:bg-gray-100">
          <td class="border-b px-4 py-2">${p.kode_product}</td>
          <td class="border-b px-4 py-2">${p.name}</td>
          <td class="border-b px-4 py-2">Rp ${p.harga_jual ?? '-'}</td>
          <td class="border-b px-4 py-2">${p.stock_quantity ?? 0}</td>
          <td class="border-b px-4 py-2">
            <span class="px-2 py-1 text-xs rounded ${badge(p.status)}">${p.status}</span>
          </td>
          <td class="border-b px-4 py-2 text-center">
            <button onclick="openEditProductModal(${p.id})" class="bg-green-600 text-white px-2 py-1 rounded">Edit</button>
          </td>
        </tr>
      `).join('');
    }

    // === ⏩ Render Pagination (Prev / Next) ===
    function renderPager(meta) {
      const current = meta.current_page ?? 1;
      const last = meta.last_page ?? 1;

      pager.innerHTML = `
        <button ${current <= 1 ? 'disabled' : ''} data-page="${current - 1}" class="border px-3 py-1 rounded">Prev</button>
        <span>Page ${current} of ${last}</span>
        <button ${current >= last ? 'disabled' : ''} data-page="${current + 1}" class="border px-3 py-1 rounded">Next</button>
      `;

      // Event click untuk pagination
      pager.querySelectorAll('button[data-page]').forEach(btn => {
        btn.onclick = () => {
          state.page = parseInt(btn.dataset.page);
          fetchData();
        };
      });
    }

    // === 🔍 Event Filter dan Search ===
    inputQ.addEventListener('input', debounce(() => {
      state.q = inputQ.value;
      state.page = 1;
      fetchData();
    }));

    selS.addEventListener('change', () => {
      state.status = selS.value;
      state.page = 1;
      fetchData();
    });

    selPP.addEventListener('change', () => {
      state.per_page = parseInt(selPP.value);
      state.page = 1;
      fetchData();
    });

    btnReset.addEventListener('click', () => {
      inputQ.value = ''; selS.value = ''; selPP.value = '10';
      state = { page: 1, q: '', status: '', per_page: 10, loading: false };
      fetchData();
    });

    async function loadFormOptions() {
      try {
        const res = await fetch('/api/products/form-options', {credentials: 'include'});
        const data = await res.json();

        populateSelect('#add_category_id', data.categories);
        populateSelect('#add_uom_id', data.uoms);
        populateSelect('#add_supplier_id', data.suppliers);
      } catch (e) {
        console.error('Gagal memuat opsi form:', e);
      }
    }

    function populateSelect(selector, items) {
      const select = document.querySelector(selector);
      select.innerHTML = '<option value="">-- Pilih --</option>';
      items.forEach(i => {
        select.innerHTML += `<option value="${i.id}">${i.name}</option>`;
      });
    }

    // === 🪟 Modal Functions ===
    function openAddProductModal() {
      addModal.classList.replace('hidden', 'flex');
      loadFormOptions();
    }

    function closeAddProductModal() {
      addModal.classList.replace('flex', 'hidden');
      document.getElementById('addProductForm').reset();
    }

    function openEditProductModal(id) {
      editModal.classList.replace('hidden', 'flex');
    }

    function closeEditProductModal() {
      editModal.classList.replace('flex', 'hidden');
      document.getElementById('editProductForm').reset();
    }

    // === 💾 Submit Tambah Produk ===
document.getElementById('addProductForm').addEventListener('submit', async function(e) {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);

  try {
    const res = await fetch('/api/products', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: formData,
      credentials: 'include'
    });

    const data = await res.json();

    if (res.ok) {
      toastr.success(data.message || 'Produk berhasil ditambahkan');
      closeAddProductModal();
      form.reset();
      fetchData(); // refresh tabel setelah tambah
    } else {
      toastr.error(data.message || 'Gagal menambahkan produk');
    }
  } catch (err) {
    console.error(err);
    toastr.error('Terjadi kesalahan saat menyimpan produk');
  }
});


    // === 🖱️ Event tombol tutup modal ===
    document.getElementById('closeAddProduct').onclick = closeAddProductModal;
    document.getElementById('cancelAddProduct').onclick = closeAddProductModal;
    document.getElementById('closeEditProduct').onclick = closeEditProductModal;
    document.getElementById('cancelEditProduct').onclick = closeEditProductModal;

    // Tutup modal saat klik di luar area konten modal
    addModal.onclick = e => { if (e.target === addModal) closeAddProductModal(); };
    editModal.onclick = e => { if (e.target === editModal) closeEditProductModal(); };

    // Tutup modal saat tekan Escape
    document.addEventListener('keydown', e => { 
      if (e.key === 'Escape') { 
        closeAddProductModal(); 
        closeEditProductModal(); 
      } 
    });

    // === 🚀 Jalankan pertama kali saat halaman dimuat ===
    fetchData();
  </script>
</x-layout>