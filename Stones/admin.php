<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ADMIN') {
    header('Location: admin-login.php');
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sanctum - Ancient Stone Emporium</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@400;700;900&family=Cinzel:wght@400;600;700&family=IM+Fell+English:ital@0;1&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="admin-styles.css">

</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="Dr_Stone.php"><i class="fas fa-crown"></i> Admin Sanctum</a>
        <a href="logout.php" class="btn btn-logout ml-auto">
            <i class="fas fa-sign-out-alt mr-2"></i>Exit Sanctum
        </a>
    </div>
</nav>

<!-- ADMIN HEADER -->
<div class="admin-header">
    <h1>Guild Master's Sanctum</h1>
    <p>Oversee the vault and judge the worthiness of ancient relics</p>
    <button class="btn btn-info" data-toggle="modal" data-target="#orderHistoryModal" onclick="loadOrderHistory()">
        <i class="fas fa-history mr-2"></i>View Order History
    </button>
</div>

<!-- STATS -->
<div class="container mb-5">
    <div class="stats-grid">
        <div class="stat-card">
            <i class="fas fa-hourglass-half stat-icon"></i>
            <div class="stat-number" id="pendingCount">0</div>
            <div class="stat-label">Pending Proposals</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-check-circle stat-icon"></i>
            <div class="stat-number" id="approvedCount">0</div>
            <div class="stat-label">Approved Relics</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-gem stat-icon"></i>
            <div class="stat-number" id="totalStones">0</div>
            <div class="stat-label">Total Vault Items</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-user-shield stat-icon"></i>
            <div class="stat-number" id="vendorCount">0</div>
            <div class="stat-label">Active Vendors</div>
        </div>
    </div>
</div>

<!-- PENDING REQUESTS -->
<div class="container mb-5">
    <div class="section-header">
        <h2><i class="fas fa-scroll mr-3"></i>Pending Relic Proposals</h2>
    </div>

    <div id="requestsContainer" class="requests-grid">
        <div class="text-center py-5">
            <i class="fas fa-spinner fa-spin fa-3x text-muted"></i>
            <p class="mt-3">Loading proposals...</p>
        </div>
    </div>
</div>

    <!-- PROPOSAL DETAILS MODAL -->
    <div class="modal fade" id="proposalModal" tabindex="-1" role="dialog" aria-labelledby="proposalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="proposalLabel">Proposal Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="proposalModalBody" style="color:black">
                    <div class="row">
                        <div class="col-md-5">
                            <div id="modalProposalImage" class="mb-3 text-center"></div>
                        </div>
                        <div class="col-md-7">
                            <h4 id="modalProposalName" class="text-warning" style="font-weight:700;"></h4>
                            <p class="mb-1"><strong>Vendor:</strong> <span id="modalVendorName"></span></p>
                            <p class="mb-1"><strong>Email:</strong> <span id="modalVendorEmail"></span></p>
                            <p class="mb-1"><strong>Price:</strong> <span id="modalPrice"></span></p>
                            <p class="mb-1"><strong>Weight:</strong> <span id="modalWeight"></span></p>
                            <p class="mb-1"><strong>Origin:</strong> <span id="modalOrigin"></span></p>
                            <p class="mb-1"><strong>Era:</strong> <span id="modalEra"></span></p>
                            <p class="mb-1"><strong>Submitted:</strong> <span id="modalSubmitted"></span></p>
                        </div>
                    </div>
                    <hr>
                    <div>
                        <h6>Description</h6>
                        <p id="modalDescription" class="text-muted"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="modalRejectBtn">Reject</button>
                    <button type="button" class="btn btn-success" id="modalApproveBtn">Accept</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

<!-- ORDER HISTORY MODAL -->
<div class="modal fade" id="orderHistoryModal" tabindex="-1" role="dialog" aria-labelledby="orderHistoryLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="orderHistoryLabel">User Order History</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="orderHistoryContainer" style="color:black">
        <div class="text-center py-3">
            <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
            <p class="mt-3">Loading orders...</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
let pendingRequests = [];
let allOrders = [];

// Load proposals from backend
async function loadRequests() {
    try {
        const response = await fetch('controls.php?action=getProposals', { credentials: 'same-origin' });
        const data = await response.json();
        
        console.log('Proposals response:', data);
        
        if (data.success) {
            pendingRequests = data.proposals;
            updateStats();
            displayRequests();
        } else {
            showNotification('Failed to load proposals: ' + data.message, 'error');
        }
    } catch (error) {
        console.error('Error loading proposals:', error);
        showNotification('Error loading proposals. Please refresh the page.', 'error');
    }
}

// Update statistics
function updateStats() {
    document.getElementById('pendingCount').textContent = pendingRequests.length;
    
    // Count unique vendors
    const uniqueVendors = new Set(pendingRequests.map(r => r.vendor_email)).size;
    document.getElementById('vendorCount').textContent = uniqueVendors;
}

