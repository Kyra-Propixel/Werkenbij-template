<div id="phpb-loading">
    <div class="circle">
        <div class="loader">
            <div class="loader">
                <div class="loader">
                    <div class="loader"></div>
                </div>
            </div>
        </div>
        <div class="text">
            <?= phpb_trans('pagebuilder.loading-text') ?>
        </div>
    </div>
</div>

<div id="gjs"></div>

<style>
  .modal-body {
    display: flex;
    flex-direction: row;
    background-color: #f7f7f7;
    border-radius: 8px;
    padding: 20px;
  }

  .modal-body h4 {
    color: #333;
    margin-bottom: 15px;
    font-size: 20px;
    font-weight: bold;
  }

  .modal-form {
    flex: 1;
    padding-right: 20px;
    width: 50%;
  }

  .modal-form label {
    font-weight: bold;
    color: #555;
    margin-bottom: 5px;
    display: block;
  }

  .modal-form textarea,
  .modal-form select,
  .modal-form input {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: #fff;
  }

  .modal-form textarea {
    resize: vertical;
  }

  .modal-form button {
    padding: 10px 20px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }

  .modal-form button:hover {
    background-color: #0056b3;
  }

  .modal-preview {
    flex: 1;
    padding-left: 20px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background-color: #fafafa;
    border-left: 1px solid #e0e0e0;
    border-radius: 8px;
    width: 50%;
  }

  .modal-preview img {
    max-width: 100%;
    max-height: 400px;
    border-radius: 8px;
    object-fit: contain;
  }

  .loading-message {
    font-size: 16px;
    color: #333;
    padding: 20px;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 4px;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .error-message {
    color: red;
    font-size: 14px;
    margin-top: 10px;
  }

  #start-save-button {
    margin-top: 10px;
    background-color: #28a745;
    color: white;
  }

  #start-save-button:hover {
    background-color: #218838;
  }
</style>

<!-- JS logic -->
<script type="text/javascript" src="<?= phpb_asset('pagebuilder/ckeditor4-full-4.21.0/ckeditor.js') ?>"></script>
<script type="text/javascript" src="<?= phpb_asset('pagebuilder/grapesjs-plugin-ckeditor-v0.0.10.min.js') ?>"></script>
<script type="text/javascript" src="<?= phpb_asset('pagebuilder/grapesjs-touch-v0.1.1.min.js') ?>"></script>
<script type="text/javascript">
CKEDITOR.dtd.$editable.a = 1;
CKEDITOR.dtd.$editable.b = 1;
CKEDITOR.dtd.$editable.em = 1;
CKEDITOR.dtd.$editable.button = 1;
CKEDITOR.dtd.$editable.strong = 1;
CKEDITOR.dtd.$editable.small = 1;
CKEDITOR.dtd.$editable.span = 1;
CKEDITOR.dtd.$editable.ol = 1;
CKEDITOR.dtd.$editable.ul = 1;
CKEDITOR.dtd.$editable.table = 1;

<?php
$currentLanguage = in_array(phpb_config('general.language'), phpb_active_languages()) ?
    phpb_config('general.language') : array_keys(phpb_active_languages())[0];
if (! empty($_SESSION['phpagebuilder_language'])) {
    $currentLanguage = $_SESSION['phpagebuilder_language'];
}
?>
window.languages = <?= json_encode(phpb_active_languages()) ?>;
window.currentLanguage = <?= json_encode($currentLanguage) ?>;
window.translations = <?= json_encode(phpb_trans('pagebuilder')) ?>;
window.contentContainerComponents = <?= json_encode($pageBuilder->getPageComponents($page)) ?>;
window.themeBlocks = <?= json_encode($blocks) ?>;
window.blockSettings = <?= json_encode($blockSettings) ?>;
window.pageBlocks = <?= json_encode($pageRenderer->getPageBlocksData()) ?>;
window.pages = <?= json_encode($pageBuilder->getPages()) ?>;
window.renderBlockUrl = '<?= phpb_url('pagebuilder', ['action' => 'renderBlock', 'page' => $page->getId()]) ?>';
window.injectionScriptUrl = '<?= phpb_asset('pagebuilder/page-injection.js') ?>';
window.renderLanguageVariantUrl = '<?= phpb_url('pagebuilder', ['action' => 'renderLanguageVariant', 'page' => $page->getId()]) ?>';

<?php
$config = require __DIR__ . '/grapesjs/config.php';
?>
let config = <?= json_encode($config) ?>;
if (window.customConfig !== undefined) {
    config = $.extend(true, {}, window.customConfig, config);
}

