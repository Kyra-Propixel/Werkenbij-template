<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title><?= phpb_e($page->get('title')) ?></title>
    <link rel="stylesheet" href="/phpagebuilder/config/themes/werken-bij/public/css/fontawesome.min.css" />
    <link rel="stylesheet" href="/phpagebuilder/config/themes/werken-bij/public/css/fontawesome.css" />
    <link rel="stylesheet" href="/phpagebuilder/config/themes/werken-bij/public/css/brands.min.css" />
    <link rel="stylesheet" href="/phpagebuilder/config/themes/werken-bij/public/css/solid.min.css" />
    <link rel="stylesheet" href="/phpagebuilder/config/themes/werken-bij/public/css/regular.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <link rel="stylesheet" href="/phpagebuilder/config/themes/werken-bij/public/css/globals.css" />
    <link rel="stylesheet" href="/phpagebuilder/config/themes/werken-bij/public/css/styleguide.css" />
    <link rel="stylesheet" href="/phpagebuilder/config/themes/werken-bij/public/css/style.css?v=1.4" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sometype+Mono:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

</head>

<body>
<section title="responsief-navbar" id="responsief-navbar" class="stickysticky">
  <nav>
    <div class="nav-content">
      <input type="checkbox" id="check">
      <label for="check" class="checkbtn">
          <i class="fa-solid fa-bars"></i>
      </label>
      <label class="logo"><a href="index.html"><img src="img/LOGO (1080 x 150 px)/puretech-white.png" alt="Logo"></a></label>
      <div class="nav-center">
          <ul>
              <label for="check" class="closebtn">
                  <i class="fa-solid fa-xmark"></i>
              </label>
              <!-- Dynamische navigatie-items worden hier geladen -->
          </ul>
      </div>
      <div id="dynamicButtonContainer"></div>
    </div>
    <!-- Dynamische knop wordt hier geladen -->
    
  </nav>
</section>

<?= $body ?>

<script src="/phpagebuilder/config/themes/werken-bij/public/js/app.js"></script>

<!-- Run PHPageBuilder script.js files -->
<script type="text/javascript">
    document.querySelectorAll("script").forEach(function(scriptTag) {
        scriptTag.dispatchEvent(new Event('run-script'));
    });
</script>

<!-- Script to dynamically load the header data -->
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', () => {
        const navItemsContainer = document.querySelector('.nav-center ul');
        const dynamicButtonContainer = document.getElementById('dynamicButtonContainer');
        const logoElement = document.querySelector('.logo img'); 
        const socialMediaContainers = document.querySelectorAll('.social-media-icons');

        // Fetch menu items, logo, and other header information
        fetch('/api/renderMenuItems')
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                applyHeaderBackground(data.header_background); // Apply background color
                updateLogo(data.headerLogo); // Update logo
                populateNavItems(data.header_items, navItemsContainer, dynamicButtonContainer); // Populate nav items
                populateSocialMediaIcons(data.social_media_items, socialMediaContainers); // Populate social icons
            })
            .catch(error => console.error('Error loading header items:', error));
    });

    // Function to set the navbar and nav center background color
    function applyHeaderBackground(color) {
        if (color) {
            const navElement = document.querySelector('nav');
            navElement.style.backgroundColor = color;

            // Also set the responsive background color
            navElement.style.setProperty('--responsive-navbar-bg-color', color);

            // Change the background color of .nav-center ul
            const navItemsContainer = document.querySelector('.nav-center ul');
            navItemsContainer.style.backgroundColor = color; // Set the same background color
        }
    }

    // Function to update the logo source
    function updateLogo(logoSrc) {
        if (logoSrc) {
            document.querySelector('.logo img').src = `/phpagebuilder/config/uploads/${logoSrc}`;
        }
    }

    // Function to populate navigation items
    function populateNavItems(items, ulContainer, buttonContainer) {
        if (Array.isArray(items)) {
            clearExistingNavItems(ulContainer, buttonContainer); // Clear existing items
            items.forEach(item => {
                if (item.type === 'button') {
                    appendButtonItem(item, buttonContainer); // For button types
                } else {
                    appendTextItem(item, ulContainer); // For text types
                }
            });
        }
    }

    // Clear existing navigation items
    function clearExistingNavItems(ulContainer, buttonContainer) {
        const existingItems = ulContainer.querySelectorAll('.nav-item.dynamic');
        existingItems.forEach(item => item.remove());

        // Clear existing button items
        buttonContainer.innerHTML = '';
    }

    // Function to append a text navigation item
    function appendTextItem(item, container) {
        if (item.button_text && item.button_link) {
            const li = document.createElement('li');
            li.classList.add('nav-item', 'dynamic');

            const link = document.createElement('a');
            link.href = item.button_link;
            link.classList.add('nav-text');
            link.textContent = item.button_text; // Set the link text
            li.appendChild(link);
            container.appendChild(li);
        }
    }

    // Function to append a button navigation item
    function appendButtonItem(item, container) {
        const button = document.createElement('button');
        button.classList.add('nav-button', 'btn-s');
        setInitialButtonStyles(button, item);
        setButtonHoverEffects(button, item);
        button.textContent = item.button_text; // Set the button text
        button.onclick = () => {
            // Redirect to button link if applicable
            if (item.button_link) {
                window.location.href = item.button_link;
            }
        };

        container.appendChild(button); // Append to the button container
    }

    // Set initial button styles
    function setInitialButtonStyles(link, item) {
        link.style.borderWidth = `${item.border_thickness}px`;
        link.style.borderColor = item.border_color;
        link.style.backgroundColor = item.background_color || '#007bff'; // Default button background color
        link.style.color = item.color; // Set initial color
    }

    // Set hover effects for buttons
    function setButtonHoverEffects(link, item) {
        link.addEventListener('mouseover', () => {
            link.style.borderWidth = `${item.hover_border_thickness}px`;
            link.style.borderColor = item.hover_border_color;
            link.style.backgroundColor = item.hover_background_color || item.background_color || '#0056b3'; // Keep background color on hover
            link.style.color = item.hover_color;
        });

        link.addEventListener('mouseout', () => {
            // Revert to initial styles on mouse out
            link.style.borderWidth = `${item.border_thickness}px`;
            link.style.borderColor = item.border_color;
            link.style.backgroundColor = item.background_color || '#007bff'; // Reset to original background color
            link.style.color = item.color;
        });
    }

    // Function to populate social media icons
    function populateSocialMediaIcons(items, containers) {
        if (Array.isArray(items)) {
            containers.forEach(container => {
                container.innerHTML = ''; // Clear existing icons
                items.forEach(item => appendSocialIcon(item, container));
            });
        }
    }

    // Function to append a social media icon
    function appendSocialIcon(item, container) {
        if (item.icon_class && item.platform_name) {
            const a = document.createElement('a');
            a.href = item.url ? item.url.trim() : '#';
            a.target = '_blank';
            a.classList.add('social-icon');

            const icon = document.createElement('i');
            icon.className = item.icon_class;
            a.appendChild(icon);
            container.appendChild(a);
        }
    }
</script>
</body>
</html>