// Display requests with full details and image
function displayRequests() {
    const container = document.getElementById('requestsContainer');
    
    if (pendingRequests.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">No Pending Proposals</p>
            </div>
        `;
        return;
    }

    // Compact clickable cards
    container.innerHTML = pendingRequests.map(request => `
        <div class="card proposal-card mb-3 p-2" data-request-id="${request.id}" style="cursor:pointer;">
            <div class="d-flex align-items-center">
                <div style="width:72px;height:72px;flex:0 0 72px;overflow:hidden;border-radius:6px;background:#f5f5f5;display:flex;align-items:center;justify-content:center;margin-right:12px;">
                    ${request.image ? `<img src="${escapeHtml(request.image)}" alt="${escapeHtml(request.stone_name)}" style="width:100%;height:100%;object-fit:cover;">` : `<i class="fas fa-image fa-2x text-muted"></i>`}
                </div>
                <div style="flex:1;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <div style="font-weight:700;">${escapeHtml(request.stone_name)}</div>
                            <div class="small text-muted">${escapeHtml(request.vendor_name || 'Unknown vendor')}</div>
                        </div>
                        <div class="text-right small text-success">₹${parseFloat(request.price||0).toFixed(2)}</div>
                    </div>
                    <div class="small text-muted mt-1">Submitted: ${formatDate(request.created_at)}</div>
                </div>
            </div>
        </div>
    `).join('');

    // Attach click handlers to open modal with full details
    Array.from(container.querySelectorAll('.proposal-card')).forEach(card => {
        card.addEventListener('click', (e) => {
            const id = Number(card.getAttribute('data-request-id'));
            openProposalModal(id);
        });
    });
}

// Open modal and populate details
function openProposalModal(id) {
    const request = pendingRequests.find(r => Number(r.id) === Number(id));
    if (!request) return;

    const imageContainer = document.getElementById('modalProposalImage');
    if (request.image) {
        imageContainer.innerHTML = `<img src="${escapeHtml(request.image)}" alt="${escapeHtml(request.stone_name)}" class="img-fluid rounded">`;
    } else {
        imageContainer.innerHTML = `<div class="text-center p-5 bg-light rounded"><i class="fas fa-image fa-3x text-muted"></i><p class="mt-2 text-muted">No image</p></div>`;
    }

    document.getElementById('modalProposalName').textContent = request.stone_name || '';
    document.getElementById('modalVendorName').textContent = request.vendor_name || '';
    document.getElementById('modalVendorEmail').textContent = request.vendor_email || '';
    document.getElementById('modalPrice').textContent = '₹' + (Number(request.price)||0).toFixed(2);
    document.getElementById('modalWeight').textContent = request.weight || '';
    document.getElementById('modalOrigin').textContent = request.origin || '';
    document.getElementById('modalEra').textContent = request.era || '';
    document.getElementById('modalSubmitted').textContent = formatDate(request.created_at);
    document.getElementById('modalDescription').textContent = request.stone_description || 'No description provided.';

    // set up approve/reject handlers (one-time)
    const approveBtn = document.getElementById('modalApproveBtn');
    const rejectBtn = document.getElementById('modalRejectBtn');

    approveBtn.onclick = () => {
        $('#proposalModal').modal('hide');
        approveRequest(id);
    };

    rejectBtn.onclick = () => {
        $('#proposalModal').modal('hide');
        rejectRequest(id);
    };

    $('#proposalModal').modal('show');
}

// Approve proposal - adds to stones table and shows on index.php
async function approveRequest(id) {
    const request = pendingRequests.find(r => Number(r.id) === Number(id));
    if (!request) return;

    if (!confirm(`Approve "${request.stone_name}"?\n\nThis will add it to the vault.`)) {
        return;
    }

    try {
        const response = await fetch('controls.php?action=approveProposal', {
            method: 'POST',
            credentials: 'same-origin', // ✅ REQUIRED
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id })
        });

        const text = await response.text();
        console.log('RAW APPROVE RESPONSE:', text);

        if (text.trim().startsWith('<')) {
            throw new Error('Server returned HTML instead of JSON');
        }

        const data = JSON.parse(text);

        if (!data.success) {
            throw new Error(data.message || 'Approval failed');
        }

        showNotification(`✓ ${request.stone_name} approved and added to vault`, 'success');

        pendingRequests = pendingRequests.filter(r => Number(r.id) !== Number(id));
        updateStats();
        displayRequests();

        const approvedEl = document.getElementById('approvedCount');
        approvedEl.textContent = (Number(approvedEl.textContent) || 0) + 1;
        const totalEl = document.getElementById('totalStones');
        totalEl.textContent = (Number(totalEl.textContent) || 0) + 1;

    } catch (err) {
        console.error('APPROVE ERROR:', err);
        showNotification('✗ Failed to approve proposal', 'error');
    }
}

// Reject proposal
async function rejectRequest(id) {
    const request = pendingRequests.find(r => Number(r.id) === Number(id));
    if (!request) return;

    const reason = prompt(`Reject "${request.stone_name}"?\n\nOptional reason:`);
    if (reason === null) return;

    try {
        const response = await fetch('controls.php?action=rejectProposal', {
            method: 'POST',
            credentials: 'same-origin', // ✅ REQUIRED
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id,
                reason: reason || 'No reason provided'
            })
        });

        const text = await response.text();
        console.log('RAW REJECT RESPONSE:', text);

        if (text.trim().startsWith('<')) {
            throw new Error('Server returned HTML instead of JSON');
        }

        const data = JSON.parse(text);

        if (!data.success) {
            throw new Error(data.message || 'Rejection failed');
        }

        showNotification(`✗ ${request.stone_name} rejected`, 'reject');

        pendingRequests = pendingRequests.filter(r => Number(r.id) !== Number(id));
        updateStats();
        displayRequests();

    } catch (err) {
        console.error('REJECT ERROR:', err);
        showNotification('✗ Failed to reject proposal', 'error');
    }
}

// Load order history from backend
async function loadOrderHistory() {
    const container = document.getElementById('orderHistoryContainer');

    container.innerHTML = `
        <div class="text-center py-3">
            <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
            <p class="mt-3">Loading orders...</p>
        </div>
    `;

    try {
        const response = await fetch('controls.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'getOrders'
            })
        });

        const text = await response.text();
        console.log('RAW ORDER RESPONSE:', text);

        const data = JSON.parse(text);

        if (!data.success) {
            throw new Error(data.message);
        }

        const orders = data.orders || [];
        const sessionInfo = data.session || null;

        // Build optional debug header showing current session returned by the server
        let debugHtml = '';
        if (sessionInfo) {
            debugHtml = `
                <div class="mb-3">
                    <div class="alert alert-secondary small mb-0">
                        <strong>Session:</strong>
                        User ID: ${escapeHtml(String(sessionInfo.user_id ?? ''))} · Role: ${escapeHtml(String(sessionInfo.role ?? ''))}
                    </div>
                </div>`;
        }

        if (orders.length === 0) {
            container.innerHTML = debugHtml + `
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No orders yet.</p>
                </div>
            `;
            return;
        }

        // Render each order as a compact "slip" (receipt-style) showing user, time and net price
        container.innerHTML = debugHtml + orders.map(order => {
            const net = order.net_price ?? order.total ?? order.amount ?? order.price ?? 0;
            const created = order.created_at || '';
            const customer = `${order.first_name || ''} ${order.last_name || ''}`.trim() || (order.customer_name || 'Unknown');

            return `
            <div class="order-slip mb-3 p-3 border rounded bg-white">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div style="font-size:1.05rem;font-weight:600">${escapeHtml(customer)}</div>
                        <div class="text-muted small">${escapeHtml(order.email || '')}</div>
                        <div class="text-muted small">User ID: ${escapeHtml(String(order.user_id ?? ''))}</div>
                    </div>
                    <div class="text-right">
                        <div class="small text-muted">${escapeHtml(formatDate(created))}</div>
                        <div style="font-size:1.1rem;font-weight:700">${escapeHtml(formatCurrency(net))}</div>
                    </div>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="text-info">${escapeHtml(order.item_name || 'Item')}</div>
                        <div class="small text-muted">Status: ${escapeHtml(order.status || '')}</div>
                    </div>
                    <div class="text-right small text-muted">Order #${escapeHtml(String(order.id || ''))}</div>
                </div>
            </div>`;
        }).join('');

    } catch (err) {
        console.error('ORDER LOAD ERROR:', err);
        container.innerHTML = `
            <div class="alert alert-danger">
                Failed to load orders.
            </div>
        `;
    }
}

// Helper function to get status badge color
function getStatusBadge(status) {
    const badges = {
        'PENDING': 'warning',
        'PROCESSING': 'info',
        'COMPLETED': 'success',
        'CANCELLED': 'danger'
    };
    return badges[status] || 'secondary';
}

// Notifications
function showNotification(msg, type) {
    const notification = document.createElement('div');
    notification.className = 'ancient-notification';
    notification.textContent = msg;
    document.body.appendChild(notification);
    setTimeout(() => notification.classList.add('show'), 100);
    setTimeout(() => { 
        notification.classList.remove('show'); 
        setTimeout(() => notification.remove(), 400); 
    }, 4000);
}

// Logout
function logout() {
    window.location.replace("logout.php");
}

// Helper function to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Helper to format currency amounts
function formatCurrency(amount) {
    const num = Number(amount) || 0;
    return '₹' + num.toFixed(2);
}

// Helper function to format dates
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('en-IN', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    loadRequests();
});
</script>

</body>
</html>