window.initialComponents = <?= json_encode($pageRenderer->render()) ?>;
window.initialStyle = <?= json_encode($pageBuilder->getPageStyleComponents($page)) ?>;
window.initialCss = <?= json_encode($pageBuilder->getPageStyleCss($page)) ?>;
window.grapesJSTranslations = {
    <?= $currentLanguage ?>: {
        styleManager: {
            empty: '<?= phpb_trans('pagebuilder.style-no-element-selected') ?>'
        },
        traitManager: {
            empty: '<?= phpb_trans('pagebuilder.trait-no-element-selected') ?>',
            label: '<?= phpb_trans('pagebuilder.trait-settings') ?>',
            traits: {
                options: {
                    target: {
                        false: '<?= phpb_trans('pagebuilder.no') ?>',
                        _blank: '<?= phpb_trans('pagebuilder.yes') ?>'
                    }
                }
            }
        },
        assetManager: {
            addButton: '<?= phpb_trans('pagebuilder.asset-manager.add-image') ?>',
            inputPlh: 'http://path/to/the/image.jpg',
            modalTitle: '<?= phpb_trans('pagebuilder.asset-manager.modal-title') ?>',
            uploadTitle: '<?= phpb_trans('pagebuilder.asset-manager.drop-files') ?>'
        }
    }
};

window.grapesJSLoaded = false;
window.editor = window.grapesjs.init(config);
window.editor.on('load', function(editor) {
    window.grapesJSLoaded = true;
});
window.editor.I18n.addMessages(window.grapesJSTranslations);

editor.setStyle(window.initialStyle);

// Function to open a new modal with the AI Image form
function openLeonardoModal() {
    const modal = editor.Modal;

// Create the form content for the modal
const modalContent = `
<!-- Modal Structure -->
<div class="modal-body">
  <!-- Left Section: Form -->
  <div class="modal-form">
    <h4>AI Image Generation Form</h4>
    <form method="post" class="leonardo-form">
      <div>
        <label for="prompt" class="leonardo-label">Prompt:</label>
        <textarea id="prompt" name="prompt" rows="4" required></textarea>
      </div>

      <div>
        <label for="negative_prompt" class="leonardo-label">Negative Prompt:</label>
        <textarea name="negative_prompt" id="negative_prompt"></textarea>
      </div>

      <div>
        <label for="width">Width:</label>
        <select name="width" id="width">
          <option value="400">400px</option>
          <option value="512">512px</option>
          <option value="600">600px</option>
          <option value="768" selected="selected">768px</option>
          <option value="1024">1024px</option>
          <option value="1366">1366px</option>
          <option value="1920">1920px</option>
        </select>
      </div>

      <div>
        <label for="height">Height:</label>
        <select name="height" id="height">
          <option value="400">400px</option>
          <option value="512" selected="selected">512px</option>
          <option value="600">600px</option>
          <option value="768">768px</option>
          <option value="1024">1024px</option>
          <option value="1080">1080px</option>
          <option value="1366">1366px</option>
          <option value="1920">1920px</option>
        </select>
      </div>

      <div>
        <label for="photoreal">PhotoReal mode:</label>
        <input type="checkbox" onchange="updateOptions();" name="photoreal" id="photoreal" checked="true">
      </div>

      <div>
        <label for="highresolution">High Resolution:</label>
        <input type="checkbox" name="highresolution" id="highresolution">
      </div>

      <div>
        <label for="photorealstrength">Depth of field:</label>
        <select name="photorealstrength" id="photorealstrength">
          <option value="null">none</option>
          <option value="0.55">low</option>
          <option value="0.5">medium</option>
        </select>
      </div>

      <div>
        <label for="num_images">Number of Images:</label>
        <select name="num_images" id="numImages">
          <option value="1">1</option>
          <option value="2">2</option>
          <option value="3">3</option>
          <option value="4">4</option>
        </select>
      </div>

      <div>
        <label for="presetStyle">Style:</label>
        <select name="presetStyle" id="presetstyle">
          <option value="CINEMATIC">Cinematic (PhotoReal)</option>
          <option value="CREATIVE">Creative  (PhotoReal)</option>
          <option value="VIBRANT">Vibrant  (PhotoReal)</option>
          <option value="ANIME">Anime</option>
          <option value="DYNAMIC">Dynamic</option>
          <option value="ENVIRONMENT">Environment</option>
          <option value="GENERAL">General</option>
          <option value="ILLUSTRATION">Illustration</option>
          <option value="PHOTOGRAPHY">Photography</option>
          <option value="RAYTRACED">Raytraced</option>
          <option value="RENDER_3D">Render 3D</option>
          <option value="SKETCH_BW">Sketch BW</option>
          <option value="SKETCH_COLOR">Sketch Color</option>
          <option value="NONE">None</option>
        </select>
      </div>

      <div>
        <label for="model">Model:</label>
        <select name="model" id="model">
          <option value="null">Geen</option>
          <option value="1e60896f-3c26-4296-8ecc-53e2afecc132">Leonardo Diffusion XL</option>
          <option value="5c232a9e-9061-4777-980a-ddc8e65647c6">Leonardo Vision XL</option>
          <option value="ac614f96-1082-45bf-be9d-757f2d31c174">DreamShaper v7</option>
          <option value="1aa0f478-51be-4efd-94e8-76bfc8f533af">Anime Pastel Dream</option>
          <option value="e316348f-7773-490e-adcd-46757c738eb7">Absolute Reality v1.6</option>
          <option value="458ecfff-f76c-402c-8b85-f09f6fb198de">Deliberate 1.1</option>
          <option value="17e4edbf-690b-425d-a466-53c816f0de8a">Vintage Style Photography</option>
        </select>
      </div>

      <button id="start-generation-button" class="generate-btn">Start Image Generation</button>
    </form>
  </div>

  <!-- Right section: Loading and image preview -->
  <div class="modal-preview">
    <div id="loading-message" class="loading-message" style="display: none;">
      <p>Loading, please wait...</p>
    </div>

    <div id="error-message" class="error-message" style="display: none;"></div>

    <div id="content" style="display: none;">
      <img src="https://via.placeholder.com/400" alt="Generated Image">
    </div>

    <div id="start-save" style="display: none;">
      <button id="start-save-button" class="generate-btn">Save</button>
    </div>
  </div>
</div>
`;

  // Set the modal content and title
  modal.setTitle('Leonardo AI Image Input');
  modal.setContent(modalContent);

  // Open the new modal
  modal.open({ preventClose: true });

  updateOptions();

  document.getElementById('start-generation-button').addEventListener('click', function (event) {
        event.preventDefault(); // Prevent the default form submission

        // Gather form data
        const formData = {
            prompt: document.getElementById('prompt').value,
            negativePrompt: document.getElementById('negative_prompt').value,
            width: document.getElementById('width').value,
            height: document.getElementById('height').value,
            photoreal: document.getElementById('photoreal').checked,
            highResolution: document.getElementById('highresolution').checked,
            depthOfField: document.getElementById('photorealstrength').value,
            num_images: document.getElementById('numImages').value,
            presetStyle: document.getElementById('presetstyle').value,
            model: document.getElementById('model').value
        };

        // Pass the form data to the startImageGeneration function
        startImageGeneration(formData);
    });
}

