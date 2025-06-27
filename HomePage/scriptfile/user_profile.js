function viewOrderDetails(orderId) {
    // Redirect to order details page
    window.location.href = `order_details.php?id=${orderId}`;
}

// Add event listeners for edit profile button
document.addEventListener('DOMContentLoaded', function() {
    const editBtn = document.querySelector('.edit-btn');
    if (editBtn) {
        editBtn.addEventListener('click', function() {
            window.location.href = 'edit_profile.php';
        });
    }
});