<!-- Form for Menu and Preview -->
<div class="container mt-5">
    <?php if (phpb_flash('message')): ?>
        <div class="alert alert-<?= phpb_flash('message-type') ?>">
            <?= phpb_flash('message') ?>
        </div>
    <?php endif; ?>

    <!-- Form for Navbar Settings -->
    <form action="<?= phpb_url('website_manager', ['route' => 'header_settings', 'action' => 'update', 'tab' => 'menus']) ?>" method="post" enctype="multipart/form-data" id="headerSettingsForm">

        <!-- Global Header Settings (Logo and Background) -->
        <div class="header-settings form-group mb-4 p-3 border rounded">
            <h4 class="mb-3">Navbar Color & Logo</h4>

            <div class="mb-3">
                <label for="header_logo">Logo Navbar</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="header_logo" name="header_logo">
                    <label class="custom-file-label" for="header_logo">
                        <?= !empty($headerLogo) ? basename($headerLogo) : phpb_trans('website-manager.choose-file'); ?>
                    </label>
                </div>
                <?php if (!empty($headerLogo)): ?>
                    <img src="<?= htmlspecialchars($headerLogo) ?>" alt="Current Logo" style="max-height: 100px; margin-top: 10px;">
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="header_background">Color Navbar</label>
                <input type="color" class="form-control form-control-color" id="header_background" name="header_background" value="<?= htmlspecialchars($headerBackground ?? '#ffffff') ?>">
            </div>
        </div>

        <h3 class="alert alert-info">Let op: de preview is een weergave en kan afwijken van de uiteindelijke versie.</h3>
        <!-- Live Preview Section -->
        <div id="navbarPreview" class="navbar-preview mb-4 p-3" style="background-color: <?= htmlspecialchars($headerBackground ?? '#ffffff') ?>; display: flex; justify-content: space-between; align-items: center;">
            <img src="<?= htmlspecialchars($headerLogo) ?>" alt="Logo" id="previewLogo" style="max-height: 50px; margin-right: 20px;"/>
            <nav>
                <ul id="previewMenuItems" style="list-style: none; padding: 0; display: flex; gap: 20px; margin: 0;">
                    <?php foreach ($headerItems as $index => $item): ?>
                        <li id="previewMenuItem_<?= $index ?>" style="padding: 10px;"><?= htmlspecialchars($item['button_text']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </div>

        <!-- Menu Items -->
        <div class="header-items-container form-group mb-4 p-3 border rounded">
            <h4 class="mb-3">Menu Items</h4>

            <div id="headerItemsContainer" class="grid-container">
                <?php if (!empty($headerItems)): ?>
                    <?php foreach ($headerItems as $index => $item): ?>
                        <div class="header-item form-group mb-3" id="header-item-<?= $index ?>">
                            <h5 class="mb-2">Item <?= $index + 1 ?></h5>

                            <div class="mb-3">
                                <label for="header_button_text_<?= $index ?>">Header Button Text</label>
                                <input type="text" class="form-control truncate" id="header_button_text_<?= $index ?>" name="header_items[<?= $index ?>][button_text]" value="<?= htmlspecialchars($item['button_text']) ?>" placeholder="Item Button Name">
                            </div>

                            <div class="mb-3">
                                <label for="header_button_link_<?= $index ?>">Header Button URL</label>
                                <input type="text" class="form-control" id="header_button_link_<?= $index ?>" name="header_items[<?= $index ?>][button_link]" value="<?= htmlspecialchars($item['button_link']) ?>" placeholder="Item Button URL">
                            </div>

                            <button type="button" class="btn btn-danger btn-sm remove-header-item">
                                <i class="fas fa-trash-alt"></i> Remove Item
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Button to Add New Items -->
            <div id="addHeaderItemContainer">
                <button type="button" class="btn btn-secondary btn-sm mb-3" id="addHeaderItemButton">
                    Add Item +
                </button>
            </div>
        </div>

        <button class="btn btn-primary btn-sm" type="submit">
            <?= phpb_trans('website-manager.save-settings') ?>
        </button>
    </form>
</div>

<!-- Custom CSS for styling and grid layout -->
<style>

    /* Smooth transition when items are moved */
.header-item {
    transition: transform 0.2s ease, opacity 0.2s ease-in-out;
    background-color: #ffffff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow for a modern look */
    overflow: hidden;
    position: relative;
}

/* Add a modern hover effect */
.header-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15); /* Larger, softer shadow on hover */
}

/* Style for the item being dragged */
.sortable-chosen {
    opacity: 0.8; /* Slightly less transparent for better visibility */
    transform: scale(1.05);
    background-color: #f5f5f5; /* Lighter background to indicate drag state */
    border-radius: 12px;
}