// Example function to handle image generation
function startImageGeneration(data) {
  console.log('Image generation started with the following data:', data);

  // Simulate an image generation process
  const loadingMessage = document.getElementById('loading-message');
  loadingMessage.style.display = 'block';

  // After the image generation, hide the loading message and show the generated content
  setTimeout(() => {
    loadingMessage.style.display = 'none';
    const content = document.getElementById('content');
    content.innerHTML = `<img src="https://via.placeholder.com/150" alt="Generated Image" />`; // Placeholder for generated image
    content.style.display = 'block';
  }, 2000);
}

editor.on('run:open-assets', () => {
  const modalHeader = document.querySelector('.gjs-mdl-header'); // Select the modal header

  // Check if the button is already added to avoid duplication by using a unique class or ID
  if (!modalHeader.querySelector('.custom-modal-header-btn')) {
    // Create your custom button
    const customButton = document.createElement('button');
    customButton.innerHTML = 'Leonardo AI'; // Button text
    customButton.className = 'gjs-btn-prim custom-modal-header-btn'; // Add a unique class for styling
    customButton.style.marginLeft = 'auto'; // Push the button to the right
    customButton.style.cursor = 'pointer';

    // Add event listener to open the new modal when clicked
    customButton.addEventListener('click', function () {
      openLeonardoModal(); // Call the function to open the new modal
    });

    // Append the button to the modal header
    modalHeader.appendChild(customButton);
  }
});

    // Update options based on the photoreal checkbox state
