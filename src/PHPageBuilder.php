<?php

namespace PHPageBuilder;

use PHPageBuilder\Contracts\AuthContract;
use PHPageBuilder\Contracts\PageContract;
use PHPageBuilder\Contracts\PageTranslationContract;
use PHPageBuilder\Contracts\WebsiteManagerContract;
use PHPageBuilder\Contracts\PageBuilderContract;
use PHPageBuilder\Contracts\RouterContract;
use PHPageBuilder\Contracts\ThemeContract;
use PHPageBuilder\Modules\GrapesJS\PageRenderer;
use PHPageBuilder\Repositories\UploadRepository;
use PHPageBuilder\Core\DB;

class PHPageBuilder
{
    /**
     * @var AuthContract $auth
     */
    protected $auth;

    /**
     * @var WebsiteManagerContract $websiteManager
     */
    protected $websiteManager;

    /**
     * @var PageBuilderContract $pageBuilder
     */
    protected $pageBuilder;

    /**
     * @var RouterContract $router
     */
    protected $router;

    /**
     * @var ThemeContract $theme
     */
    protected $theme;

    /**
     * PHPageBuilder constructor.
     *
     * @param array|null $config         configuration in the format defined in config/config.example.php
     */
    public function __construct($config = [])
    {
        // do nothing if no config is provided (e.g. during composer install)
        if (empty($config)) {
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // if flash session data is set, set global session flash data and remove data
        if (isset($_SESSION['phpb_flash'])) {
            global $phpb_flash;
            $phpb_flash = $_SESSION['phpb_flash'];
            unset($_SESSION['phpb_flash']);
        }

        $this->setConfig($config);

        // create database connection, if enabled
        if (phpb_config('storage.use_database')) {
            $this->setDatabaseConnection(phpb_config('storage.database'));
        }

        // init the default authentication, if enabled
        if (phpb_config('auth.use_login')) {
            $this->auth = phpb_instance('auth');
        }

        // init the default website manager, if enabled
        if (phpb_config('website_manager.use_website_manager')) {
            $this->websiteManager = phpb_instance('website_manager');
        }

        // init the default page builder, active theme and page router
        $this->pageBuilder = phpb_instance('pagebuilder');

        $this->theme = phpb_instance('theme', [
            phpb_config('theme'), 
            phpb_config('theme.active_theme')
        ]);

        $this->router = phpb_instance('router');

        // load translations in the language that is currently active
        $this->loadTranslations(phpb_current_language());
    }

    /**
     * Load translations of the given language into a global variable.
     *
     * @param $language
     * @return array
     */
    public function loadTranslations($language)
    {
        global $phpb_translations;

        $phpbLanguageFile = __DIR__ . '/../lang/' . $language . '.php';
        if (! file_exists($phpbLanguageFile)) {
            $phpbLanguageFile = __DIR__ . '/../lang/en.php';
        }
        $phpb_translations = require $phpbLanguageFile;

        // load default and current language translations of the current theme
        $themeTranslationsFolder = phpb_config('theme.folder') . '/' . phpb_config('theme.active_theme') . '/translations';
        if (file_exists($themeTranslationsFolder . '/en.php')) {
            $phpb_translations = array_merge($phpb_translations, require $themeTranslationsFolder . '/en.php');
        }
        if (file_exists($themeTranslationsFolder . '/' . $language . '.php')) {
            $phpb_translations = array_merge($phpb_translations, require $themeTranslationsFolder . '/' . $language . '.php');
        }

        $phpb_translations = phpb_instance(Translator::class)->customize($phpb_translations);
        return $phpb_translations;
    }


    /**
     * Set the PHPageBuilder configuration to the given array.
     *
     * @param array $config
     */
    public function setConfig(array $config)
    {
        global $phpb_config;
        $phpb_config = $config;
    }

    /**
     * Set the PHPageBuilder database connection using the given array.
     *
     * @param array $config
     */
    public function setDatabaseConnection(array $config)
    {
        global $phpb_db;
        $phpb_db = new DB($config);
    }

    /**
     * Set a custom auth.
     *
     * @param AuthContract $auth
     */
    public function setAuth(AuthContract $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Set a custom website manager.
     *
     * @param WebsiteManagerContract $websiteManager
     */
    public function setWebsiteManager(WebsiteManagerContract $websiteManager)
    {
        $this->websiteManager = $websiteManager;
    }

    /**
     * Set a custom PageBuilder.
     *
     * @param PageBuilderContract $pageBuilder
     */
    public function setPageBuilder(PageBuilderContract $pageBuilder)
    {
        $this->pageBuilder = $pageBuilder;
    }

    /**
     * Set a custom router.
     *
     * @param RouterContract $router
     */
    public function setRouter(RouterContract $router)
    {
        $this->router = $router;
    }

    /**
     * Set a custom theme.
     *
     * @param ThemeContract $theme
     */
    public function setTheme(ThemeContract $theme)
    {
        $this->theme = $theme;
        if ($this->pageBuilder !== null) {
            $this->pageBuilder->setTheme($theme);
        }
    }


    /**
     * Return the Auth instance of this PHPageBuilder.
     *
     * @return AuthContract
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * Return the WebsiteManager instance of this PHPageBuilder.
     *
     * @return WebsiteManagerContract
     */
    public function getWebsiteManager()
    {
        return $this->websiteManager;
    }

    /**
     * Return the PageBuilder instance of this PHPageBuilder.
     *
     * @return PageBuilderContract
     */
    public function getPageBuilder()
    {
        return $this->pageBuilder;
    }

    /**
     * Return the Router instance of this PHPageBuilder.
     *
     * @return RouterContract
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Return the Theme instance of this PHPageBuilder.
     *
     * @return ThemeContract
     */
    public function getTheme()
    {
        return $this->theme;
    }


    /**
     * Process the current GET or POST request and redirect or render the requested page.
     *
     * @param string|null $action
     * @return bool
     */
    public function handleRequest($action = null)
    {
        $route = $route ?? $_GET['route'] ?? null;
        $action = $action ?? $_GET['action'] ?? null;

        if (! phpb_config('auth.use_login') || ! phpb_config('website_manager.use_website_manager')) {
            die('The PHPageBuilder Authentication module is disabled, but no alternative has been implemented (you are still calling the standard handleRequest() method).<br>'
                . 'Implement a piece of code that checks whether the user is logged in. If logged in, call handleAuthenticatedRequest() or else call handlePublicRequest().');
        }

        // handle login and logout requests
        $this->auth->handleRequest($action);

        // handle website manager requests
        if (phpb_in_module('website_manager')) {
            $this->auth->requireAuth();
            $this->websiteManager->handleRequest($route, $action);
            header("HTTP/1.1 404 Not Found");
            die('PHPageBuilder WebsiteManager page not found');
        }

        // handle page builder requests
        if (phpb_in_module('pagebuilder')) {
            $this->auth->requireAuth();
            phpb_set_in_editmode();
            $this->pageBuilder->handleRequest($route, $action);
            header("HTTP/1.1 404 Not Found");
            die('PHPageBuilder PageBuilder page not found');
        }

        // handle all requests that do not need authentication
        if ($this->handlePublicRequest()) {
            return true;
        }

        if (phpb_current_relative_url() === '/') {
            $this->websiteManager->renderWelcomePage();
            return true;
        }

        if (phpb_current_relative_url() === '/api/renderMenuItems') {
            $this->websiteManager->renderMenuItemsAPI();
            return true;
        }

        header("HTTP/1.1 404 Not Found");
        die('
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina Niet Gevonden</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .error-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            text-align: center;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease-in-out forwards;
        }

        h1 {
            font-size: 26px;
            color: #ff4f4f;
            margin-bottom: 10px;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease-in-out forwards;
            animation-delay: 0.1s;
        }

        p {
            color: #555;
            font-size: 16px;
            margin-bottom: 20px;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease-in-out forwards;
            animation-delay: 0.2s;
        }

        .url-box {
            background-color: #f4f4f4;
            padding: 10px;
            border-radius: 4px;
            font-family: monospace;
            word-wrap: break-word;
            margin-bottom: 20px;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease-in-out forwards;
            animation-delay: 0.3s;
        }

        .info-box {
            background-color: #e7f3fe;
            padding: 20px;
            border: 1px solid #b3d4fc;
            border-radius: 8px;
            color: #31708f;
            text-align: left;
            font-size: 14px;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease-in-out forwards;
            animation-delay: 0.4s;
        }

        a {
            display: inline-block;
            padding: 12px 25px;
            background-color: #ff4f4f;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: background-color 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease-in-out forwards;
            animation-delay: 0.5s;
        }

        a:hover {
            background-color: #e84343;
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        hr {
            border: 0;
            height: 1px;
            background: #ddd;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <img src="https://dev01.propixel.nl//Logo_Transparante-achtergrond.svg" height="50px" alt="Bedrijfslogo" class="mb-4 fade-in">
        <h1>Whoops! 404 - Pagina Niet Gevonden</h1>
        <p>We hebben de pagina die je zoekt niet kunnen vinden.</p>
        <div class="url-box"> ' . phpb_e(phpb_full_url(phpb_current_relative_url())) . '</div>
        <a href="/">Ga naar de Homepage</a>

        <hr>

        <div class="info-box">
            <h2>Was dit een pagina die je hebt aangemaakt?</h2>
            <p>Om een route toe te voegen, volg de onderstaande stappen:</p>
            <ol>
                <li>Log in op het dashboard.</li>
                <li>Navigeer naar <strong>Pagina</strong> in het menu.</li>
                <li>Klik op <strong>Nieuwe pagina toevoegen</strong>.</li>
                <li>Voer de URL in, bijvoorbeeld <strong><?= phpb_e(phpb_current_relative_url()) ?></strong> als de URL die je wilt aanmaken is <strong><?= phpb_e(phpb_current_relative_url()) ?></strong>.</li>
                <li>Vul de rest van de paginagegevens in en sla de pagina op.</li>
            </ol>
            <p>Als de URL die je probeert te bezoeken <strong><?= phpb_e(phpb_current_relative_url()) ?></strong> is, volg dan de bovenstaande stappen om deze toe te voegen aan je site.</p>
        </div>
    </div>
</body>
</html>

');
    }

    /**
     * Handle public requests, allowed without any authentication.
     *
     * @return bool
     */
    public function handlePublicRequest()
    {
        // if we are on the URL of an upload, return uploaded file
        // (note: this is a fallback option used if .htaccess does not whitelist direct access to the /uploads folder.
        // allowing direct /uploads access via .htaccess is preferred since it gives faster loading time)
        if (strpos(phpb_current_relative_url(), phpb_config('general.uploads_url') . '/') === 0) {
            $this->handleUploadedFileRequest();
            header("HTTP/1.1 404 Not Found");
            exit();
        }
        // if we are on the URL of a PHPageBuilder asset, return the asset
        if (strpos(phpb_current_relative_url(), phpb_config('general.assets_url') . '/') === 0) {
            $this->handlePageBuilderAssetRequest();
            header("HTTP/1.1 404 Not Found");
            exit();
        }

        // try to find page in cache
        $cache = phpb_instance('cache');
        if (phpb_config('cache.enabled') &&
            ! isset($_GET['ignore_cache']) &&
            ! isset($_GET['refresh_cache']) &&
            ! isset($_COOKIE['ignore_cache']) &&
            PageRenderer::canBeCached()
        ) {
            $cachedContent = $cache->getForUrl(phpb_current_relative_url());
            if ($cachedContent) {
                echo $cachedContent;
                return true;
            }
        }

        // let the page router resolve the current URL
        $page = null;
        $pageTranslation = $this->resolvePageLanguageVariantFromUrl(phpb_current_relative_url());
        if ($pageTranslation !== null) {
            $page = $pageTranslation->getPage();
        }
        // if the URL cannot be resolved, but the lowercase version of the URL can be resolved, redirect to the lowercase URL
        if (($page->logic ?? '') === 'page-not-found' && phpb_current_relative_url() !== strtolower(phpb_current_relative_url())) {
            $pageLowerCaseUrlTranslation = $this->resolvePageLanguageVariantFromUrl(strtolower(phpb_current_relative_url()));
            if ($pageLowerCaseUrlTranslation !== null) {
                $pageLowerCaseUrl = $pageLowerCaseUrlTranslation->getPage();
                if (($pageLowerCaseUrl->logic ?? '') !== 'page-not-found') {
                    header("HTTP/1.1 301 Moved Permanently");
                    header("Location: " . strtolower(phpb_current_relative_url()));
                    exit();
                }
            }
        }
        // render page if resolved
        if ($page !== null) {
            $renderedContent = $this->pageBuilder->renderPage($page, $pageTranslation->locale);
            if (strpos($pageTranslation->route, '/*') === false) {
                $this->cacheRenderedPage($renderedContent);
            }
            echo $renderedContent;
            return true;
        }
        return false;
    }

    /**
     * Resolve a PageTranslation from the given URL.
     *
     * @param $url
     * @return PageTranslationContract|null
     */
    protected function resolvePageLanguageVariantFromUrl($url)
    {
        return $this->router->resolve($url);
    }

    /**
     * Cache the rendered page contents, if caching is enabled and the current page does not contain non-cacheable blocks.
     *
     * @param string $renderedContent
     * @param $language
     * @return void
     */
    public function cacheRenderedPage(string $renderedContent, $language = null)
    {
        if (! phpb_config('cache.enabled') || ! PageRenderer::canBeCached() || isset($_GET['ignore_cache'])) {
            return;
        }
        $cache = phpb_instance('cache');

        // allow a forced cached page refresh, stored for the current URL but without the refresh parameter
        $url = phpb_current_relative_url();
        $url = str_replace('?refresh_cache&', '?', $url);
        $url = str_replace('?refresh_cache', '', $url);
        $url = str_replace('&refresh_cache', '', $url);
        if ($language && strpos($url, '/' . $language . '/') !== 0) {
            $cache->invalidate($url);
            $url = '/' . $language . $url;
        }

        if (! empty(PageRenderer::$skeletonCacheUrl)) {
            $url = PageRenderer::$skeletonCacheUrl;
        }
        $cache->storeForUrl($url, $renderedContent, phpb_static(PageRenderer::class)::getCacheLifetime());
    }

    /**
     * Handle authenticated requests, this method assumes you have checked that the user is currently logged in.
     *
     * @param string|null $route
     * @param string|null $action
     */
    public function handleAuthenticatedRequest($route = null, $action = null)
    {
        $route = $route ?? $_GET['route'] ?? null;
        $action = $action ?? $_GET['action'] ?? null;

        // handle website manager requests
        if (phpb_config('website_manager.use_website_manager') && phpb_in_module('website_manager')) {
            $this->websiteManager->handleRequest($route, $action);
            header("HTTP/1.1 404 Not Found");
            exit();
        }

        // handle page builder requests
        if (phpb_in_module('pagebuilder')) {
            phpb_set_in_editmode();
            $this->pageBuilder->handleRequest($route, $action);
            header("HTTP/1.1 404 Not Found");
            exit();
        }
    }

    /**
     * Handle uploaded file requests.
     */
    public function handleUploadedFileRequest()
    {
        // get the requested file by stripping the configured uploads_url prefix from the current request URI
        $file = substr(phpb_current_relative_url(), strlen(phpb_config('general.uploads_url')) + 1);
        // $file is in the format {file id}/{file name}.{file extension}, so get file id as the part before /
        $fileId = explode('/', $file)[0];
        if (empty($fileId)) {
            header("HTTP/1.1 404 Not Found");
            exit();
        }

        $uploadRepository = new UploadRepository;
        $uploadedFile = $uploadRepository->findWhere('public_id', $fileId);
        if (empty($uploadedFile)) {
            header("HTTP/1.1 404 Not Found");
            exit();
        }

        $uploadedFile = $uploadedFile[0];
        $serverFile = realpath(phpb_config('storage.uploads_folder') . '/' . $uploadedFile->server_file);
        // add backwards compatibility for files uploaded with PHPageBuilder <= v0.12.0, stored as /uploads/{id}.{extension}
        if (! $serverFile) $serverFile = realpath(phpb_config('storage.uploads_folder') . '/' . basename($uploadedFile->server_file));
        if (! $serverFile) {
            header("HTTP/1.1 404 Not Found");
            exit();
        }

        header('Content-Type: ' . $uploadedFile->mime_type);
        header('Content-Disposition: inline; filename="' . basename($uploadedFile->original_file) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Length: ' . filesize($serverFile));

        readfile($serverFile);
        exit();
    }

    /**
     * Handle page builder asset requests.
     */
    public function handlePageBuilderAssetRequest()
    {
        // get asset file path by stripping the configured assets_url prefix from the current request URI
        $asset = substr(phpb_current_relative_url(), strlen(phpb_config('general.assets_url')) + 1);
        $asset = explode('?', $asset)[0];

        $distPath = realpath(__DIR__ . '/../dist/');
        $requestedFile = realpath($distPath . '/' . $asset);
        if (! $requestedFile) {
            header("HTTP/1.1 404 Not Found");
            exit();
        }

        // prevent path traversal by ensuring the requested file is inside the dist folder
        if (strpos($requestedFile, $distPath) !== 0) {
            header("HTTP/1.1 404 Not Found");
            exit();
        }

        // only allow specific extensions
        $ext = pathinfo($requestedFile, PATHINFO_EXTENSION);
        if (! in_array($ext, ['js', 'css', 'jpg', 'png', 'svg'])) {
            header("HTTP/1.1 404 Not Found");
            exit();
        }

        $contentTypes = [
            'js' => 'application/javascript; charset=utf-8',
            'css' => 'text/css; charset=utf-8',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'svg' => 'image/svg+xml'
        ];
        header('Content-Type: ' . $contentTypes[$ext]);
        header('Content-Disposition: inline; filename="' . basename($requestedFile) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Length: ' . filesize($requestedFile));

        readfile($requestedFile);
        exit();
    }


    /**
     * Render the PageBuilder.
     *
     * @param PageContract $page
     */
    public function renderPageBuilder(PageContract $page)
    {
        phpb_set_in_editmode();
        $this->pageBuilder->renderPageBuilder($page);
    }
}
