// Patient Profile Tabs Functionality
document.addEventListener('DOMContentLoaded', function () {
    // Get all tab buttons and content sections
    const tabButtons = {
        visitsTab: document.getElementById('visitsTab'),
        prescriptionsTab: document.getElementById('prescriptionsTab'),
        panchkarmaTab: document.getElementById('panchkarmaTab'),
        billsTab: document.getElementById('billsTab'),
        feedbackTab: document.getElementById('feedbackTab'),
        reportsTab: document.getElementById('reportsTab')
    };

    const tabContents = {
        visitsTabContent: document.getElementById('visitsTabContent'),
        prescriptionsTabContent: document.getElementById('prescriptionsTabContent'),
        // Add other tab content sections as they are created
        panchkarmaTabContent: document.getElementById('panchkarmaTabContent'),
        billsTabContent: document.getElementById('billsTabContent'),
        feedbackTabContent: document.getElementById('feedbackTabContent'),
        reportsTabContent: document.getElementById('reportsTabContent')
    };

    // Function to remove active classes from all tabs
    function removeActiveClasses() {
        Object.values(tabButtons).forEach(button => {
            if (button) {
                button.classList.remove('text-ayur-green-600', 'border-b-2', 'border-ayur-green-500');
                button.classList.add('text-ayur-brown-600', 'hover:text-ayur-brown-800');
            }
        });
    }

    // Function to hide all tab contents
    function hideAllTabContents() {
        Object.values(tabContents).forEach(content => {
            if (content) {
                content.classList.add('hidden');
            }
        });
    }

    // Function to show active tab
    function showActiveTab(activeButton, activeContent) {
        // Remove active classes from all tabs
        removeActiveClasses();

        // Hide all content
        hideAllTabContents();

        // Add active classes to clicked tab
        if (activeButton) {
            activeButton.classList.remove('text-ayur-brown-600', 'hover:text-ayur-brown-800');
            activeButton.classList.add('text-ayur-green-600', 'border-b-2', 'border-ayur-green-500');
        }

        // Show active content
        if (activeContent) {
            activeContent.classList.remove('hidden');
        }
    }

    // Add click event listeners to each tab
    if (tabButtons.visitsTab) {
        tabButtons.visitsTab.addEventListener('click', function () {
            showActiveTab(tabButtons.visitsTab, tabContents.visitsTabContent);
        });
    }

    if (tabButtons.prescriptionsTab) {
        tabButtons.prescriptionsTab.addEventListener('click', function () {
            showActiveTab(tabButtons.prescriptionsTab, tabContents.prescriptionsTabContent);
        });
    }

    if (tabButtons.panchkarmaTab) {
        tabButtons.panchkarmaTab.addEventListener('click', function () {
            showActiveTab(tabButtons.panchkarmaTab, tabContents.panchkarmaTabContent);
            // If content doesn't exist, create placeholder
            if (!tabContents.panchkarmaTabContent) {
                createPlaceholderContent('panchkarmaTabContent', 'Panchkarma History', 'panchkarma treatments');
            }
        });
    }

    if (tabButtons.billsTab) {
        tabButtons.billsTab.addEventListener('click', function () {
            showActiveTab(tabButtons.billsTab, tabContents.billsTabContent);
            // If content doesn't exist, create placeholder
            if (!tabContents.billsTabContent) {
                createPlaceholderContent('billsTabContent', 'Bills', 'billing records');
            }
        });
    }

    if (tabButtons.feedbackTab) {
        tabButtons.feedbackTab.addEventListener('click', function () {
            showActiveTab(tabButtons.feedbackTab, tabContents.feedbackTabContent);
            // If content doesn't exist, create placeholder
            if (!tabContents.feedbackTabContent) {
                createPlaceholderContent('feedbackTabContent', 'Feedback', 'patient feedback');
            }
        });
    }

    if (tabButtons.reportsTab) {
        tabButtons.reportsTab.addEventListener('click', function () {
            showActiveTab(tabButtons.reportsTab, tabContents.reportsTabContent);
            // If content doesn't exist, create placeholder
            if (!tabContents.reportsTabContent) {
                createPlaceholderContent('reportsTabContent', 'Reports', 'medical reports');
            }
        });
    }

    // Function to create placeholder content for tabs that don't have content yet
    function createPlaceholderContent(contentId, title, type) {
        const mainElement = document.querySelector('main');
        const placeholderContent = document.createElement('div');
        placeholderContent.id = contentId;
        placeholderContent.className = 'bg-white rounded-lg shadow-md p-6 mb-6';

        placeholderContent.innerHTML = `
            <div class="text-center py-12">
                <div class="mb-4">
                    <i class="fa-solid fa-folder-open text-6xl text-ayur-brown-300"></i>
                </div>
                <h3 class="text-xl font-semibold text-ayur-brown-800 mb-2">${title}</h3>
                <p class="text-ayur-brown-600 mb-6">No ${type} found for this patient.</p>
                <button class="bg-ayur-green-600 text-white hover:bg-ayur-green-700 font-medium rounded-lg text-sm px-6 py-2">
                    <i class="fa-solid fa-plus mr-2"></i> Add ${title}
                </button>
            </div>
        `;

        mainElement.appendChild(placeholderContent);
        tabContents[contentId] = placeholderContent;
    }

    // Initialize - show visits tab by default (it's already active in HTML)
    // Just ensure other contents are hidden
    hideAllTabContents();
    if (tabContents.visitsTabContent) {
        tabContents.visitsTabContent.classList.remove('hidden');
    }
});

// Additional utility functions for enhanced functionality

// Function to programmatically switch to a specific tab
function switchToTab(tabName) {
    const event = new Event('click');
    const tabButton = document.getElementById(tabName + 'Tab');
    if (tabButton) {
        tabButton.dispatchEvent(event);
    }
}

// Function to get currently active tab
function getCurrentActiveTab() {
    const activeTab = document.querySelector('#patientProfileTabs button.text-ayur-green-600');
    return activeTab ? activeTab.id : null;
}

// Function to add badge count to tabs (useful for notifications)
function addTabBadge(tabId, count) {
    const tab = document.getElementById(tabId);
    if (tab && count > 0) {
        // Remove existing badge if any
        const existingBadge = tab.querySelector('.tab-badge');
        if (existingBadge) {
            existingBadge.remove();
        }

        // Add new badge
        const badge = document.createElement('span');
        badge.className = 'tab-badge ml-1 bg-red-500 text-white text-xs rounded-full px-2 py-1';
        badge.textContent = count;
        tab.appendChild(badge);
    }
}