function updateOptions() {
  const photorealCheckbox = document.getElementById('photoreal');
  const modelSelect = document.getElementById('model');
  const photorealStrengthSelect = document.getElementById('photorealstrength');
  const presetStyleSelect = document.getElementById('presetstyle');

  // Enable/Disable Depth of Field and Model based on the photoreal checkbox
  if (photorealCheckbox.checked) {
    modelSelect.disabled = true;
    modelSelect.value = 'null'; // Set the model to "Geen" when photoreal is enabled
    photorealStrengthSelect.disabled = false; // Enable depth of field
  } else {
    modelSelect.disabled = false;
    modelSelect.value = '1e60896f-3c26-4296-8ecc-53e2afecc132'; // Set the default model when photoreal is disabled
    photorealStrengthSelect.disabled = true; // Disable depth of field
    photorealStrengthSelect.value = 'null';
  }

  // Update the style dropdown
  if (photorealCheckbox.checked) {
    enablePhotorealOptions(presetStyleSelect);
  } else {
    enableNonPhotorealOptions(presetStyleSelect);
  }
}

// Enable photoreal style options
function enablePhotorealOptions(selectElement) {
  const photorealOptions = ['CINEMATIC', 'VIBRANT', 'CREATIVE']; // Photoreal options

  for (let i = 0; i < selectElement.options.length; i++) {
    const option = selectElement.options[i];
    option.disabled = !photorealOptions.includes(option.value); // Enable only photoreal options
  }

  if (selectElement.options[selectElement.selectedIndex].disabled) {
    selectElement.value = 'CINEMATIC'; // Set default photoreal style if current is disabled
  }
}

// Enable non-photoreal style options
function enableNonPhotorealOptions(selectElement) {
  const nonPhotorealOptions = ['ANIME', 'DYNAMIC', 'ENVIRONMENT', 'GENERAL', 'ILLUSTRATION', 'PHOTOGRAPHY', 'RAYTRACED', 'RENDER_3D', 'SKETCH_BW', 'SKETCH_COLOR', 'NONE'];

  for (let i = 0; i < selectElement.options.length; i++) {
    const option = selectElement.options[i];
    option.disabled = !nonPhotorealOptions.includes(option.value); // Enable only non-photoreal options
  }

  if (selectElement.options[selectElement.selectedIndex].disabled) {
    selectElement.value = 'DYNAMIC'; // Set default non-photoreal style if current is disabled
  }
}

// Function to get the domain name from the current URL
function getDomainName() {
    const host = window.location.hostname; // Get the current hostname
    const propixelPattern = /^[0-9]+\.propixel\..+/; // Regex to check if domain starts with an integer and ends with .propixel

    if (propixelPattern.test(host)) {
        // If the domain starts with integers and ends with .propixel, extract the number part
        const numberPart = host.split('.')[0];
        return numberPart; // Return only the integer part
    } else {
        return host; // Return the full domain name if it doesn't match the pattern
    }
}

// Function to handle image generation and API call
function startImageGeneration(data) {
    console.log('Image generation started with the following data:', data);

    // Show loading message
    const loadingMessage = document.getElementById('loading-message');
    loadingMessage.style.display = 'block';

    // Send POST request to the API
    $.post('https://dev01.propixel.nl/test/submit', data)
        .done(function (response) {
            console.log('API response:', response); // Log the response for debugging

            const content = document.getElementById('content');
            let imageUrls = []; // To store all image URLs

            // Clear any previous content before inserting new images
            content.innerHTML = '';

            // Check if image URL exists and add it to the array
            if (response.image_url) {
                // Single image URL
                imageUrls.push(response.image_url);
                content.innerHTML = `<img src="${response.image_url}" alt="Generated Image" />`;
            } else if (response.image_base64) {
                // Base64 encoded image
                imageUrls.push(`data:image/png;base64,${response.image_base64}`);
                content.innerHTML = `<img src="data:image/png;base64,${response.image_base64}" alt="Generated Image" />`;
            } else {
                // If response is some other data
                content.innerHTML = response; // Fallback: append raw response
            }

            // Hide loading message and show content
            loadingMessage.style.display = 'none';
            content.style.display = 'block';

            // Show the save button after successful generation
            const saveButton = document.getElementById('start-save');
            saveButton.style.display = 'block';

            // Bind the click event to the save button
            saveButton.onclick = function () {
                // Get the domain name or website ID
                const domainName = getDomainName();

                // Collect all images inside the content div
                let imagesInContent = content.getElementsByTagName('img');
                let allImageUrls = [];

                // Loop through the images and collect their src attributes
                for (let i = 0; i < imagesInContent.length; i++) {
                    allImageUrls.push(imagesInContent[i].src);
                }

                // Call the function to save the images with the collected URLs and domain name
                saveGeneratedImages(allImageUrls, domainName);
            };
        })
        .fail(function (jqXHR, textStatus, errorThrown) {
            console.error('Error during API request:', textStatus, errorThrown);

            // Handle errors
            loadingMessage.style.display = 'none'; // Hide loading message
            document.getElementById('start-generation-button').style.display = 'block'; // Show the start button again
            document.getElementById('error-message').textContent = 'Something went wrong. Please try again later.';
            document.getElementById('error-message').style.display = 'block'; // Show error message
        });
}

