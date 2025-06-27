const STATE = {
    SHOWING_ALL: false,
    ACTIVE_TAB: null
};

function updateMenuAvailability() {
    const currentHour = new Date().getHours();
    
    // Define meal time ranges
    const mealTimes = {
        breakfast: { start: 7, end: 12 },
        lunch: { start: 12, end: 16 },
        dinner: { start: 18, end: 23 }
    };

    Object.entries(mealTimes).forEach(([meal, time]) => {
        const section = document.getElementById(meal);
        if (section) {
            const menuItems = section.querySelectorAll('.menu-item');
            
            menuItems.forEach(item => {
                const availabilityBadge = item.querySelector('.availability-badge');
                const addToCartBtn = item.querySelector('.add-to-cart');
                
                // Determine meal time status
                let status;
                if (currentHour < time.start) {
                    status = {
                        message: `${meal.charAt(0).toUpperCase() + meal.slice(1)} starts at ${time.start}:00`,
                        available: false,
                        class: 'not-started'
                    };
                } else if (currentHour >= time.start && currentHour < time.end) {
                    status = {
                        message: 'Available Now',
                        available: true,
                        class: 'available'
                    };
                } else {
                    status = {
                        message: `${meal.charAt(0).toUpperCase() + meal.slice(1)} Hours Ended`,
                        available: false,
                        class: 'ended'
                    };
                }
                
                // Update UI elements
                if (availabilityBadge) {
                    availabilityBadge.innerHTML = `<span class="${status.class}">${status.message}</span>`;
                    availabilityBadge.setAttribute('data-available', status.available.toString());
                }
                
                if (addToCartBtn) {
                    addToCartBtn.disabled = !status.available;
                }
            });
        }
    });
}

