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
 * Load the max pages allowed per plan from the `website_restrictions` table.
 */
function loadMaxPagesByPlan(&$rootDb)
{
    if ($rootDb === null) {
        connectRootDatabase($rootDb);
    }

    $query = "SELECT plan, max_pages FROM website_restrictions";
    $result = $rootDb->query($query);

    $maxPagesByPlan = [];
    while ($row = $result->fetch_assoc()) {
        $maxPagesByPlan[$row['plan']] = $row['max_pages'];
    }

    return $maxPagesByPlan;
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

// Dynamically load the max pages by plan from the database
$maxPagesByPlan = loadMaxPagesByPlan($rootDb);

// Determine the maximum number of pages allowed for the user's subscription plan
$maxPages = isset($maxPagesByPlan[$subscriptionPlan]) ? $maxPagesByPlan[$subscriptionPlan] : 0;

?>

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
<div class="py-5 text-center">
    <h2><?= phpb_trans('website-manager.title') ?></h2>
</div>

<div class="row">
    <div class="col-12">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link <?= phpb_e($pagesTabActive) ?>" data-toggle="tab" href="#pages"><?= phpb_trans('website-manager.pages') ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= phpb_e($menusTabActive) ?>" data-toggle="tab" href="#menus">Header</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= phpb_e($settingsTabActive) ?>" data-toggle="tab" href="#settings"><?= phpb_trans('website-manager.settings') ?></a>
            </li>
        </ul>

        <div class="tab-content">
            <div id="pages" class="tab-pane <?= phpb_e($pagesTabActive) ?>">

                <h4><?= phpb_trans('website-manager.pages') ?></h4>

                <div class="main-spacing">
                    <?php if (phpb_flash('message')): ?>
                        <div class="alert alert-<?= phpb_flash('message-type') ?>">
                            <?= phpb_flash('message') ?>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col"><?= phpb_trans('website-manager.name') ?></th>
                                    <th scope="col"><?= phpb_trans('website-manager.route') ?></th>
                                    <th scope="col"><?= phpb_trans('website-manager.actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pages as $page): ?>
                                    <tr>
                                        <td><?= phpb_e($page->getName()) ?></td>
                                        <td><?= phpb_e($page->getRoute()) ?></td>
                                        <td class="actions">
                                            <a href="<?= phpb_e(phpb_full_url($page->getRoute())) ?>" target="_blank" class="btn btn-light btn-sm">
                                                <span><?= phpb_trans('website-manager.view') ?></span> <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= phpb_url('pagebuilder', ['page' => $page->getId()]) ?>" class="btn btn-primary btn-sm">
                                                <span><?= phpb_trans('website-manager.edit') ?></span> <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= phpb_url('website_manager', ['route' => 'page_settings', 'action' => 'edit', 'page' => $page->getId()]) ?>" class="btn btn-secondary btn-sm">
                                                <span><?= phpb_trans('website-manager.settings') ?></span> <i class="fas fa-cog"></i>
                                            </a>
                                            <a href="<?= phpb_url('website_manager', ['route' => 'page_settings', 'action' => 'destroy', 'page' => $page->getId()]) ?>" class="btn btn-danger btn-sm">
                                                <span><?= phpb_trans('website-manager.remove') ?></span> <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <hr class="mb-3">

                <?php if ($pageCount >= $maxPages): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>&nbsp;
                        <div>
                            <strong>Let op!</strong> Uw <span class="text-capitalize"><?= $subscriptionPlan ?></span> plan biedt slechts <strong><?= $maxPages ?></strong> pagina's aan. Wilt u upgraden voor meer pagina's?
                        </div>
                        <hr>
                        <a href="https://propixel.nl" target="_blank" class="btn btn-success btn-sm ms-auto">Plan Upgraden</a>
                    </div>
                <?php else: ?>
                    <a href="<?= phpb_url('website_manager', ['route' => 'page_settings', 'action' => 'create']) ?>" class="btn btn-primary btn-sm">
                        <?= phpb_trans('website-manager.add-new-page') ?>
                    </a>
                <?php endif; ?>

            </div>

            <div id="menus" class="tab-pane <?= phpb_e($menusTabActive) ?>">
                <h4 class="mb-3">Menu Items</h4>
                <?php require __DIR__ . '/header-settings.php'; ?>
            </div>

            <div id="settings" class="tab-pane <?= phpb_e($settingsTabActive) ?>">
                <h4 class="mb-3"><?= phpb_trans('website-manager.settings') ?></h4>
                <?php require __DIR__ . '/settings.php'; ?>
            </div>
        </div>
    </div>
</div>