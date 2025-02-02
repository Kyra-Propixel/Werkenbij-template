<?php

namespace PHPageBuilder\Modules\WebsiteManager;

use PHPageBuilder\Contracts\PageContract;
use PHPageBuilder\Contracts\WebsiteManagerContract;
use PHPageBuilder\Extensions;
use PHPageBuilder\Repositories\PageRepository;
use PHPageBuilder\Repositories\SettingRepository;
use PHPageBuilder\Repositories\HeaderSettingRepository;

class WebsiteManager implements WebsiteManagerContract
{
    /**
     * Process the current GET or POST request and redirect or render the requested page.
     *
     * @param $route
     * @param $action
     */
    public function handleRequest($route, $action)
    {
        // Render the default overview page
        if (is_null($route)) {
            $this->renderOverview();
            exit();
        }

        // Handle settings route
        if ($route === 'settings') {
            if ($action === 'renderBlockThumbs') {
                $this->renderBlockThumbs();
                exit();
            }
            if ($action === 'update') {
                $this->handleUpdateSettings();
                exit();
            }
        }

        // Handle headerSettings route
        if ($route === 'header_settings') {
            if ($action === 'update') {
                $this->handleUpdateHeaderSettings();
                exit();
            }
        }

        // Handle page settings route
        if ($route === 'page_settings') {
            if ($action === 'create') {
                $this->handleCreate();
                exit();
            }

            $pageId = $_GET['page'] ?? null;
            $pageRepository = new PageRepository;
            $page = $pageRepository->findWithId($pageId);
            if (! ($page instanceof PageContract)) {
                phpb_redirect(phpb_url('website_manager'));
            }

            if ($action === 'edit') {
                $this->handleEdit($page);
                exit();
            } elseif ($action === 'destroy') {
                $this->handleDestroy($page);
            }
        }
    }


    /**
     * Handle requests for creating a new page.
     */
    public function handleCreate()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pageRepository = new PageRepository;
            $page = $pageRepository->create($_POST);
            if ($page) {
                phpb_redirect(phpb_url('website_manager'), [
                    'message-type' => 'success',
                    'message' => phpb_trans('website-manager.page-created')
                ]);
            }
        }

        $this->renderPageSettings();
    }

    /**
     * Handle requests for editing the given page.
     *
     * @param PageContract $page
     */
    public function handleEdit(PageContract $page)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pageRepository = new PageRepository;
            $success = $pageRepository->update($page, $_POST);
            if ($success) {
                phpb_redirect(phpb_url('website_manager'), [
                    'message-type' => 'success',
                    'message' => phpb_trans('website-manager.page-updated')
                ]);
            }
        }

        $this->renderPageSettings($page);
    }

    /**
     * Handle requests to destroy the given page.
     *
     * @param PageContract $page
     */
    public function handleDestroy(PageContract $page)
    {
        $pageRepository = new PageRepository;
        $pageRepository->destroy($page->getId());
        phpb_redirect(phpb_url('website_manager'), [
            'message-type' => 'success',
            'message' => phpb_trans('website-manager.page-deleted')
        ]);
    }

    /**
     * Handle requests for updating the website settings.
     */
    public function handleUpdateSettings()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $settingRepository = new SettingRepository;
            $success = $settingRepository->updateSettings($_POST);
            if ($success) {
                phpb_redirect(phpb_url('website_manager', ['tab' => 'settings']), [
                    'message-type' => 'success',
                    'message' => phpb_trans('website-manager.settings-updated')
                ]);
            }
        }
    }
    /**
     * Handle requests for updating the website settings.
     */
    public function handleUpdateHeaderSettings()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $headerSettingRepository = new HeaderSettingRepository;
            $success = $headerSettingRepository->updateSettings($_POST);
            if ($success) {
                phpb_redirect(phpb_url('website_manager', ['tab' => 'menus']), [
                    'message-type' => 'success',
                    'message' => "De header bijgewerkt."
                ]);
            }
        }
    }

    /**
     * Render the website manager overview page.
     */
    public function renderOverview()
    {

        $headerSettingRepository = new HeaderSettingRepository();
        $headerSettings = $headerSettingRepository->getAll();

        // Initialize variables
        $headerLogo = '';
        $headerBackground = '#ffffff';
        $headerItems = [];

        // Loop through the retrieved settings and assign values
        foreach ($headerSettings as $setting) {
            if ($setting['setting'] === 'header_logo') {
                $headerLogo = $setting['value'];  // Assign logo value
            } elseif ($setting['setting'] === 'header_background') {
                $headerBackground = $setting['value'];  // Assign background color value
            } elseif ($setting['setting'] === 'header_item') {
                // Since header items are JSON-encoded, we decode them and store them in the array
                $headerItems[] = json_decode($setting['value'], true);
            }
        }
 
        $pageRepository = new PageRepository;
        $pages = $pageRepository->getAll();

        $viewFile = 'overview';
        require __DIR__ . '/resources/layouts/master.php';
    }

    /**
     * Render the website manager page settings (add/edit page form).
     *
     * @param PageContract $page
     */
    public function renderPageSettings(PageContract $page = null)
    {
        $action = isset($page) ? 'edit' : 'create';
        $theme = phpb_instance('theme', [
            phpb_config('theme'), 
            phpb_config('theme.active_theme')
        ]);

        $viewFile = 'page-settings';
        require __DIR__ . '/resources/layouts/master.php';
    }

    /**
     * Render the website manager menu settings (add/edit menu form).
     */
    
     public function renderMenuSettings()
     {
         
 
         $viewFile = 'menu-settings';
         require __DIR__ . '/resources/layouts/master.php';
     }

    /**
     * Render a thumbnail for each theme block.
     */
    public function renderBlockThumbs()
    {
        $viewFile = 'block-thumbs';
        require __DIR__ . '/resources/layouts/master.php';
    }

    public function renderMenuItemsAPI(){
        $headerSettingRepository = new HeaderSettingRepository();
        $headerSettings = $headerSettingRepository->getAll();
    
        $headerItems = array_filter($headerSettings, function ($setting) {
            return $setting['setting'] === 'header_item' || $setting['setting'] === 'header_background';
        });
    
        header('Content-Type: application/json');
        echo json_encode($headerItems);
        exit();
    }
    

    /**
     * Render the website manager welcome page for installations without a homepage.
     */
    public function renderWelcomePage()
    {
        $viewFile = 'welcome';
        require __DIR__ . '/resources/layouts/empty.php';
    }
}