// Add CSS styles for unavailable items
const menuStyles = document.createElement('style');
menuStyles.textContent = `
    .meal-section {
        transition: all 0.3s ease-in-out;
        display: none; /* Hide all sections by default */
    }
    
    .menu-item.unavailable {
        opacity: 0.7;
        pointer-events: none;
    }
    
    .menu-item.unavailable::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.5);
        z-index: 1;
    }

    .out-of-service {
        background: #dc3545;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.9em;
    }

    .meal-tab {
        transition: all 0.3s ease;
    }
    
    .meal-tab.active {
        background-color: #007bff;
        color: white;
    }

    .currently-unavailable {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(220, 53, 69, 0.9);
        color: white;
        padding: 8px 16px;
        border-radius: 4px;
        font-weight: bold;
        z-index: 2;
        text-align: center;
        white-space: nowrap;
    }

    .menu-item.unavailable .menu-item-img {
        filter: grayscale(50%);
        opacity: 0.7;
    }

    .menu-item.unavailable .menu-item-content {
        opacity: 0.7;
    }

    .menu-item.unavailable .add-to-cart {
        background: #ccc;
        cursor: not-allowed;
    }

    .out-of-stock {
        background: #dc3545;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.9em;
        font-weight: bold;
        display: inline-block;
        margin-top: 4px;
    }

    .add-to-cart[disabled] {
        background: #ccc;
        cursor: not-allowed;
        opacity: 0.7;
    }

    .not-started {
        background: #ffc107;
        color: #000;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.9em;
        font-weight: bold;
        display: inline-block;
        margin-top: 4px;
    }

    .available {
        background: #28a745;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.9em;
        font-weight: bold;
        display: inline-block;
        margin-top: 4px;
    }

    .ended {
        background: #dc3545;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.9em;
        font-weight: bold;
        display: inline-block;
        margin-top: 4px;
    }
`;
document.head.appendChild(menuStyles);

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM fully loaded and parsed.');
    
    // Initial availability check
    updateMenuAvailability();
    
    // Update availability every minute
    setInterval(updateMenuAvailability, 60000);

    // Show first section by default (breakfast)
    const firstSection = document.querySelector('.meal-section');
    if (firstSection) {
        firstSection.style.display = 'block';
    }

    const showAllBtn = document.querySelector('[data-meal="all"]');
    if (showAllBtn) {
        showAllBtn.addEventListener('click', () => {
            console.log('Show All button clicked.');
            handleShowAllClick(showAllBtn);
        });
    }

    // Update meal tab click handlers
    document.querySelectorAll('.meal-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            const mealType = tab.dataset.meal;
            console.log(`Meal tab clicked: ${mealType}`);

            if (mealType === 'all') {
                handleShowAllClick(tab);
            } else {
                handleMealTabClick(tab);
            }
            // Add your custom logic for each meal type here
            const mealSections = document.querySelectorAll('.meal-section');
            mealSections.forEach(section => {
                if (section.id === mealType) {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            });
            const allTabs = document.querySelectorAll('.meal-tab');
            allTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            STATE.ACTIVE_TAB = mealType;
            STATE.SHOWING_ALL = false;
            console.log(`Active tab set to: ${STATE.ACTIVE_TAB}`);
            console.log(`Showing all set to: ${STATE.SHOWING_ALL}`);
            // Additional logic for meal types can be added here
            console.log(`Meal type selected: ${mealType}`);
            // Example: Fetch and display meals based on selected type
            // fetchMeals(mealType).then(meals => {
            //     displayMeals(meals);
            // });
            // Example: Update UI with selected meal type
            // updateUIWithMealType(mealType);
            // Example: Perform any other actions based on selected meal type
            // ...
        });
    });

    // Add event listeners for remaining buttons
    const remainingButtons = document.querySelectorAll('[data-action]');
    remainingButtons.forEach(button => {
        button.addEventListener('click', () => {
            const action = button.dataset.action;
            console.log(`Button clicked with action: ${action}`);
            // Add your custom logic for each button action here
        });
    });

    // Add this to your existing JavaScript
    document.querySelectorAll('.meal-button').forEach(button => {
        button.addEventListener('click', () => {
            // Remove active class from all buttons
            document.querySelectorAll('.meal-button').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Add active class to clicked button
            button.classList.add('active');
            
            // Hide all meal sections
            document.querySelectorAll('.meal-section').forEach(section => {
                section.style.display = 'none';
            });
            
            // Show selected meal section
            const mealType = button.getAttribute('data-meal');
            document.getElementById(mealType).style.display = 'block';
            
            // Update menu availability based on current time
            updateMenuAvailability();
        });
    });

    // Update button states based on current time
    function updateButtonStates() {
        const currentHour = new Date().getHours();
        const buttons = document.querySelectorAll('.meal-button');
        
        buttons.forEach(button => {
            const mealType = button.getAttribute('data-meal');
            let isAvailable = false;
            
            switch(mealType) {
                case 'breakfast':
                    isAvailable = currentHour >= 7 && currentHour < 12;
                    break;
                case 'lunch':
                    isAvailable = currentHour >= 12 && currentHour < 16;
                    break;
                case 'dinner':
                    isAvailable = currentHour >= 18 && currentHour < 23;
                    break;
            }
            
            button.disabled = !isAvailable;
            if (!isAvailable) {
                button.classList.remove('active');
            }
        });
    }

    // Call updateButtonStates initially and every minute
    updateButtonStates();
    setInterval(updateButtonStates, 60000);
});

function handleShowAllClick(tab) {
    const mealSections = document.querySelectorAll('.meal-section');
    const allTabs = document.querySelectorAll('.meal-tab');

    // Always show all sections when "Show All" is clicked
    mealSections.forEach(section => section.style.display = 'block');
    allTabs.forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    STATE.ACTIVE_TAB = 'all';
    STATE.SHOWING_ALL = true;
}

function handleMealTabClick(tab) {
    if (STATE.SHOWING_ALL) {
        STATE.SHOWING_ALL = false;
    }

    const mealType = tab.dataset.meal;
    const targetSection = document.getElementById(mealType);
    const allTabs = document.querySelectorAll('.meal-tab');
    const allSections = document.querySelectorAll('.meal-section');

    // Show clicked tab's content, hide others
    allSections.forEach(section => section.style.display = 'none');
    allTabs.forEach(t => t.classList.remove('active'));
    targetSection.style.display = 'block';
    tab.classList.add('active');
    STATE.ACTIVE_TAB = mealType;
}