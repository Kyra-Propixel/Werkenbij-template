<?php
$setting = phpb_instance('setting');
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

<form method="post" action="<?= phpb_url('website_manager', ['route' => 'settings', 'action' => 'update', 'tab' => 'settings']) ?>">

    <div class="main-spacing">
        <?php
        if (phpb_flash('message')):
        ?>
        <div class="alert alert-<?= phpb_flash('message-type') ?>">
            <?= phpb_flash('message') ?>
        </div>
        <?php
        endif;
        ?>

        <div class="form-group required">
            <label for="languages">
                <?= phpb_trans('website-manager.website-languages') ?>
            </label>
            <select class="form-control" id="languages" name="languages[]" title="<?= phpb_trans('website-manager.languages-selector-placeholder') ?>" required multiple>
                <?php
                foreach (phpb_trans('languages') as $locale => $localeText):
                ?>
                <option value="<?= phpb_e($locale) ?>" <?= phpb_e($setting::has('languages', $locale)) !== '' && phpb_e($setting::has('languages', $locale)) !== '0' ? 'selected' : '' ?>><?= phpb_e($localeText) ?></option>
                <?php
                endforeach;
                ?>
            </select>
        </div>

        <hr class="mb-3">

        <button class="btn btn-primary btn-sm">
            <?= phpb_trans('website-manager.save-settings'); ?>
        </button>
    </div>

    <div class="main-spacing mt-5">
        <label class="d-block">
            <?= phpb_trans('website-manager.pagebuilder-block-images') ?>
        </label>
        <a href="<?= phpb_url('website_manager', ['route' => 'settings', 'action' => 'renderBlockThumbs']) ?>" class="btn btn-secondary btn-sm mr-1">
            <?= phpb_trans('website-manager.render-thumbs') ?>
        </a>
    </div>

</form>
