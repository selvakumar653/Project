document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTables
    const dataTables = document.querySelectorAll('.data-table');
    
    dataTables.forEach(table => {
        // Simple client-side sorting/filtering
        const headers = table.querySelectorAll('th');
        headers.forEach((header, index) => {
            header.addEventListener('click', () => {
                sortTable(table, index);
            });
        });
    });

    // Search functionality
    const searchBoxes = document.querySelectorAll('.search-box input');
    searchBoxes.forEach(input => {
        input.addEventListener('input', function() {
            const table = this.closest('.section-header').nextElementSibling.querySelector('table');
            filterTable(table, this.value);
        });
    });

    // Category filter
    const categoryFilter = document.getElementById('category-filter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            const table = document.querySelector('.inventory-table-container table');
            const value = this.value.toLowerCase();
            
            table.querySelectorAll('tbody tr').forEach(row => {
                const category = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                if (value === 'all' || category.includes(value)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Modal handling
    const modalTriggers = document.querySelectorAll('[data-modal-target]');
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal-target');
            const modal = document.getElementById(modalId);
            modal.classList.add('active');
        });
    });

    document.querySelectorAll('.modal-close').forEach(closeBtn => {
        closeBtn.addEventListener('click', function() {
            this.closest('.modal').classList.remove('active');
        });
    });

    // Helper functions
    function sortTable(table, columnIndex) {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.sort((a, b) => {
            const aText = a.querySelector(`td:nth-child(${columnIndex + 1})`).textContent;
            const bText = b.querySelector(`td:nth-child(${columnIndex + 1})`).textContent;
            
            // Simple string comparison
            return aText.localeCompare(bText);
        });
        
        // Reverse if already sorted
        if (tbody.getAttribute('data-sorted') === columnIndex) {
            rows.reverse();
            tbody.removeAttribute('data-sorted');
        } else {
            tbody.setAttribute('data-sorted', columnIndex);
        }
        
        // Re-append sorted rows
        rows.forEach(row => tbody.appendChild(row));
    }
    
    function filterTable(table, searchText) {
        const search = searchText.toLowerCase();
        table.querySelectorAll('tbody tr').forEach(row => {
            const rowText = row.textContent.toLowerCase();
            row.style.display = rowText.includes(search) ? '' : 'none';
        });
    }

    // Bill actions
    document.querySelectorAll('.btn-view').forEach(btn => {
        btn.addEventListener('click', function() {
            const billId = this.getAttribute('data-bill-id');
            viewBill(billId);
        });
    });

    document.querySelectorAll('.btn-print').forEach(btn => {
        btn.addEventListener('click', function() {
            const billId = this.getAttribute('data-bill-id');
            printBill(billId);
        });
    });

    document.querySelectorAll('.btn-cancel').forEach(btn => {
        btn.addEventListener('click', function() {
            const billId = this.getAttribute('data-bill-id');
            cancelBill(billId);
        });
    });

    // User actions
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            editUser(userId);
        });
    });

    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            deleteUser(userId);
        });
    });

    // Inventory actions
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const itemId = this.getAttribute('data-item-id');
            editItem(itemId);
        });
    });

    document.querySelectorAll('.btn-stock').forEach(btn => {
        btn.addEventListener('click', function() {
            const itemId = this.getAttribute('data-item-id');
            updateStock(itemId);
        });
    });

    // Add event listeners for modal buttons
    document.getElementById('add-user-btn')?.addEventListener('click', showAddUserModal);
    document.getElementById('add-item-btn')?.addEventListener('click', showAddItemModal);
    document.getElementById('new-bill-btn')?.addEventListener('click', showNewBillModal);
});

// AJAX Functions
function viewBill(billId) {
    console.log(`Viewing bill ${billId}`);
    // Implement AJAX call to get bill details
}

function printBill(billId) {
    console.log(`Printing bill ${billId}`);
    // Implement print functionality
}

function cancelBill(billId) {
    if (confirm('Are you sure you want to cancel this bill?')) {
        console.log(`Canceling bill ${billId}`);
        // Implement AJAX call to cancel bill
    }
}

function editUser(userId) {
    console.log(`Editing user ${userId}`);
    // Implement AJAX call to get user details
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        console.log(`Deleting user ${userId}`);
        // Implement AJAX call to delete user
    }
}

function editItem(itemId) {
    console.log(`Editing item ${itemId}`);
    // Implement AJAX call to get item details
}

function updateStock(itemId) {
    console.log(`Updating stock for item ${itemId}`);
    // Implement stock update modal
}

function showAddUserModal() {
    console.log('Showing add user modal');
    // Implement modal display
}

function showAddItemModal() {
    console.log('Showing add item modal');
    // Implement modal display
}

function showNewBillModal() {
    console.log('Showing new bill modal');
    // Implement modal display
}