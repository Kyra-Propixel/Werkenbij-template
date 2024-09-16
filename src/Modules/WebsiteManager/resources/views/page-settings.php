<style>
  /* General Body Styling */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f6f9;
    color: #333;
    margin: 0;
    padding: 0;
    line-height: 1.5;
}

/* Container for main content */
.container {
    padding: 20px;
}

/* Header Title */
h2 {
    font-weight: 600;
    color: #1a202c;
    font-size: 24px;
    margin-bottom: 20px;
}

/* Tab Navigation Styling */
.nav-tabs {
    border-bottom: 1px solid #ddd;
    margin-bottom: 15px;
}

.nav-tabs .nav-item {
    margin-bottom: -1px;
}

.nav-tabs .nav-link {
    background-color: transparent;
    border: none;
    padding: 8px 15px;
    color: #555;
    font-size: 14px;
    font-weight: 500;
    border-bottom: 2px solid transparent;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    color: #0a74da;
    border-bottom: 2px solid #0a74da;
}

.nav-tabs .nav-link.active {
    color: #0a74da;
    border-bottom: 2px solid #0a74da;
}

/* Tab Content */
.tab-content {
    background-color: #fff;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 6px;
}

/* Table Styling */
.table {
    width: 100%;
    margin-bottom: 15px;
    border-collapse: collapse;
    font-size: 14px;
}

.table thead {
    background-color: #f8f9fb;
}

.table thead th {
    color: #495057;
    font-weight: 600;
    padding: 12px 15px;
    border-bottom: 2px solid #e5e7eb;
    text-align: left;
}

.table tbody td {
    padding: 12px 15px;
    background-color: #fff;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
    font-size: 13px;
}

/* Numeric or action columns right-align */
.table tbody td.text-right {
    text-align: right;
}

.table-bordered {
    border: 1px solid #e5e7eb;
}

.table-hover tbody tr:hover {
    background-color: #f4f6f9;
}

/* Add subtle borders between table rows */
.table tbody tr {
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    transition: box-shadow 0.2s ease;
}

/* Right-align buttons and actions in table rows */
.table .actions {
    text-align: right;
    white-space: nowrap;
}

.table .actions .btn {
    margin-left: 5px;
}

/* Button Styling */
.btn {
    font-weight: 500;
    border-radius: 3px;
    padding: 6px 12px;
    font-size: 12px;
    transition: background-color 0.2s ease, box-shadow 0.2s ease;
}

.btn-primary {
    background-color: #0a74da;
    border-color: #0a74da;
    color: white;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #5a6268;
}

.btn-light {
    background-color: #fff;
    border-color: #ddd;
    color: #333;
}

.btn-light:hover {
    background-color: #f7f9fb;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
}

.btn-danger:hover {
    background-color: #c82333;
}

/* Alerts and Notifications - 3D Box Style */
.alert {
    padding: 12px 15px;
    border-radius: 6px;
    font-size: 13px;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    border: 1px solid transparent;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    background-color: #fff;
}

.alert i {
    margin-right: 10px;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border-color: #f5c6cb;
}

.alert-danger i {
    color: #721c24;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border-color: #c3e6cb;
}

.alert-success i {
    color: #155724;
}

.alert-info {
    background-color: #d1ecf1;
    color: #0c5460;
    border-color: #bee5eb;
}

.alert-info i {
    color: #0c5460;
}

.alert .btn {
    margin-left: auto;
}

/* Card Styling */
.card {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 6px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    padding: 20px;
    margin-bottom: 15px;
}

/* Table Responsive */
.table-responsive {
    border: 1px solid #ddd;
    border-radius: 6px;
    overflow-x: auto;
}

/* Main Content Spacing */
.main-spacing {
    padding: 20px;
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    .nav-tabs .nav-link {
        padding: 10px;
        font-size: 12px;
    }

    .btn {
        font-size: 11px;
    }

    .table {
        font-size: 12px;
    }
}
</style>

<?php

$baseDir = __DIR__;
$pathParts = explode('/', $baseDir);
$domainName = $pathParts[3];

$configPath = "/var/www/{$domainName}/public/phpagebuilder/config/config.php";

if (file_exists($configPath)) {
    $config = require $configPath;
} else {
    die("Configuration file not found at: $configPath");
}

$pagesTabActive = !isset($_GET['tab']) || $_GET['tab'] === 'pages' ? 'active' : '';
$menusTabActive = isset($_GET['tab']) && $_GET['tab'] === 'menus' ? 'active' : '';
$settingsTabActive = isset($_GET['tab']) && $_GET['tab'] === 'settings' ? 'active' : '';

$rootDb = null;
$websiteDb = null;

/**
 * Connect to the root database using root credentials
 */
function connectRootDatabase(&$rootDb)
{
    $rootDb = new \mysqli('localhost', 'aiwebgen_dbadmin', '4Tg0:6"@][q}', 'aiwebgenerator');

    if ($rootDb->connect_error) {
        die('Root database connection error: ' . $rootDb->connect_error);
    }
}

/**
 * Get the subscription plan for the given domain
 */
function getSubscriptionPlan($domainName, &$rootDb)
{
    if ($rootDb === null) {
        connectRootDatabase($rootDb);
    }

    $query = "SELECT subscription FROM websites WHERE domain_name = ?";
    $stmt = $rootDb->prepare($query);
    $stmt->bind_param('s', $domainName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['subscription'];
    }

    return null;
}

/**
 * Get the maximum number of pages allowed for the given subscription plan
 */