/* Ghost element to visualize where the item will be placed */
.sortable-ghost {
    opacity: 0.3;
    background-color: #e9ecef; /* Subtle background color for ghost element */
    border: 2px dashed #cccccc;
    border-radius: 12px;
}

/* Adding a placeholder style to show where the item will be dropped */
.sortable-placeholder {
    height: 60px;
    background-color: #f1f3f4; /* Light background color for the placeholder */
    border: 2px solid #dcdcdc;
    border-radius: 12px;
    margin-bottom: 20px;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1); /* Inset shadow for depth */
}

/* Improve the layout and spacing for the header items */
.grid-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); /* Adjust to better fit modern screen sizes */
    gap: 24px;
}

/* Style the custom file input for a modern look */
.custom-file-input {
    cursor: pointer;
    border-radius: 6px;
}

.custom-file-label {
    cursor: pointer;
    font-size: 14px;
    padding: 8px 12px;
    border-radius: 6px;
    background-color: #f1f3f4;
    border: 1px solid #ced4da;
}

/* Style the color input */
.form-control-color {
    width: 100%;
    height: 48px;
    border-radius: 8px;
    border: 1px solid #ced4da;
    background-color: #ffffff;
}

/* Adjust form control elements for a modern design */
.form-control {
    border-radius: 8px;
    border: 1px solid #ced4da;
    padding: 10px 15px;
    font-size: 14px;
}

/* Truncate long text in header button inputs */
.truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    max-width: 100%;
}

/* Modernize the button styles */
.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
    padding: 8px 16px;
    font-size: 14px;
    border-radius: 8px;
    transition: background-color 0.2s ease-in-out;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #004085;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
    padding: 6px 12px;
    border-radius: 8px;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
    padding: 8px 16px;
    font-size: 14px;
    border-radius: 8px;
    transition: background-color 0.2s ease-in-out;
}

.btn-secondary:hover {
    background-color: #5a6268;
    border-color: #545b62;
}

/* Style for the original index badge */
.original-index {
    font-size: 12px;
    color: #ffffff;
    background-color: #007bff;
    padding: 3px 8px;
    border-radius: 6px;
    position: absolute;
    top: 10px;
    right: 10px;
}

/* Adjust margins and paddings in the header settings section */
.header-settings {
    padding: 20px;
    background-color: #f1f3f4;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); /* Subtle shadow for the header settings */
}

.header-items-container {
    padding: 20px;
    background-color: #f1f3f4;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); /* Subtle shadow for the menu items */
}

/* Style for the original index badge */
.original-index {
    font-size: 12px;
    color: #ffffff;
    background-color: #007bff;
    padding: 3px 8px;
    border-radius: 6px;
    margin-left: 10px;
    display: inline-block;
    font-weight: 500;
}

.highlight {
    background-color: lightgreen;
    transition: 0.4s;
}

.navbar-preview {
    border: 1px solid #ced4da;
    border-radius: 8px;
    padding: 10px;
    display: flex;
    justify-content: flex-start;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.navbar-preview ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    gap: 15px;
}

.navbar-preview img {
    max-height: 50px;
    margin-right: 20px;
}

/* Modernized card container */
.header-item {
    transition: transform 0.2s ease, opacity 0.2s ease-in-out;
    background-color: #fff; /* White background for cleaner design */
    border-radius: 16px; /* Slightly more rounded corners */
    padding: 24px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); /* Softer, deeper shadow */
    margin-bottom: 24px; /* More space between cards */
    position: relative;
    overflow: hidden;
}

/* Hover effect for cards */
.header-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1); /* Larger, softer shadow on hover */
}

/* Title of each item */
.header-item h5 {
    font-size: 1.3rem; /* Slightly larger font for the item title */
    font-weight: bold;
    color: #333; /* Darker color for better contrast */
    margin-bottom: 1rem;
}

/* Input field labels */
.header-item label {
    font-size: 0.9rem; /* Keep labels slightly smaller but legible */
    color: #555; /* Softer grey color */
    margin-bottom: 8px;
    font-weight: 500;
}

/* Modern input fields */
.header-item .form-control {
    border-radius: 12px; /* Softer, more rounded inputs */
    font-size: 0.95rem;
    padding: 12px;
    border: 1px solid #ddd; /* Light border color */
    transition: border-color 0.3s ease;
}

/* Input hover/focus state */
.header-item .form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 6px rgba(0, 123, 255, 0.15); /* Subtle shadow on focus */
}

/* Buttons */
.btn-danger {
    background-color: #ff6b6b; /* Modern red color for remove button */
    border-color: #ff6b6b;
    padding: 8px 16px;
    border-radius: 12px; /* Rounded corners */
    font-size: 0.9rem;
    transition: background-color 0.3s ease;
}

