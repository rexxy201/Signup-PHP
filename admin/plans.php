<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdminLogin();
$admin = getAdminUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Manage Plans — MangoNet Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
body{background:#f8f9fa}
.sidebar{width:220px;min-height:100vh;background:#1a1a2e;position:fixed;top:0;left:0;z-index:100;padding:0}
.sidebar-brand{padding:20px;color:#f97316;font-weight:800;font-size:20px;border-bottom:1px solid rgba(255,255,255,.1)}
.sidebar a{display:block;padding:12px 20px;color:rgba(255,255,255,.7);text-decoration:none;font-size:14px;transition:.2s}
.sidebar a:hover,.sidebar a.active{background:rgba(249,115,22,.15);color:#f97316}
.main-content{margin-left:220px;padding:24px}
@media(max-width:768px){.sidebar{display:none}.main-content{margin-left:0}}
.zone-label{font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;padding:2px 8px;border-radius:20px}
.zone-default{background:#f3f4f6;color:#374151}
.zone-oniru{background:#dbeafe;color:#1e40af}
.zone-abuja_banex{background:#dcfce7;color:#166534}
.category-Residential{background:#fef3c7;color:#92400e}
.category-Corporate{background:#ede9fe;color:#5b21b6}
</style>
</head>
<body>

<div class="sidebar">
  <div class="sidebar-brand">🥭 MangoNet</div>
  <nav class="mt-2">
    <a href="/admin/"><i class="bi bi-grid me-2"></i>Dashboard</a>
    <a href="/admin/plans.php" class="active"><i class="bi bi-list-check me-2"></i>Manage Plans</a>
    <a href="/admin/settings.php"><i class="bi bi-gear me-2"></i>Settings</a>
    <a href="/" target="_blank"><i class="bi bi-box-arrow-up-right me-2"></i>Signup Form</a>
    <a href="/admin/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
  </nav>
  <div style="position:absolute;bottom:16px;left:0;right:0;padding:0 20px">
    <div class="small text-white-50">Logged in as</div>
    <div class="small text-white fw-semibold"><?= htmlspecialchars($admin['username']) ?></div>
  </div>
</div>

<div class="main-content">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-0 fw-bold">Manage Plans</h4>
      <small class="text-muted">Add, edit or remove service plans by zone</small>
    </div>
    <button class="btn btn-warning fw-semibold" onclick="openAdd()"><i class="bi bi-plus-lg me-1"></i>Add Plan</button>
  </div>

  <!-- Filters -->
  <div class="d-flex gap-2 mb-3 flex-wrap">
    <select class="form-select form-select-sm w-auto" id="filterZone" onchange="applyFilter()">
      <option value="">All Zones</option>
      <option value="default">Rest of Lagos (Default)</option>
      <option value="oniru">Oniru</option>
      <option value="abuja_banex">Abuja / Banex</option>
    </select>
    <select class="form-select form-select-sm w-auto" id="filterCategory" onchange="applyFilter()">
      <option value="">All Categories</option>
      <option value="Residential">Residential</option>
      <option value="Corporate">Corporate</option>
    </select>
  </div>

  <div id="alertArea"></div>

  <div class="card border-0 shadow-sm">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0" id="plansTable">
        <thead class="table-light">
          <tr>
            <th>Name</th>
            <th>Price</th>
            <th>Speed</th>
            <th>Category</th>
            <th>Zone</th>
            <th>Status</th>
            <th>Order</th>
            <th></th>
          </tr>
        </thead>
        <tbody id="plansBody">
          <tr><td colspan="8" class="text-center py-4 text-muted">Loading…</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="planModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Add Plan</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="editId">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label small fw-semibold">Plan Name *</label>
            <input class="form-control" id="fName" placeholder="Mango Basic">
          </div>
          <div class="col-6">
            <label class="form-label small fw-semibold">Price *</label>
            <input class="form-control" id="fPrice" placeholder="NGN 14,067">
          </div>
          <div class="col-6">
            <label class="form-label small fw-semibold">Speed *</label>
            <input class="form-control" id="fSpeed" placeholder="25Mbps">
          </div>
          <div class="col-6">
            <label class="form-label small fw-semibold">Category</label>
            <select class="form-select" id="fCategory">
              <option value="Residential">Residential</option>
              <option value="Corporate">Corporate</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small fw-semibold">Location Zone</label>
            <select class="form-select" id="fZone">
              <option value="default">Rest of Lagos (Default)</option>
              <option value="oniru">Oniru</option>
              <option value="abuja_banex">Abuja / Banex</option>
            </select>
          </div>
          <div class="col-6">
            <label class="form-label small fw-semibold">Sort Order</label>
            <input class="form-control" type="number" id="fOrder" value="0">
          </div>
          <div class="col-6 d-flex align-items-end">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="fActive" checked>
              <label class="form-check-label small" for="fActive">Active (visible to customers)</label>
            </div>
          </div>
        </div>
        <div id="modalError" class="alert alert-danger mt-3" style="display:none"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-warning fw-semibold" id="saveBtn" onclick="savePlan()">Save Plan</button>
      </div>
    </div>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title">Delete Plan</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Delete <strong id="deleteName"></strong>? This cannot be undone.</p>
      </div>
      <div class="modal-footer border-0">
        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-danger btn-sm" id="confirmDeleteBtn">Delete</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
let allPlans = [];
let deleteId = null;
const planModal = new bootstrap.Modal(document.getElementById('planModal'));
const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

const ZONE_LABELS = {
  default: 'Rest of Lagos',
  oniru: 'Oniru',
  abuja_banex: 'Abuja / Banex'
};

async function loadPlans() {
  const res = await fetch('/api/admin-plans.php');
  allPlans = await res.json();
  applyFilter();
}

function applyFilter() {
  const zone = document.getElementById('filterZone').value;
  const cat  = document.getElementById('filterCategory').value;
  let plans = allPlans;
  if (zone) plans = plans.filter(p => p.location_zone === zone);
  if (cat)  plans = plans.filter(p => p.category === cat);
  renderTable(plans);
}

function renderTable(plans) {
  const tbody = document.getElementById('plansBody');
  if (!plans.length) {
    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">No plans found</td></tr>';
    return;
  }
  tbody.innerHTML = plans.map(p => `
    <tr>
      <td class="fw-semibold">${esc(p.name)}</td>
      <td>${esc(p.price)}<span class="text-muted small">/mo</span></td>
      <td>${esc(p.speed)}</td>
      <td><span class="zone-label category-${esc(p.category)}">${esc(p.category)}</span></td>
      <td><span class="zone-label zone-${esc(p.location_zone)}">${esc(ZONE_LABELS[p.location_zone] || p.location_zone)}</span></td>
      <td>${p.is_active == 1
        ? '<span class="badge bg-success">Active</span>'
        : '<span class="badge bg-secondary">Inactive</span>'}</td>
      <td>${p.sort_order}</td>
      <td class="text-end">
        <button class="btn btn-sm btn-outline-secondary me-1" onclick="openEdit(${p.id})"><i class="bi bi-pencil"></i></button>
        <button class="btn btn-sm btn-outline-danger" onclick="openDelete(${p.id},'${esc(p.name)}')"><i class="bi bi-trash"></i></button>
      </td>
    </tr>`).join('');
}

function esc(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function openAdd() {
  document.getElementById('editId').value = '';
  document.getElementById('modalTitle').textContent = 'Add Plan';
  document.getElementById('fName').value = '';
  document.getElementById('fPrice').value = '';
  document.getElementById('fSpeed').value = '';
  document.getElementById('fCategory').value = 'Residential';
  document.getElementById('fZone').value = 'default';
  document.getElementById('fOrder').value = '0';
  document.getElementById('fActive').checked = true;
  document.getElementById('modalError').style.display = 'none';
  planModal.show();
}

function openEdit(id) {
  const p = allPlans.find(x => x.id == id);
  if (!p) return;
  document.getElementById('editId').value = p.id;
  document.getElementById('modalTitle').textContent = 'Edit Plan';
  document.getElementById('fName').value = p.name;
  document.getElementById('fPrice').value = p.price;
  document.getElementById('fSpeed').value = p.speed;
  document.getElementById('fCategory').value = p.category;
  document.getElementById('fZone').value = p.location_zone;
  document.getElementById('fOrder').value = p.sort_order;
  document.getElementById('fActive').checked = p.is_active == 1;
  document.getElementById('modalError').style.display = 'none';
  planModal.show();
}

function openDelete(id, name) {
  deleteId = id;
  document.getElementById('deleteName').textContent = name;
  deleteModal.show();
}

async function savePlan() {
  const id    = document.getElementById('editId').value;
  const errEl = document.getElementById('modalError');
  const btn   = document.getElementById('saveBtn');
  errEl.style.display = 'none';
  btn.disabled = true; btn.textContent = 'Saving…';

  const body = {
    name:          document.getElementById('fName').value.trim(),
    price:         document.getElementById('fPrice').value.trim(),
    speed:         document.getElementById('fSpeed').value.trim(),
    category:      document.getElementById('fCategory').value,
    location_zone: document.getElementById('fZone').value,
    sort_order:    parseInt(document.getElementById('fOrder').value) || 0,
    is_active:     document.getElementById('fActive').checked ? 1 : 0,
  };

  const url    = id ? `/api/admin-plans.php?id=${id}` : '/api/admin-plans.php';
  const method = id ? 'PUT' : 'POST';

  try {
    const res  = await fetch(url, { method, headers: {'Content-Type':'application/json'}, body: JSON.stringify(body) });
    const data = await res.json();
    if (!res.ok) throw new Error(data.error || 'Save failed');
    planModal.hide();
    showAlert('Plan saved successfully.', 'success');
    await loadPlans();
  } catch(e) {
    errEl.textContent = e.message; errEl.style.display = 'block';
  } finally {
    btn.disabled = false; btn.textContent = 'Save Plan';
  }
}

document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
  const btn = document.getElementById('confirmDeleteBtn');
  btn.disabled = true; btn.textContent = 'Deleting…';
  try {
    const res = await fetch(`/api/admin-plans.php?id=${deleteId}`, { method: 'DELETE' });
    const data = await res.json();
    if (!res.ok) throw new Error(data.error || 'Delete failed');
    deleteModal.hide();
    showAlert('Plan deleted.', 'warning');
    await loadPlans();
  } catch(e) {
    alert(e.message);
  } finally {
    btn.disabled = false; btn.textContent = 'Delete';
  }
});

function showAlert(msg, type) {
  const el = document.getElementById('alertArea');
  el.innerHTML = `<div class="alert alert-${type} alert-dismissible">${msg}<button class="btn-close" data-bs-dismiss="alert"></button></div>`;
  setTimeout(() => el.innerHTML = '', 4000);
}

loadPlans();
</script>
</body>
</html>
