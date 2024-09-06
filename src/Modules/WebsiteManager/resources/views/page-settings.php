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

// Extract domain name from the configuration file
$domainName = $config['general']['domain_name'];

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