.btn-danger:hover {
    background-color: #ff4b4b; /* Darker shade on hover */
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
    color: #fff;
    padding: 8px 16px;
    font-size: 0.9rem;
    border-radius: 12px; /* Rounded corners */
    transition: background-color 0.3s ease;
}

.btn-secondary:hover {
    background-color: #5a6268;
}

/* Add subtle hover effect to buttons */
.btn {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.btn:hover {
    transform: translateY(-2px);
}

/* Truncate long text in header button inputs */
.truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    max-width: 100%;
}

/* Preview styling */
.navbar-preview {
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); /* Subtle shadow */
}

.navbar-preview img {
    max-height: 50px;
    margin-right: 20px;
}

.navbar-preview ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    gap: 20px; /* More space between menu items */
}

.navbar-preview li {
    padding: 12px;
    background-color: #f8f9fa; /* Subtle background for menu items */
    border-radius: 8px; /* Slightly rounded menu items */
    transition: background-color 0.3s ease;
}

.navbar-preview li:hover {
    background-color: #e2e6ea; /* Slight hover effect */
}

.highlight {
    background-color: #e0e0e0;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
let itemCount = <?= isset($headerItems) ? count($headerItems) : 0 ?>;
const maxItems = 6;
let originalOrder = []; // Track the original positions

document.addEventListener('DOMContentLoaded', function () {
    const headerItemsContainer = document.getElementById('headerItemsContainer');
    const previewMenuItems = document.getElementById('previewMenuItems');
    const previewNavbar = document.getElementById('navbarPreview');
    const previewLogo = document.getElementById('previewLogo');

    // Function to update the live preview for menu items
    function updatePreview() {
        const menuItems = document.querySelectorAll('.header-item input[name^="header_items"][name$="[button_text]"]');
        previewMenuItems.innerHTML = ''; // Clear the current preview

        menuItems.forEach(function (input, index) {
            const menuItem = document.createElement('li');
            menuItem.textContent = input.value || `Item ${index + 1}`; // Default to "Item <number>" if input is empty
            menuItem.style.padding = '10px';
            previewMenuItems.appendChild(menuItem);
        });
    }

    // Update navbar background color in live preview
    document.getElementById('header_background').addEventListener('input', function () {
        previewNavbar.style.backgroundColor = this.value;
    });

    // Update menu items text in live preview
    document.querySelectorAll('.header-item input[name^="header_items"][name$="[button_text]"]').forEach(function (input) {
        input.addEventListener('input', updatePreview);
    });

    // Update the logo in the live preview when a new file is selected
    document.getElementById('header_logo').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
                previewLogo.src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Initialize the preview on page load
    updatePreview();
});


document.addEventListener('DOMContentLoaded', function () {
    const headerItemsContainer = document.getElementById('headerItemsContainer');

    // Store the original order of the items when the page first loads
    document.querySelectorAll('.header-item').forEach((item, index) => {
        item.setAttribute('data-original-index', index + 1);  // Store original position
        originalOrder.push(index + 1); // Store the original order in the array
    });

    // Initialize the headings based on input values on page load
    updateItemNumbers();

    // Initialize SortableJS with enhanced options
    Sortable.create(headerItemsContainer, {
        swap: true,
        swapClass: 'highlight',
        animation: 300,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        handle: '.header-item',
        swapThreshold: 0.5,
        filter: 'input, textarea',  // Prevent dragging while interacting with inputs
        preventOnFilter: false,
        onEnd: function () {
            // Show original badges after moving an item
            showOriginalPositions();  
            // Renumber the items after sorting
            updateItemNumbers();

            updatePreview();
        }
    });

    // Event listener for "Header Button Text" input fields only
    document.addEventListener('input', function (event) {
        if (event.target.matches('.header-item input[name^="header_items"][name$="[button_text]"]')) {
            const input = event.target;
            const headerItem = input.closest('.header-item');
            const itemIndex = Array.from(headerItemsContainer.children).indexOf(headerItem);
            const heading = headerItem.querySelector('h5');

            // Update heading text based on input value
            if (input.value.trim() !== '') {
                heading.textContent = input.value.trim();  // Set the heading to input value
            } else {
                heading.textContent = `Item ${itemIndex + 1}`;  // Default to "Item <number>"
            }
        }
    });
    updatePreview();
});

// Function to update the live preview for menu items
function updatePreview() {
        const menuItems = document.querySelectorAll('.header-item input[name^="header_items"][name$="[button_text]"]');
        previewMenuItems.innerHTML = ''; // Clear the current preview

        menuItems.forEach(function (input, index) {
            const menuItem = document.createElement('li');
            menuItem.textContent = input.value || `Item ${index + 1}`; // Default to "Item <number>" if input is empty
            menuItem.style.padding = '10px';
            previewMenuItems.appendChild(menuItem);
        });
    }

// Function to show original positions in badges after sorting or interaction
function showOriginalPositions() {
    document.querySelectorAll('.header-item').forEach((item) => {
        const originalIndex = item.getAttribute('data-original-index');
        let originalIndexBadge = item.querySelector('.original-index');

        // Create the badge only if it doesn't exist
        if (!originalIndexBadge) {
            originalIndexBadge = document.createElement('div');  // Create a new div for the badge
            originalIndexBadge.classList.add('badge', 'badge-primary', 'original-index');
            originalIndexBadge.style.marginTop = '10px'; // Add margin for spacing
            item.appendChild(originalIndexBadge);  // Append the badge at the bottom of the item (card)
        }

        // Update the badge content with the original index
        originalIndexBadge.textContent = `Vorige Positie: ${originalIndex}`;
        originalIndexBadge.style.display = 'block'; // Ensure the badge is visible
    });
}

// Function to remove badges when the page is reloaded (i.e., reset)
function removeOriginalBadges() {
    document.querySelectorAll('.original-index').forEach(badge => {
        badge.style.display = 'none';  // Hide badges when the page reloads
    });
}

// Call removeOriginalBadges on page load to hide badges on reload
removeOriginalBadges();

// Function to add a new header item
document.getElementById('addHeaderItemButton').addEventListener('click', function () {
    if (itemCount < maxItems) {
        addItem();
    }
    if (itemCount >= maxItems) {
        displayMaxItemsWarning();
    }
});

// Event delegation for remove button
document.addEventListener('click', function (event) {
    if (event.target.closest('.remove-header-item')) {
        event.target.closest('.header-item').remove();
        updateItemNumbers();  // Update the item numbers after deletion
        hideMaxItemsWarning(); // Hide the warning if we're back under the limit
        updatePreview();
    }
});

// Function to add a new header item
function addItem(buttonText = '', buttonLink = '') {
    const newItem = document.createElement('div');
    newItem.classList.add('header-item', 'form-group', 'mb-3');
    newItem.id = `header-item-${itemCount}`;

    const itemNumber = itemCount + 1;
    newItem.innerHTML = `
        <h5 class="mb-2">${buttonText ? buttonText : 'Item ' + itemNumber}</h5>
        <div class="mb-3">
            <label for="header_button_text_${itemCount}">Header Button Text</label>
            <input type="text" class="form-control truncate" id="header_button_text_${itemCount}" name="header_items[${itemCount}][button_text]" value="${buttonText}" placeholder="Item Button Name">
        </div>
        <div class="mb-3">
            <label for="header_button_link_${itemCount}">Header Button URL</label>
            <input type="text" class="form-control" id="header_button_link_${itemCount}" name="header_items[${itemCount}][button_link]" value="${buttonLink}" placeholder="Item Button URL">
        </div>
        <button type="button" class="btn btn-danger btn-sm remove-header-item">
            <i class="fas fa-trash-alt"></i> Remove Item
        </button>
    `;

    document.getElementById('headerItemsContainer').appendChild(newItem);
    itemCount++;

    // Renumber items after adding
    updateItemNumbers();
    updatePreview();
}

// Function to update item numbers and set headings on page reload
function updateItemNumbers() {
    const headerItems = document.querySelectorAll('.header-item');
    itemCount = headerItems.length;  // Adjust the itemCount to match current items

    // Loop through each item and set the heading based on input value or default to "Item <number>"
    headerItems.forEach((item, index) => {
        const heading = item.querySelector('h5');
        const input = item.querySelector('input[name^="header_items"][name$="[button_text]"]');

        // Set heading to the input value or default to "Item <number>"
        if (input.value.trim() !== '') {
            heading.textContent = input.value.trim();
        } else {
            heading.textContent = `Item ${index + 1}`;
        }

        // Update IDs and other elements to reflect the correct order
        item.id = `header-item-${index}`;
        input.id = `header_button_text_${index}`;
        item.querySelector('input[type="text"]').id = `header_button_link_${index}`;
    });
}

// Function to display the warning message when max items limit is reached
function displayMaxItemsWarning() {
    const warningMessage = `
        <div class="alert alert-info" role="alert">
            Je kan tot maximaal 6 items per header.
        </div>
    `;
    document.getElementById('addHeaderItemContainer').innerHTML = warningMessage;
}

// Function to hide the warning message when items are removed and limit is not reached
function hideMaxItemsWarning() {
    if (itemCount < maxItems) {
        const addButton = `
            <button type="button" class="btn btn-secondary btn-sm mb-3" id="addHeaderItemButton">
                Add Item +
            </button>
        `;
        document.getElementById('addHeaderItemContainer').innerHTML = addButton;
        document.getElementById('addHeaderItemButton').addEventListener('click', function () {
            if (itemCount < maxItems) {
                addItem();
            }
            if (itemCount >= maxItems) {
                displayMaxItemsWarning();
            }
        });
    }
}
</script>