function getMaxPagesByPlan($subscriptionPlan, &$rootDb)
{
    if ($rootDb === null) {
        connectRootDatabase($rootDb);
    }

    $query = "SELECT max_pages FROM website_restrictions WHERE plan = ?";
    $stmt = $rootDb->prepare($query);
    $stmt->bind_param('s', $subscriptionPlan);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['max_pages'];
    }

    return 0;  // Default to 0 if the plan is not found
}

/**
 * Connect to the website's database using credentials from config
 */
function connectWebsiteDatabase(&$websiteDb, $config)
{
    $dbConfig = $config['storage']['database'];
    $websiteDb = new \mysqli($dbConfig['host'], $dbConfig['username'], $dbConfig['password'], $dbConfig['database']);

    if ($websiteDb->connect_error) {
        die('Website database connection error: ' . $websiteDb->connect_error);
    }
}

/**
 * Get the total number of pages for the website
 */
function getPageCount(&$websiteDb, $config)
{
    if ($websiteDb === null) {
        connectWebsiteDatabase($websiteDb, $config);
    }

    $query = "SELECT COUNT(*) as total_pages FROM pages";
    $result = $websiteDb->query($query);
    $row = $result->fetch_assoc();

    return $row['total_pages'];
}

// Fetch the subscription plan and page count
$subscriptionPlan = getSubscriptionPlan($domainName, $rootDb);
$pageCount = getPageCount($websiteDb, $config);

// Fetch the maximum number of pages allowed for the subscription plan
$maxPages = getMaxPagesByPlan($subscriptionPlan, $rootDb);

// If the action is 'create' and the user has reached their page limit, redirect to /admin
if ($action === 'create' && $pageCount >= $maxPages) {
    header("Location: /admin");
    exit();
}

$pageUrlParam = '';
if (isset($page)) {
    $pageUrlParam = '&page=' . phpb_e($page->getId());
}

$pageTranslations = $page ? $page->getTranslations() : [];
?>

<div class="py-5 text-center">
    <h2><?= phpb_trans('website-manager.title') ?></h2>
</div>

<div class="row">
    <div class="col-12">
        <div class="manager-panel">
            <form method="post" action="<?= phpb_url('website_manager', ['route' => 'page_settings', 'action' => $action]) ?><?= $pageUrlParam ?>">
                <h4>
                    <?php
                    if ($action === 'create'):
                        echo phpb_trans('website-manager.add-new-page');
                    else:
                        echo phpb_trans('website-manager.edit-page');
                    endif;
                    ?>
                </h4>

                <div class="main-spacing">
                    <div class="form-group required">
                        <label for="name">
                            <?= phpb_trans('website-manager.name') ?>
                            <span class="text-muted">(<?= phpb_trans('website-manager.visible-in-page-overview') ?>)</span>
                        </label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= phpb_field_value('name', $page) ?>" required>
                    </div>

                    <div class="form-group required">
                        <label for="layout"><?= phpb_trans('website-manager.layout') ?></label>
                        <select class="form-control" id="layout" name="layout" required>
                            <?php
                            $value = phpb_field_value('layout', $page);
                            foreach ($theme->getThemeLayouts() as $layout):
                                if ($layout->getSlug() === $value):
                                    echo '<option value="' . phpb_e($layout->getSlug()) . '" selected>' . phpb_e($layout->getTitle()) . '</option>';
                                else:
                                    echo '<option value="' . phpb_e($layout->getSlug()) . '">' . phpb_e($layout->getTitle()) . '</option>';
                                endif;
                            endforeach;
                            ?>
                        </select>
                    </div>

                    <?php
                    foreach (array_keys(phpb_active_languages()) as $languageCode):
                    ?>
                    <h5 class="pt-2"><?= phpb_trans('languages.' . $languageCode) ?></h5>
                    <div class="pt-2 pl-3 pr-3">
                        <div class="form-group required">
                            <label for="page-title"><?= phpb_trans('website-manager.page-title') ?></label>
                            <input type="text" class="form-control" id="page-title" name="title[<?= phpb_e($languageCode) ?>]" value="<?= phpb_e($pageTranslations[$languageCode]['title'] ?? '') ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="page-meta-title"><?= phpb_trans('website-manager.page-meta-title') ?></label>
                            <input type="text" class="form-control" id="page-meta-title" name="meta_title[<?= phpb_e($languageCode) ?>]" value="<?= phpb_e($pageTranslations[$languageCode]['meta_title'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label for="page-meta-description"><?= phpb_trans('website-manager.page-meta-description') ?></label>
                            <input type="text" class="form-control" id="page-meta-description" name="meta_description[<?= phpb_e($languageCode) ?>]" value="<?= phpb_e($pageTranslations[$languageCode]['meta_description'] ?? '') ?>">
                        </div>

                        <div class="form-group required">
                            <label for="route"><?= phpb_trans('website-manager.route') ?></label>
                            <input type="text" class="form-control" id="route" name="route[<?= phpb_e($languageCode) ?>]" value="<?= phpb_e($pageTranslations[$languageCode]['route'] ?? '') ?>" required>
                        </div>
                    </div>
                    <?php
                    endforeach;
                    ?>
                </div>

                <hr class="mb-3">

                <a href="<?= phpb_url('website_manager') ?>" class="btn btn-light btn-sm mr-1">
                    <?= phpb_trans('website-manager.back') ?>
                </a>
                <button class="btn btn-primary btn-sm">
                    <?php
                    if ($action === 'create'):
                        echo phpb_trans('website-manager.add-new-page');
                    else:
                        echo phpb_trans('website-manager.save-changes');
                    endif;
                    ?>
                </button>
            </form>
        </div>
    </div>
</div>