let currentLanguage = 'en';
function changeLanguage(lang) {
    currentLanguage = lang;
    document.documentElement.lang = lang;
    updateContent();
}
function updateContent() {
    document.querySelectorAll('.nav-item').forEach(item => {
        const key = item.dataset.section;
        if (translations[currentLanguage][key]) {
            const icon = item.innerHTML.split('</i>')[0] + '</i> ';
            item.innerHTML = icon + translations[currentLanguage][key];
        }
    });
    document.getElementById('searchInput').placeholder = translations[currentLanguage].searchPlaceholder;
    document.querySelector('.section-title').textContent = translations[currentLanguage].signatureDishes;
    document.querySelectorAll('.availability-badge span').forEach(badge => {
        if (badge.classList.contains('in-stock')) {
            if (badge.classList.contains('low')) {
                const count = badge.textContent.match(/\d+/)[0];
                badge.textContent = translations[currentLanguage].onlyLeft.replace('{count}', count);
            } else {
                badge.textContent = translations[currentLanguage].inStock;
            }
        } else {
            badge.textContent = translations[currentLanguage].outOfStock;
        }
    });
}
const style = document.createElement('style');
style.textContent = `
    .language-selector {
        margin-left: auto;
        padding: 0 15px;
    }
    .language-selector select {
        padding: 5px 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background: white;
        font-size: 14px;
        cursor: pointer;
    }
    .language-selector select:focus {
        outline: none;
        border-color: #666;
    }
`;
document.head.appendChild(style);
document.addEventListener('DOMContentLoaded', () => {
    changeLanguage('en');
});
let cart = [];
let cartTotal = 0;
const cartPreview = document.getElementById('cartPreview');
const cartCount = document.getElementById('cartCount');
const billModal = document.getElementById('billModal');
const billContainer = document.getElementById('billContainer');
const closeBill = document.getElementById('closeBill');
const billItems = document.getElementById('billItems');
const billTotal = document.getElementById('billTotal');
const clearCartBtn = document.getElementById('clearCart');
const checkoutBtn = document.getElementById('checkoutBtn');
const addToCartBtns = document.querySelectorAll('.add-to-cart');
const locationModal = document.getElementById('locationModal');
const locationContainer = document.getElementById('locationContainer');
const closeLocation = document.getElementById('closeLocation');
const monthlyOffers = document.getElementById('monthlyOffers');
let offersVisible = false;
const aboutSection = document.getElementById('about');
const contactSection = document.getElementById('contact');
const homeSection = document.querySelector('.menu-grid');
const navItems = document.querySelectorAll('.nav-item');
const footerLinks = document.querySelectorAll('.footer-links a');
const contactForm = document.getElementById('contactForm');
const submitBtn = document.getElementById('submitBtn');
const successMessage = document.getElementById('successMessage');
addToCartBtns.forEach((btn, index) => {
    btn.addEventListener('click', async function() {
        const menuItem = this.closest('.menu-item');
        const itemName = menuItem.querySelector('.menu-item-title').textContent;
        const itemPrice = parseInt(menuItem.querySelector('.menu-item-price').textContent);
        const badge = menuItem.querySelector('.availability-badge');
        const stockQuantity = parseInt(badge.dataset.stock);
        if (stockQuantity <= 0) {
            showAlert('error', `${itemName} is out of stock`);
            return;
        }
        const existingItem = cart.find(item => item.name === itemName);
        const currentQuantity = existingItem ? existingItem.quantity : 0;
        if (currentQuantity >= stockQuantity) {
            showAlert('warning', `Cannot add more ${itemName}. Only ${stockQuantity} available.`);
            return;
        }
        if (existingItem) {
            existingItem.quantity++;
            existingItem.total = existingItem.quantity * existingItem.price;
        } else {
            cart.push({
                name: itemName,
                price: itemPrice,
                quantity: 1,
                total: itemPrice
            });
        }
        cartTotal += itemPrice;
        const newStock = stockQuantity - 1;
        badge.dataset.stock = newStock;
        updateStockDisplay(menuItem, newStock);
        updateCartUI();
        showAlert('success', `${itemName} added to cart!`);
    });
});
function updateStockDisplay(menuItem, newStock) {
    const badge = menuItem.querySelector('.availability-badge');
    const stockDisplay = badge.querySelector('span');
    if (newStock <= 0) {
        stockDisplay.className = 'out-of-stock';
        stockDisplay.textContent = 'Out of Stock';
        menuItem.querySelector('.add-to-cart').disabled = true;
    } else if (newStock <= 5) {
        stockDisplay.className = 'in-stock low';
        stockDisplay.textContent = `Only ${newStock} left`;
    } else {
        stockDisplay.className = 'in-stock';
        stockDisplay.textContent = `In Stock (${newStock})`;
    }
}
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 
                         type === 'warning' ? 'exclamation-triangle' : 
                         'times-circle'}"></i>
        <span>${message}</span>
    `;
    document.body.appendChild(alertDiv);
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}
checkoutBtn.addEventListener('click', async function() {
    const selectedOption = document.querySelector('input[name="diningOption"]:checked');
    if (!selectedOption) {
        showAlert('error', 'Please select a dining option');
        return;
    }
    if (selectedOption.value === 'room' && !roomNumber.value) {
        showAlert('error', 'Please enter a room number');
        roomNumber.focus();
        return;
    }
    if (selectedOption.value === 'table' && !tableNumber.value) {
        showAlert('error', 'Please enter a table number');
        tableNumber.focus();
        return;
    }
    if (selectedOption.value === 'takeaway') {
        if (!customerName.value.trim()) {
            showAlert('error', 'Please enter your name');
            customerName.focus();
            return;
        }
        if (!phoneNumber.value || phoneNumber.value.length !== 10) {
            showAlert('error', 'Please enter a valid 10-digit phone number');
            phoneNumber.focus();
            return;
        }
        if (!arrivalTime.value) {
            showAlert('error', 'Please select your pickup time');
            arrivalTime.focus();
            return;
        }
    }
    const diningDetails = {
        type: selectedOption.value,
        location: selectedOption.value === 'room' ? roomNumber.value :
                 selectedOption.value === 'table' ? tableNumber.value : 'waiting',
        customerInfo: selectedOption.value === 'takeaway' ? {
            name: customerName.value.trim(),
            phone: phoneNumber.value,
            arrivalTime: arrivalTime.value
        } : {
            name: 'Guest',
            phone: null,
            arrivalTime: null
        }
    };
    const orderData = {
        items: cart,
        quantity: cart.reduce((sum, item) => sum + item.quantity, 0),
        totalAmount: cartTotal,
        dining: diningDetails
    };
    try {
        const response = await fetch('verify_stock.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ items: cart })
        });
        const responseText = await response.text();
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (e) {
            showAlert('error', 'Server error. Please try again later.');
            return;
        }
        if (!data.success) {
            showAlert('error', data.message);
            if (data.unavailableItems) {
                updateCartWithAvailableStock(data.unavailableItems);
            }
            return;
        }
        const orderResult = await sendOrderToServer(orderData);
        if (orderResult.success) {
            const waitingIdDiv = document.getElementById('waitingId');
            const idNumber = waitingIdDiv.querySelector('.id-number');
            idNumber.textContent = orderResult.waitingId;
            waitingIdDiv.style.display = 'block';
            checkoutBtn.style.display = 'none';
            showAlert('success', 'Order placed successfully!');
            const diningOptions = document.querySelectorAll('input[name="diningOption"]');
            diningOptions.forEach(option => option.disabled = true);
            setTimeout(() => {
                cart = [];
                cartTotal = 0;
                updateCartUI();
                billModal.style.display = 'none';
                waitingIdDiv.style.display = 'none';
                checkoutBtn.style.display = 'block';
                diningOptions.forEach(option => option.disabled = false);
            }, 5000);
        } else {
            showAlert('error', orderResult.error || 'Order placement failed.');
        }
    } catch (error) {
        showAlert('error', 'An error occurred. Please try again.');
    }
});
function updateCartWithAvailableStock(unavailableItems) {
    let cartUpdated = false;
    unavailableItems.forEach(item => {
        const cartItem = cart.find(ci => ci.name === item.name);
        if (cartItem) {
            if (!item.available || item.stock === 0) {
                cart = cart.filter(ci => ci.name !== item.name);
                cartUpdated = true;
            } else if (cartItem.quantity > item.stock) {
                cartItem.quantity = item.stock;
                cartItem.total = cartItem.quantity * cartItem.price;
                cartUpdated = true;
            }
        }
    });
    if (cartUpdated) {
        cartTotal = cart.reduce((sum, item) => sum + item.total, 0);
        updateCartUI();
        showAlert('warning', 'Cart updated due to stock changes');
    }
}
function updateAvailabilityBadges() {
    const badges = document.querySelectorAll('.availability-badge');
    badges.forEach(badge => {
        const stock = parseInt(badge.dataset.stock);
        const available = badge.dataset.available === "true";
        if (!available || stock <= 0) {
            badge.innerHTML = '<span class="out-of-stock">Out of Stock</span>';
            badge.closest('.menu-item').dataset.available = "false";
        } else {
            if (stock <= 5) {
                badge.dataset.stock = "low";
            }
            badge.innerHTML = `<span class="in-stock">In Stock (${stock})</span>`;
        }
    });
}
document.addEventListener('DOMContentLoaded', updateAvailabilityBadges);
function updateCartUI() {
    const itemCount = cart.reduce((total, item) => total + item.quantity, 0);
    cartCount.textContent = itemCount;
    renderBill();
}
function renderBill() {
    billItems.innerHTML = '';
    if (cart.length === 0) {
        billItems.innerHTML = `
            <div class="empty-cart">
                <i class="fas fa-shopping-basket"></i>
                <p>Your cart is empty</p>
            </div>
        `;
        billTotal.textContent = '₹0';
        return;
    }
    cart.forEach(item => {
        const billItem = document.createElement('div');
        billItem.className = 'bill-item';
        billItem.innerHTML = `
            <div class="bill-item-name">${item.name}</div>
            <div class="bill-item-details">
                <span class="bill-item-quantity">Qty: ${item.quantity}</span>
                <span class="bill-item-price">Price: ₹${item.price}</span>
                <span class="bill-item-total">Total: ₹${item.total}</span>
            </div>
        `;
        billItems.appendChild(billItem);
    });
    const total = cart.reduce((sum, item) => sum + item.total, 0);
    billTotal.textContent = `₹${total}`;
}
cartPreview.addEventListener('click', function() {
    billModal.style.display = 'flex';
    setTimeout(() => {
        billContainer.style.transform = 'translateY(0)';
    }, 10);
});
closeBill.addEventListener('click', function() {
    billContainer.style.transform = 'translateY(20px)';
    setTimeout(() => {
        billModal.style.display = 'none';
    }, 300);
});
billModal.addEventListener('click', function(e) {
    if (e.target === billModal) {
        billContainer.style.transform = 'translateY(20px)';
        setTimeout(() => {
            billModal.style.display = 'none';
        }, 300);
    }
});
clearCartBtn.addEventListener('click', function() {
    if (cart.length === 0) return;
    this.innerHTML = '<i class="fas fa-check"></i> Cleared!';
    this.style.background = 'linear-gradient(135deg, var(--success), #2ecc71)';
    cart = [];
    cartTotal = 0;
    updateCartUI();
    setTimeout(() => {
        this.innerHTML = '<i class="fas fa-trash-alt"></i> Clear Cart';
        this.style.background = '#f5f5f5';
    }, 1500);
});
function openLocationModal() {
    locationModal.style.display = 'flex';
    setTimeout(() => {
        locationContainer.style.transform = 'translateY(0)';
    }, 10);
}
closeLocation.addEventListener('click', function() {
    locationContainer.style.transform = 'translateY(20px)';
    setTimeout(() => {
        locationModal.style.display = 'none';
    }, 300);
});
locationModal.addEventListener('click', function(e) {
    if (e.target === locationModal) {
        locationContainer.style.transform = 'translateY(20px)';
        setTimeout(() => {
            locationModal.style.display = 'none';
        }, 300);
    }
});
function toggleOffers() {
    offersVisible = !offersVisible;
    monthlyOffers.style.display = offersVisible ? 'block' : 'none';
    if (offersVisible) {
        monthlyOffers.scrollIntoView({ behavior: 'smooth' });
    }
}
function handleNavigation(section) {
    homeSection.style.display = 'none';
    monthlyOffers.style.display = 'none';
    aboutSection.style.display = 'none';
    contactSection.style.display = 'none';
    navItems.forEach(item => {
        item.classList.remove('active');
        if (item.getAttribute('data-section') === section) {
            item.classList.add('active');
        }
    });
    switch(section) {
        case 'home':
            homeSection.style.display = 'grid';
            break;
        case 'offers':
            homeSection.style.display = 'grid';
            monthlyOffers.style.display = 'block';
            offersVisible = true;
            monthlyOffers.scrollIntoView({ behavior: 'smooth' });
            break;
        case 'location':
            homeSection.style.display = 'grid';
            openLocationModal();
            break;
        case 'about':
            aboutSection.style.display = 'block';
            aboutSection.scrollIntoView({ behavior: 'smooth' });
            break;
        case 'contact':
            contactSection.style.display = 'block';
            contactSection.scrollIntoView({ behavior: 'smooth' });
            break;
    }
}
navItems.forEach(item => {
    item.addEventListener('click', function(e) {
        e.preventDefault();
        const section = this.getAttribute('data-section');
        handleNavigation(section);
    });
});
footerLinks.forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const section = this.getAttribute('data-section');
        handleNavigation(section);
    });
});
if (contactForm) {
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const messageInput = document.getElementById('message');
    let isValid = true;
    const validateField = (field, validator) => {
        if (!field) return;
        field.addEventListener('input', () => {
            const group = field.closest('.form-group');
            if (validator(field.value.trim())) {
                group.classList.remove('error');
                group.classList.add('success');
            } else {
                group.classList.remove('success');
            }
        });
    };
    validateField(nameInput, value => value.length > 0);
    validateField(emailInput, value => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value));
    validateField(messageInput, value => value.length > 0);
    const formGroups = document.querySelectorAll('.form-group');
    formGroups.forEach(group => {
        const input = group.querySelector('input, textarea');
        if (input) {
            input.addEventListener('focus', () => {
                group.classList.add('focused');
            });
            input.addEventListener('blur', () => {
                if (!input.value) {
                    group.classList.remove('focused');
                }
            });
            if (input.value) {
                group.classList.add('focused');
            }
        }
    });
    contactForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        submitBtn.classList.remove('loading');
        isValid = true;
        document.querySelectorAll('.form-group').forEach(group => {
            group.classList.remove('error');
        });
        if (!nameInput.value.trim()) {
            nameInput.closest('.form-group').classList.add('error');
            isValid = false;
        }
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailInput.value.trim())) {
            emailInput.closest('.form-group').classList.add('error');
            isValid = false;
        }
        if (!messageInput.value.trim()) {
            messageInput.closest('.form-group').classList.add('error');
            isValid = false;
        }
        if (!isValid) {
            const errorFields = document.querySelectorAll('.form-group.error');
            errorFields.forEach(field => {
                field.style.animation = 'none';
                setTimeout(() => {
                    field.style.animation = 'shake 0.5s cubic-bezier(.36,.07,.19,.97) both';
                }, 10);
            });
            return;
        }
        submitBtn.classList.add('loading');
        try {
            await new Promise(resolve => setTimeout(resolve, 1500));
            successMessage.classList.add('show');
            contactForm.reset();
            formGroups.forEach(group => {
                group.classList.remove('focused');
            });
            setTimeout(() => {
                successMessage.classList.remove('show');
            }, 5000);
        } catch (error) {
            alert('There was an error submitting your form. Please try again.');
        } finally {
            submitBtn.classList.remove('loading');
        }
    });
}
const animateOnScroll = () => {
    const elements = document.querySelectorAll('.menu-item, .monthly-offers, .section-title, .about-section, .contact-section');
    elements.forEach(element => {
        const elementPosition = element.getBoundingClientRect().top;
        const screenPosition = window.innerHeight / 1.2;
        if (elementPosition < screenPosition) {
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }
    });
};
document.querySelectorAll('.menu-item, .monthly-offers, .about-section, .contact-section').forEach(element => {
    element.style.opacity = '0';
    element.style.transform = 'translateY(30px)';
    element.style.transition = 'all 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
});
window.addEventListener('scroll', animateOnScroll);
window.addEventListener('load', animateOnScroll);
function sendOrderToServer(orderDetails) {
    const formattedOrder = {
        items: orderDetails.items.map(item => ({
            name: item.name,
            quantity: item.quantity,
            price: item.price,
            total: item.total
        })),
        quantity: orderDetails.quantity,
        totalAmount: orderDetails.totalAmount,
        dining: {
            type: orderDetails.dining.type,
            location: orderDetails.dining.location,
            customerInfo: orderDetails.dining.customerInfo ? {
                name: orderDetails.dining.customerInfo.name,
                phone: orderDetails.dining.customerInfo.phone,
                arrivalTime: orderDetails.dining.customerInfo.arrivalTime
            } : null
        }
    };
    return fetch('test.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(formattedOrder),
        credentials: 'same-origin'
    })
    .then(async response => {
        const responseText = await response.text();
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}, response: ${responseText}`);
        }
        try {
            const data = JSON.parse(responseText);
            const waitingIdElement = document.getElementById('waitingId');
            if (data.showWaitingId && waitingIdElement) {
                waitingIdElement.style.display = 'block';
                waitingIdElement.querySelector('.id-number').textContent = data.waitingId;
            } else if (waitingIdElement) {
                waitingIdElement.style.display = 'none';
            }
            return data;
        } catch (e) {
            throw new Error('Invalid JSON response from server');
        }
    })
    .catch(error => {
        return {
            success: false,
            error: error.message || 'Failed to process order'
        };
    });
}
const searchInput = document.getElementById('searchInput');
const menuItems = document.querySelectorAll('.menu-item');
let noResultsDiv;
function createNoResultsDiv() {
    noResultsDiv = document.createElement('div');
    noResultsDiv.className = 'no-results';
    noResultsDiv.innerHTML = '<i class="fas fa-search"></i><p>No menu items found</p>';
    document.querySelector('.menu-grid').after(noResultsDiv);
}
createNoResultsDiv();
function searchMenuItems() {
    const searchTerm = searchInput.value.toLowerCase();
    let hasResults = false;
    menuItems.forEach(item => {
        const itemName = item.querySelector('.menu-item-title').textContent.toLowerCase();
        const itemDesc = item.querySelector('.menu-item-desc').textContent.toLowerCase();
        if (itemName.includes(searchTerm) || itemDesc.includes(searchTerm)) {
            item.style.display = 'block';
            item.style.animation = 'fadeIn 0.5s ease-out';
            hasResults = true;
        } else {
            item.style.display = 'none';
        }
    });
    noResultsDiv.style.display = hasResults ? 'none' : 'block';
}
searchInput.addEventListener('input', debounce(searchMenuItems, 300));
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
navItems.forEach(item => {
    item.addEventListener('click', () => {
        searchInput.value = '';
        searchMenuItems();
    });
});
const categoryBtns = document.querySelectorAll('.category-btn');
categoryBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        categoryBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const category = btn.dataset.category;
        menuItems.forEach(item => {
            if (category === 'all' || item.dataset.category === category) {
                item.style.display = 'block';
                item.style.animation = 'fadeIn 0.5s ease-out';
            } else {
                item.style.display = 'none';
            }
        });
    });
});
document.addEventListener('DOMContentLoaded', function() {
    const menuImages = document.querySelectorAll('.menu-item-img');
    menuImages.forEach(img => {
        img.classList.add('loading');
        img.addEventListener('load', function() {
            this.classList.remove('loading');
        });
    });
});
const roomDelivery = document.getElementById('roomDelivery');
const tableService = document.getElementById('tableService');
const takeaway = document.getElementById('takeaway');
const roomNumber = document.getElementById('roomNumber');
const tableNumber = document.getElementById('tableNumber');
const takeawayDetails = document.getElementById('takeawayDetails');
const customerName = document.getElementById('customerName');
const phoneNumber = document.getElementById('phoneNumber');
const arrivalTime = document.getElementById('arrivalTime');
function handleDiningOptionChange() {
    roomNumber.disabled = true;
    tableNumber.disabled = true;
    customerName.disabled = true;
    phoneNumber.disabled = true;
    arrivalTime.disabled = true;
    takeawayDetails.style.display = 'none';
    roomNumber.value = '';
    tableNumber.value = '';
    customerName.value = '';
    phoneNumber.value = '';
    arrivalTime.value = '';
    if (roomDelivery.checked) {
        roomNumber.disabled = false;
        roomNumber.focus();
    } else if (tableService.checked) {
        tableNumber.disabled = false;
        tableNumber.focus();
    } else if (takeaway.checked) {
        takeawayDetails.style.display = 'block';
        customerName.disabled = false;
        phoneNumber.disabled = false;
        arrivalTime.disabled = false;
        customerName.focus();
    }
}
phoneNumber.addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
    if (this.value.length > 10) {
        this.value = this.value.slice(0, 10);
    }
});
arrivalTime.addEventListener('change', function() {
    const now = new Date();
    const selected = new Date();
    const [hours, minutes] = this.value.split(':');
    selected.setHours(hours, minutes, 0);
    const minTime = new Date(now.getTime() + 30 * 60000);
    if (selected < minTime) {
        alert('Please select a time at least 30 minutes from now');
        this.value = '';
    }
});
roomDelivery.addEventListener('change', handleDiningOptionChange);
tableService.addEventListener('change', handleDiningOptionChange);
takeaway.addEventListener('change', handleDiningOptionChange);
roomNumber.addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});
tableNumber.addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});
document.addEventListener('DOMContentLoaded', function() {
    const mealTabs = document.querySelectorAll('.meal-tab');
    const mealSections = document.querySelectorAll('.meal-section');
    mealTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            mealTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            const selectedMeal = tab.getAttribute('data-meal');
            if (selectedMeal === 'all') {
                mealSections.forEach(section => {
                    section.style.display = 'block';
                });
            } else {
                mealSections.forEach(section => {
                    section.style.display = 'none';
                });
                const selectedSection = document.getElementById(selectedMeal);
                if (selectedSection) {
                    selectedSection.style.display = 'block';
                }
            }
        });
    });
    const defaultSection = document.getElementById('breakfast');
    if (defaultSection) {
        defaultSection.style.display = 'block';
    }
});
function updateMenuSection(hours) {
    const mealTabs = document.querySelectorAll('.meal-tab');
    const mealSections = document.querySelectorAll('.meal-section');
    const showAllBtn = document.querySelector('[data-meal="all"]');
    const isBreakfastTime = hours >= 7 && hours < 12;
    const isLunchTime = hours >= 12 && hours < 16;
    const isDinnerTime = hours >= 18 && hours < 23;
    mealTabs.forEach(tab => {
        tab.classList.remove('active');
        tab.style.display = 'block';
    });
    mealSections.forEach(section => section.style.display = 'none');
    if (isBreakfastTime) {
        const breakfastTab = document.querySelector('[data-meal="breakfast"]');
        const breakfastSection = document.getElementById('breakfast');
        if (breakfastTab && breakfastSection) {
            breakfastTab.classList.add('active');
            breakfastSection.style.display = 'block';
            updateItemAvailability(hours, 'breakfast');
        }
    } else if (isLunchTime) {
        const lunchTab = document.querySelector('[data-meal="lunch"]');
        const lunchSection = document.getElementById('lunch');
        if (lunchTab && lunchSection) {
            lunchTab.classList.add('active');
            lunchSection.style.display = 'block';
            updateItemAvailability(hours, 'lunch');
        }
    } else if (isDinnerTime) {
        const dinnerTab = document.querySelector('[data-meal="dinner"]');
        const dinnerSection = document.getElementById('dinner');
        if (dinnerTab && dinnerSection) {
            dinnerTab.classList.add('active');
            dinnerSection.style.display = 'block';
            updateItemAvailability(hours, 'dinner');
        }
    } else {
        const container = document.querySelector('.menu-grid');
        if (container) {
            container.innerHTML = '<div class="no-service-message">Our service hours are:<br>Breakfast: 7 AM - 11 AM<br>Lunch: 12 PM - 4 PM<br>Dinner: 6 PM - 11 PM</div>';
        }
    }
    if (showAllBtn) {
        showAllBtn.classList.remove('active');
    }
}
function updateItemAvailability(hours, mealTime) {
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach(item => {
        const section = item.closest('.meal-section');
        if (!section) return;
        const availabilityBadge = item.querySelector('.availability-badge');
        const addToCartBtn = item.querySelector('.add-to-cart');
        const stockQty = parseInt(availabilityBadge?.dataset.stock || '0');
        const isCurrentMealTime = section.id === mealTime;
        if (isCurrentMealTime && stockQty > 0) {
            if (availabilityBadge) {
                availabilityBadge.innerHTML = '<span class="in-stock">In Stock</span>';
            }
            if (addToCartBtn) {
                addToCartBtn.disabled = false;
            }
        } else {
            if (availabilityBadge) {
                availabilityBadge.innerHTML = '<span class="not-available">Not Available Now</span>';
            }
            if (addToCartBtn) {
                addToCartBtn.disabled = true;
            }
        }
    });
}
function updateClock() {
    const now = new Date();
    const hours = now.getHours();
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const seconds = now.getSeconds().toString().padStart(2, '0');
    const meridiem = hours >= 12 ? 'PM' : 'AM';
    const hour12 = (hours % 12) || 12;
    const timeString = `${hour12}:${minutes}:${seconds} ${meridiem}`;
    const clockDisplay = document.getElementById('clockDisplay');
    if (clockDisplay) {
        clockDisplay.textContent = timeString;
    }
}
document.addEventListener('DOMContentLoaded', () => {
    const header = document.querySelector('.header');
    if (header && !document.getElementById('clockDisplay')) {
        const clockElement = document.createElement('div');
        clockElement.id = 'clockDisplay';
        clockElement.classList.add('clock');
        header.appendChild(clockElement);
    }
    updateClock();
    setInterval(updateClock, 1000);
});