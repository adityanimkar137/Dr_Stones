<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ADMIN') {
    header('Location: admin-login.html');
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
        <button class="btn btn-logout ml-auto" onclick="logout()">
            <i class="fas fa-sign-out-alt mr-2"></i>Exit Sanctum
        </button>
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

    <div id="requestsContainer">
        <div class="text-center py-5">
            <i class="fas fa-spinner fa-spin fa-3x text-muted"></i>
            <p class="mt-3">Loading proposals...</p>
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
        const response = await fetch('controls.php?action=getProposals');
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

    container.innerHTML = pendingRequests.map(request => `
        <div class="request-card" data-request-id="${request.id}">
            <div class="row">
                <div class="col-md-4">
                    <div class="proposal-image-container mb-3">
                        ${request.image ? `
    <img src="${escapeHtml(request.image)}"
         alt="${escapeHtml(request.stone_name)}"
         class="img-fluid rounded"
         style="width: 100%; max-height: 300px; object-fit: cover;">
` : `
                            <div class="text-center p-5 bg-light rounded">
                                <i class="fas fa-image fa-3x text-muted"></i>
                                <p class="mt-2 text-muted">No image</p>
                            </div>
                        `}
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h4 class="mb-2 text-warning">${escapeHtml(request.stone_name)}</h4>
                            <span class="badge badge-info">Proposal #${request.id}</span>
                        </div>
                        <div>
                            <button class="btn btn-reject mb-2" onclick="rejectRequest(${request.id})">
                                <i class="fas fa-times mr-1"></i>Reject
                            </button>
                            <button class="btn btn-approve" onclick="approveRequest(${request.id})">
                                <i class="fas fa-check mr-1"></i>Approve
                            </button>
                        </div>
                    </div>

                    <div class="proposal-details">
                        <div class="row mb-2">
                            <div class="col-6">
                                <strong><i class="fas fa-user mr-2"></i>Vendor:</strong>
                                <p class="mb-0">${escapeHtml(request.vendor_name || 'N/A')}</p>
                            </div>
                            <div class="col-6">
                                <strong><i class="fas fa-envelope mr-2"></i>Email:</strong>
                                <p class="mb-0">${escapeHtml(request.vendor_email || 'N/A')}</p>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-6">
                                <strong><i class="fas fa-coins mr-2"></i>Price:</strong>
                                <p class="mb-0 text-success">₹${parseFloat(request.price).toFixed(2)}</p>
                            </div>
                            <div class="col-6">
                                <strong><i class="fas fa-weight mr-2"></i>Weight:</strong>
                                <p class="mb-0">${escapeHtml(request.weight || 'N/A')}</p>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-6">
                                <strong><i class="fas fa-map-marked-alt mr-2"></i>Origin:</strong>
                                <p class="mb-0">${escapeHtml(request.origin || 'N/A')}</p>
                            </div>
                            <div class="col-6">
                                <strong><i class="fas fa-calendar-alt mr-2"></i>Era:</strong>
                                <p class="mb-0">${escapeHtml(request.era || 'N/A')}</p>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-12">
                                <strong><i class="fas fa-scroll mr-2"></i>Description:</strong>
                                <p class="mb-0 text-muted" style="max-height: 100px; overflow-y: auto;">
                                    ${escapeHtml(request.stone_description || 'No description provided')}
                                </p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <small class="text-muted">
                                    <i class="fas fa-clock mr-1"></i>
                                    Submitted: ${formatDate(request.created_at)}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

// Approve proposal - adds to stones table and shows on index.php
async function approveRequest(id) {
    const request = pendingRequests.find(r => r.id === id);
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

        pendingRequests = pendingRequests.filter(r => r.id !== id);
        updateStats();
        displayRequests();

        document.getElementById('approvedCount').textContent++;
        document.getElementById('totalStones').textContent++;

    } catch (err) {
        console.error('APPROVE ERROR:', err);
        showNotification('✗ Failed to approve proposal', 'error');
    }
}

// Reject proposal
async function rejectRequest(id) {
    const request = pendingRequests.find(r => r.id === id);
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

        pendingRequests = pendingRequests.filter(r => r.id !== id);
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

        const orders = data.orders;

        if (orders.length === 0) {
            container.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No orders yet.</p>
                </div>
            `;
            return;
        }

        container.innerHTML = orders.map(order => `
            <div class="order-card mb-3 p-3 border rounded">
                <strong>${order.first_name} ${order.last_name}</strong>
                <br>
                <span>${order.email}</span>
                <br>
                <span class="text-info">${order.item_name}</span>
                <br>
                <small>${order.created_at}</small>
                <span class="badge badge-warning float-right">${order.status}</span>
            </div>
        `).join('');

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
    sessionStorage.removeItem("isAdmin");
    window.location.replace("admin-login.html");
}

// Helper function to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
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