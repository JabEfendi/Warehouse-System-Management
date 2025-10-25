<x-layout>
  <x-slot:title>{{ $title ?? 'Warehouse Layout Management' }}</x-slot:title>
  <div class="tbl overflow-hidden w-full">
    <div class="mb-4 flex flex-wrap items-center gap-2">
      <input id="search" type="text" placeholder="Search name/email"
            class="border rounded px-3 py-2 w-64" />
      <select id="status" class="border rounded px-3 py-2">
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
        <option value="suspend">Suspended</option>
      </select>
      <select id="role" class="border rounded px-3 py-2">
        <option value="">All Roles</option>
        @foreach($roles as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach
      </select>
      <select id="per_page" class="border rounded px-3 py-2">
        <option value="10">10 / page</option>
        <option value="25">25 / page</option>
        <option value="50">50 / page</option>
      </select>
      <button id="reset" class="border rounded px-3 py-2">Reset</button>
    </div>

    <table class="table-auto overflow-hidden rounded-lg border-collapse border border-black w-full text-left">
      <thead class="bg-gray-100">
        <tr>
          <th class="border-b px-4 py-2">Name</th>
          <th class="border-b px-4 py-2">Email</th>
          <th class="border-b px-4 py-2">Role</th>
          <th class="border-b px-4 py-2">Created At</th>
          <th class="border-b px-4 py-2">Status</th>
          <th class="border-b text-center px-4 py-2">Action</th>
        </tr>
      </thead>
      <tbody id="tbody" class="[&>tr:nth-child(even)]:bg-gray-200 [&>tr:hover]:bg-gray-100">
        <tr><td colspan="6" class="border px-4 py-6 text-center text-gray-500">Loading...</td></tr>
      </tbody>
    </table>
    <div id="pager" class="mt-4 flex items-center gap-2"></div>
  </div>

  <!-- Modal: Detail User -->
  <div id="userModal"
      class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50"
      role="dialog" aria-modal="true" aria-labelledby="userModalTitle">

    <div class="bg-white w-full max-w-xl rounded-lg shadow-lg overflow-hidden">
      <div class="flex items-center justify-between px-5 py-3 border-b">
        <h2 id="userModalTitle" class="text-lg font-semibold">User Detail</h2>
        <button id="userModalClose" class="text-gray-500 hover:text-black text-xl leading-none">×</button>
      </div>

      <div id="userModalBody" class="p-5">
        <div id="userModalLoading" class="text-gray-500">Loading...</div>

        <div id="userModalContent" class="hidden space-y-3">
          <div class="grid grid-cols-3 gap-3">
            <div class="text-gray-500">Name</div>
            <div class="col-span-2 font-medium" id="ud_name">-</div>

            <div class="text-gray-500">Email</div>
            <div class="col-span-2" id="ud_email">-</div>

            <div class="text-gray-500">Status</div>
            <div class="col-span-2" id="ud_status">-</div>
            
            <div class="text-gray-500">Created At</div>
            <div class="col-span-2" id="ud_created_at">-</div>

            <!-- <div class="col-span-2" id="ud_role">-</div> -->
            
            <div class="text-gray-500">Role</div>
            <select id="ud_role" class="border rounded col-span-2">
              <!-- <option value="">-- Select Role --</option> -->
            </select>

          </div>
        </div>

        <div id="userModalError" class="hidden text-red-600">Gagal memuat data.</div>
      </div>

      <div class="px-5 py-3 border-t flex justify-end gap-2">
        <button id="btnUpdateUser" class="border px-4 py-2 rounded bg-blue-600 text-white opacity-50 cursor-not-allowed" disabled>Update</button>
        <button id="userModalClose2" class="border px-4 py-2 rounded hover:bg-gray-100">Close</button>
      </div>
    </div>
  </div>

  <script>
    const tbody   = document.getElementById('tbody');
    const pager   = document.getElementById('pager');
    const inputQ  = document.getElementById('search');
    const selS    = document.getElementById('status');
    const selR    = document.getElementById('role');
    const selPP   = document.getElementById('per_page');
    const btnReset= document.getElementById('reset');
    const userModal = document.getElementById('userModal');
    const userModalLoading = document.getElementById('userModalLoading');
    const userModalContent = document.getElementById('userModalContent');
    const userModalError   = document.getElementById('userModalError');

    const ud_name       = document.getElementById('ud_name');
    const ud_email      = document.getElementById('ud_email');
    const ud_role       = document.getElementById('ud_role');
    const ud_status     = document.getElementById('ud_status');
    const ud_created_at = document.getElementById('ud_created_at');

    let state = {
      page: 1,
      q: '',
      status: '',
      role_id: '',
      per_page: 10,
      loading: false,
    };


    // Debounce helper
    const debounce = (fn, ms=400) => {
      let t; return (...args)=>{ clearTimeout(t); t = setTimeout(()=>fn(...args), ms); }
    };

    function badge(status){
      const map = {
        active: 'bg-green-100 text-green-700',
        pending: 'bg-yellow-100 text-yellow-700',
        inactive: 'bg-red-300 text-black',
        suspend: 'bg-orange-100 text-red-700'
      };
      return map[status] || 'bg-gray-100 text-gray-700';
    }

    function renderRows(items){
      if(!items.length){
        tbody.innerHTML = `<tr><td colspan="6" class="border px-4 py-6 text-center text-gray-500">No data</td></tr>`;
        return;
      }
      tbody.innerHTML = items.map(u=>`
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-2">${u.name}</td>
          <td class="px-4 py-2">${u.email}</td>
          <td class="px-4 py-2">${u.role}</td>
          <td class="px-4 py-2">${u.created_at ?? '-'}</td>
          <td class="px-4 py-2"><span class="px-2 py-1 text-xs rounded ${badge(u.status)}">${(u.status||'-').replace(/^./,c=>c.toUpperCase())}</span></td>
          <td class="px-2 py-2 flex justify-center gap-2">
            ${u.status === 'pending' ? `
              <button onclick="viewUser(${u.id})"class="rounded bg-blue-600 text-white hover:scale-90 transition-transform duration-300 px-3 py-1">View</button> 
              <button onclick="updateStatus(${u.id}, 'active')" class="rounded bg-green-600 text-white hover:scale-90 transition-transform duration-300 px-2 py-1">Active</button>
              <button onclick="updateStatus(${u.id}, 'inactive')" class="rounded bg-red-600 text-white hover:scale-90 transition-transform duration-300 px-2 py-1">Inactive</button>
            ` : u.status === 'active' ? `
                  <button onclick="viewUser(${u.id})"class="rounded bg-blue-600 text-white hover:scale-90 transition-transform duration-300 px-3 py-1">View</button> 
                  <button onclick="updateStatus(${u.id}, 'inactive')" class="rounded bg-red-600 text-white hover:scale-90 transition-transform duration-300 px-2 py-1">Inactive</button> 
                  <button onclick="updateStatus(${u.id}, 'suspend')" class="rounded bg-yellow-600 text-white hover:scale-90 transition-transform duration-300 px-2 py-1">Suspend</button>
            ` : u.status === 'suspend' ? `
                  <button onclick="viewUser(${u.id})"class="rounded bg-blue-600 text-white hover:scale-90 transition-transform duration-300 px-3 py-1">View</button> 
                  <button onclick="updateStatus(${u.id}, 'active')" class="rounded bg-green-600 text-white hover:scale-90 transition-transform duration-300 px-2 py-1">Active</button> 
                  <button onclick="updateStatus(${u.id}, 'inactive')" class="rounded bg-red-600 text-white hover:scale-90 transition-transform duration-300 px-2 py-1">Inactive</button>
            ` : u.status === 'inactive' ? `
                  <button onclick="viewUser(${u.id})"class="rounded bg-blue-600 text-white hover:scale-90 transition-transform duration-300 px-3 py-1">View</button> 
                  <button onclick="updateStatus(${u.id}, 'active')" class="rounded bg-green-600 text-white hover:scale-90 transition-transform duration-300 px-2 py-1">Active</button>
            ` : ''}
          </td>
        </tr>
      `).join('');
    }

    function renderPager(meta) {
      const current = meta?.current_page ?? 1;
      const last = Math.max(1, meta?.last_page ?? 1);

      const prevDisabled = current <= 1 ? 'opacity-50 pointer-events-none' : '';
      const nextDisabled = current >= last ? 'opacity-50 pointer-events-none' : '';

      // helper buat elemen angka
      const pageBtn = (n, isActive = false) =>
        `<button data-page="${n}"
          class="${isActive
            ? 'font-semibold text-blue-700'
            : 'text-blue-600 hover:underline'} px-1">
          ${n}
        </button>`;

      // range angka di sekitar current (mis. 3 kiri & 3 kanan)
      const span = 3;
      let start = Math.max(1, current - span);
      let end = Math.min(last, current + span);

      // rakit html
      let html = '';

      // Prev
      html += `<button class="border rounded px-3 py-1 ${prevDisabled}" data-page="${current - 1}">Prev</button>`;

      // Label "Page"
      html += `<span class="mx-2 text-gray-500">Page</span>`;

      // Awal (1 ...)
      if (start > 1) {
        html += pageBtn(1, current === 1);
        if (start > 2) html += `<span class="px-1 text-gray-500">…</span>`;
      }

      // Tengah (range dinamis)
      for (let i = start; i <= end; i++) {
        html += pageBtn(i, i === current);
      }

      // Akhir (... last)
      if (end < last) {
        if (end < last - 1) html += `<span class="px-1 text-gray-500">…</span>`;
        html += pageBtn(last, current === last);
      }

      // Next
      html += `<button class="border rounded px-3 py-1 ${nextDisabled}" data-page="${current + 1}">Next</button>`;

      pager.innerHTML = html;

      // bind click
      pager.querySelectorAll('button[data-page]').forEach(btn => {
        btn.addEventListener('click', () => {
          const p = parseInt(btn.dataset.page, 10);
          if (p >= 1 && p <= last) {
            state.page = p;
            fetchData();
          }
        });
      });
    }

    function updateStatus(id, status) {
      const tokenElement = document.querySelector('meta[name="csrf-token"]');
      const token = tokenElement ? tokenElement.getAttribute('content') : '';

      fetch(`/api/users/${id}/status`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
        body: JSON.stringify({ status })
      })
      .then(res => res.json())
      .then(data => {
        console.log(data.message);
        fetchData(); // refresh data tabel
      })
      .catch(err => console.error(err));
    }

    async function fetchData(){
      if(state.loading) return;
      state.loading = true;
      tbody.innerHTML = `<tr><td colspan="6" class="border px-4 py-6 text-center text-gray-500">Loading...</td></tr>`;
      try{
        const params = new URLSearchParams({
          page: state.page,
          q: state.q,
          status: state.status,
          role_id: state.role_id,
          per_page: state.per_page
        });
        const res = await fetch(`/api/users?${params.toString()}`, {
          headers: { 'Accept': 'application/json' }
        });
        if(!res.ok){
          const txt = await res.text();
          throw new Error(txt.slice(0,200));
        }
        const json = await res.json();
        renderRows(json.data || []);
        renderPager(json.meta || { current_page:1, last_page:1 });
      }catch(e){
        console.error(e);
        tbody.innerHTML = `<tr><td colspan="6" class="border px-4 py-6 text-center text-red-600">Gagal memuat data</td></tr>`;
        pager.innerHTML = '';
      }finally{
        state.loading = false;
      }
    }

    // Events
    inputQ.addEventListener('input', debounce(()=>{
      state.q = inputQ.value.trim();
      state.page = 1;
      fetchData();
    }));

    selS.addEventListener('change', ()=>{
      state.status = selS.value;
      state.page = 1;
      fetchData();
    });

    selR.addEventListener('change', ()=>{
      state.role_id = selR.value;
      state.page = 1;
      fetchData();
    });

    selPP.addEventListener('change', ()=>{
      state.per_page = parseInt(selPP.value,10) || 10;
      state.page = 1;
      fetchData();
    });

    btnReset.addEventListener('click', ()=>{
      inputQ.value = '';
      selS.value = '';
      selR.value = '';
      selPP.value = '10';
      state = { page:1, q:'', status:'', role_id:'', per_page:10, loading:false };
      fetchData();
    });

    function openUserModal()  { 
      userModal.classList.remove('hidden'); 
      userModal.classList.add('flex'); 
    }

    function closeUserModal() { 
      userModal.classList.add('hidden'); 
      userModal.classList.remove('flex'); 
      btnUpdateUser.disabled = true;
      btnUpdateUser.classList.add('opacity-50', 'cursor-not-allowed');
    }

    document.getElementById('userModalClose').addEventListener('click', closeUserModal);
    document.getElementById('userModalClose2').addEventListener('click', closeUserModal);
    // klik overlay untuk close
    userModal.addEventListener('click', (e) => { if (e.target === userModal) closeUserModal(); });
    // escape to close
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeUserModal(); });


    let rolesCache = null; // cache supaya load sekali saja
    let currentUserId = null;
    let initialRoleId = null;

    async function loadRoles(currentRoleId = null, currentRoleName = '') {
      // Ambil data roles (cache hanya data, bukan tampilan)
      if (!rolesCache) {
        const res = await fetch('/api/roles', { headers: { Accept: 'application/json' }});
        if (!res.ok) throw new Error(await res.text());
        rolesCache = await res.json();
      }

      const sel = document.getElementById('ud_role');
      sel.innerHTML = '';

      // Jika user punya role, jadikan role tersebut sebagai placeholder
      if (currentRoleId && currentRoleName) {
        const placeholder = document.createElement('option');
        placeholder.value = currentRoleId;
        placeholder.textContent = `-- ${currentRoleName} (current) --`;
        placeholder.disabled = true;
        placeholder.selected = true;
        sel.appendChild(placeholder);
      } else {
        // Jika user belum punya role sama sekali
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = '-- No role assigned --';
        placeholder.disabled = true;
        placeholder.selected = true;
        sel.appendChild(placeholder);
      }

      // Tambahkan semua role lain
      rolesCache.forEach(r => {
        const opt = document.createElement('option');
        opt.value = String(r.id);
        opt.textContent = r.name;
        sel.appendChild(opt);
      });

      return rolesCache;
    }


    async function viewUser(id) {
      // Reset state
      userModalLoading.classList.remove('hidden');
      userModalContent.classList.add('hidden');
      userModalError.classList.add('hidden');

      btnUpdateUser.disabled = true;
      btnUpdateUser.classList.add('opacity-50', 'cursor-not-allowed');

      openUserModal();

      try {
        // Ambil data user
        const res = await fetch(`/api/users/${id}`, { headers: { 'Accept': 'application/json' }});
        if (!res.ok) throw new Error(await res.text());
        const u = await res.json();

        currentUserId = u.id;
        initialRoleId = u.role_id ? String(u.role_id) : null;
        const currentRoleName = u.role ?? ''; // pastikan API kirim 'role_name'

        // Debug
        console.log('User role_id:', u.role, 'role_name:', currentRoleName);

        // Panggil loadRoles, gunakan role user sebagai placeholder
        await loadRoles(initialRoleId, currentRoleName);

        // Isi data user lain
        ud_name.textContent  = u.name ?? '-';
        ud_email.textContent = u.email ?? '-';
        ud_created_at.textContent = u.created_at ?? '-';

        if (u.status) {
          const statusText = u.status.charAt(0).toUpperCase() + u.status.slice(1);
          ud_status.innerHTML = `<span class="inline-block px-2 py-1 text-xs font-medium rounded ${badge(u.status)}">${statusText}</span>`;
        } else {
          ud_status.innerHTML = '-';
        }

        // Event listener untuk tombol update
        const sel = document.getElementById('ud_role');
        sel.addEventListener('change', () => {
          btnUpdateUser.disabled = (sel.value === initialRoleId || sel.value === '');
          btnUpdateUser.classList.toggle('opacity-50', btnUpdateUser.disabled);
          btnUpdateUser.classList.toggle('cursor-not-allowed', btnUpdateUser.disabled);
        });

        // Tampilkan isi modal
        userModalLoading.classList.add('hidden');
        userModalContent.classList.remove('hidden');
      } catch (err) {
        console.error(err);
        userModalLoading.classList.add('hidden');
        userModalError.classList.remove('hidden');
      }
    }



    btnUpdateUser.addEventListener('click', async () => {
      const newRoleId = ud_role.value;
      if (btnUpdateUser.disabled) return;

      btnUpdateUser.textContent = 'Updating...';
      btnUpdateUser.disabled = true;

      try {
        const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const res = await fetch(`/api/users/${currentUserId}`, {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token
          },
          body: JSON.stringify({ role_id: newRoleId })
        });

        if (!res.ok) throw new Error(await res.text());
        const data = await res.json();
        console.log('Updated:', data);

        // Refresh tabel & tutup modal
        closeUserModal();
        fetchData();

      } catch (err) {
        console.error(err);
        alert('Gagal memperbarui role.');
      } finally {
        btnUpdateUser.textContent = 'Update';
      }
    });


    // Initial load
    fetchData();
  </script>
</x-layout>