// Function to save the generated images when "Save" button is clicked
function saveGeneratedImages(imageUrls, websiteId) {
    const authToken = '<?php echo $_SESSION["auth_token"]; ?>';
    const saveUrl = 'https://dev01.propixel.nl/save-image';

    // Log the imageUrls to see if they are correctly populated
    console.log("Saving images with URLs: ", imageUrls);

    // Check if imageUrls array is not empty
    if (imageUrls.length === 0) {
        alert('No images to save.');
        return;
    }

    const dataToSend = {
        website: websiteId, // Website ID or domain name
        imageData: imageUrls.map(url => `<img src="${url}" />`).join('') // Convert image URLs to HTML <img> elements
    };

    $.ajax({
        url: saveUrl,
        method: 'POST', // This is a POST request
        data: dataToSend, // Data to send in the request body
        headers: {
            'Authorization': authToken
        },
        success: function (response) {
            console.log('Save Image API response:', response);

            if (response.success) {
                console.log('Images saved:', response.savedImageArray);
                alert('Images saved successfully!'); // Show success message
            } else {
                alert('Failed to save images.'); // Handle failure
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error('Error saving images:', textStatus, errorThrown);
            alert('Error saving images. Please try again.');
        }
    });
}



</script>

<?php
require __DIR__ . '/grapesjs/asset-manager.php';
require __DIR__ . '/grapesjs/component-type-manager.php';
require __DIR__ . '/grapesjs/style-manager.php';
require __DIR__ . '/grapesjs/trait-manager.php';
?>

<button id="toggle-sidebar" class="btn">
    <i class="fa fa-bars"></i>
</button>
<div id="sidebar-header">
    <?php
    if (count(phpb_active_languages()) > 1):
    ?>
    <div id="language-selector">
        <select class="selectpicker" data-width="fit">
            <?php
            foreach (phpb_active_languages() as $languageCode => $languageTranslation):
            ?>
            <option value="<?= phpb_e($languageCode) ?>" <?= $languageCode === $currentLanguage ? 'selected' : '' ?>
                    data-content='<span class="flag-icon flag-icon-<?= phpb_e($languageCode) ?>"></span><span class="language-name ml-1"><?= phpb_e($languageTranslation) ?></span>'>
                >
                <?= phpb_e($languageTranslation) ?>
            </option>
            <?php
            endforeach;
            ?>
        </select>
    </div>
    <?php
    endif;
    ?>
    <style>
        <?php
        foreach (phpb_active_languages() as $languageCode => $languageTranslation):
        ?>
        .flag-icon-<?= $languageCode ?> {
            background-image: url(<?= phpb_asset('pagebuilder/images/flags/' . $languageCode . '.svg') ?>);
        }
        <?php
        endforeach;
        ?>
    </style>
</div>

<div id="sidebar-bottom-buttons">
    <button id="save-page" class="btn" data-url="<?= phpb_url('pagebuilder', ['action' => 'store', 'page' => $page->getId()]) ?>">
        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
        <i class="fa fa-save"></i>
        <?= phpb_trans('pagebuilder.save-page') ?>
    </button>

    <a id="view-page" href="<?= phpb_e(phpb_full_url($page->getRoute())) ?>" target="_blank" class="btn">
        <i class="fa fa-external-link"></i>
        <?= phpb_trans('pagebuilder.view-page') ?>
    </a>

    <a id="go-back" href="<?= phpb_e(phpb_full_url(phpb_config('pagebuilder.actions.back'))) ?>" class="btn">
        <i class="fa fa-arrow-circle-left"></i>
        <?= phpb_trans('pagebuilder.go-back') ?>
    </a>
</div>

<div id="block-search">
    <i class="fa fa-search"></i>
    <input type="text" class="form-control" placeholder="<?= phpb_trans('pagebuilder.filter-placeholder') ?>">
</div>

<style>
.cke_notifications_area {
    display: none !important;
}
</style>