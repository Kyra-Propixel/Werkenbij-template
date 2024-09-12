<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="container mt-5">
    <h1 class="mb-4"><?= phpb_trans('website-manager.global-header-settings'); ?></h1>
    <?php
        if (phpb_flash('message')):
        ?>
        <div class="alert alert-<?= phpb_flash('message-type') ?>">
            <?= phpb_flash('message') ?>
        </div>
        <?php
        endif;
        ?>
    <form action="<?= phpb_url('website_manager', ['route' => 'header_settings', 'action' => 'update', 'tab' => 'menus']) ?>" method="post" enctype="multipart/form-data" id="headerSettingsForm">
        
        <!-- Global Header Settings (Logo and Background) -->
        <div class="header-settings form-group mb-4 p-3 border rounded">
            <h4 class="mb-3">Navbar Color & Logo</h4>

            <div class="mb-3">
                <label for="header_logo">Logo Navbar</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="header_logo" name="header_logo">
                    <label class="custom-file-label" for="header_logo"><?= phpb_trans('website-manager.choose-file'); ?></label>
                </div>
            </div>

            <div class="mb-3">
                <label for="header_background">Color Navbar</label>
                <input type="color" class="form-control form-control-color" id="header_background" name="header_background" value="#ffffff">
            </div>
        </div>

        <!-- Container for dynamically added items (e.g., buttons) -->
        <div class="header-items-container form-group mb-4 p-3 border rounded">
            <h4 class="mb-3">Menu Items</h4>

            <div id="headerItemsContainer" class="grid-container">
                <!-- Dynamically added items like buttons will appear here -->
            </div>

            <!-- Button to add new items, which will be replaced with a warning after 6 items -->
            <div id="addHeaderItemContainer">
                <button type="button" class="btn btn-secondary btn-sm mb-3" id="addHeaderItemButton">
                    <?= phpb_trans('website-manager.add-header-item'); ?> +
                </button>
            </div>
        </div>

        <button class="btn btn-primary btn-sm" type="submit">
            <?= phpb_trans('website-manager.save-settings'); ?>
        </button>
    </form>
</div>

<!-- Custom CSS for styling and grid layout -->
<style>
    /* Container for grid layout, 3 columns max */
    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); /* 3 items max, flexible */
        gap: 20px;
    }

    .custom-file-input {
        cursor: pointer;
    }

    .custom-file-label {
        cursor: pointer;
    }

    .form-control-color {
        width: 100%;
        height: 38px;
    }

    .header-settings, .header-items-container {
        background-color: #f8f9fa;
    }

    .header-item {
        background-color: #e9ecef;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease;
    }

    .header-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .btn-sm {
        padding: .25rem .5rem;
    }

    .truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
        max-width: 100%;
    }

    .remove-header-item {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .remove-header-item i {
        margin-right: 4px;
    }
</style>

<script>
    let itemCount = 0;  // Keep track of item count for unique identifiers
    const maxItems = 6; // Maximum allowed items

    // Function to add a new header item (such as a button)
    document.getElementById('addHeaderItemButton').addEventListener('click', function() {
        if (itemCount < maxItems) {
            addItem();
        }
        if (itemCount >= maxItems) {
            displayMaxItemsWarning();
        }
    });

    // Function to add a new item
    function addItem() {
        const newItem = document.createElement('div');
        newItem.classList.add('header-item', 'form-group', 'mb-3');
        newItem.id = `header-item-${itemCount}`;

        newItem.innerHTML = `
            <h5 class="mb-2">Header Button ${itemCount + 1}</h5>
            
            <div class="mb-3">
                <label for="header_button_text_${itemCount}">Header Button Text</label>
                <input type="text" class="form-control truncate" id="header_button_text_${itemCount}" name="header_items[${itemCount}][button_text]" placeholder="Item Button Name">
            </div>

            <div class="mb-3">
                <label for="header_button_link_${itemCount}">Header Button URL</label>
                <input type="url" class="form-control" id="header_button_link_${itemCount}" name="header_items[${itemCount}][button_link]" placeholder="Item Button URL">
            </div>

            <button type="button" class="btn btn-danger btn-sm remove-header-item">
                <i class="fas fa-trash-alt"></i> <?= phpb_trans('website-manager.remove-item'); ?>
            </button>
        `;

        // Append the new item (button) to the container
        document.getElementById('headerItemsContainer').appendChild(newItem);

        // Attach event listener for the remove button
        newItem.querySelector('.remove-header-item').addEventListener('click', function() {
            newItem.remove();
            updateItemNumbers();  // Update the item numbers after deletion
            hideMaxItemsWarning(); // Hide the warning if we're back under the limit
        });

        itemCount++;  // Increment item count
    }

    // Function to display the warning message when the max items limit is reached
    function displayMaxItemsWarning() {
        const warningMessage = `
            <div class="alert alert-warning" role="alert">
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
                    <?= phpb_trans('website-manager.add-header-item'); ?> +
                </button>
            `;
            document.getElementById('addHeaderItemContainer').innerHTML = addButton;
            document.getElementById('addHeaderItemButton').addEventListener('click', function() {
                if (itemCount < maxItems) {
                    addItem();
                }
                if (itemCount >= maxItems) {
                    displayMaxItemsWarning();
                }
            });
        }
    }

    // Function to update the numbering after an item is removed
    function updateItemNumbers() {
        const headerItems = document.querySelectorAll('.header-item');
        itemCount = headerItems.length;  // Adjust the itemCount to match current items

        // Renumber the items
        headerItems.forEach((item, index) => {
            item.id = `header-item-${index}`;
            item.querySelector('h5').innerText = `<?= phpb_trans('website-manager.header-item'); ?> ${index + 1}`;
            item.querySelector('.form-control.truncate').id = `header_button_text_${index}`;
            item.querySelector('input[type="url"]').id = `header_button_link_${index}`;
        });
    }
</script>