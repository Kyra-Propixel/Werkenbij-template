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
                        <table class="table">
                            <thead>
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
                <h4 class="mb-3">Dynamische Items Header en Footer</h4>
                <?php require __DIR__ . '/header-settings.php'; ?>
            </div>

            <div id="settings" class="tab-pane <?= phpb_e($settingsTabActive) ?>">
                <h4 class="mb-3"><?= phpb_trans('website-manager.settings') ?></h4>
                <?php require __DIR__ . '/settings.php'; ?>
            </div>
        </div>
    </div>
</div>