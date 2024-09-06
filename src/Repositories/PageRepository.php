<?php

namespace PHPageBuilder\Repositories;

use PHPageBuilder\Contracts\PageContract;
use PHPageBuilder\Contracts\PageRepositoryContract;
use Exception;

class PageRepository extends BaseRepository implements PageRepositoryContract
{
    protected $table;
    protected $class;
    protected $rootDb = null;
    protected $websiteDb = null;

    public function __construct()
    {
        $this->table = empty(phpb_config('page.table')) ? 'pages' : phpb_config('page.table');
        parent::__construct();
        $this->class = phpb_instance('page');
    }

    /**
     * Create a new page after verifying the user's subscription plan limit.
     *
     * @param array $data
     * @return bool|object|null
     * @throws Exception
     */
    public function create(array $data)
    {
        // Fetch the domain name dynamically
        $domainName = $this->getDomainNameFromPath();

        // Fetch the subscription plan and page limit
        $subscriptionPlan = $this->getSubscriptionPlan($domainName);
        $pageLimit = $this->getMaxPagesByPlan($subscriptionPlan);

        // Check the current number of pages created
        $currentPageCount = $this->getCurrentPageCount();
        if ($currentPageCount >= $pageLimit) {
            throw new Exception("Page creation limit reached for the $subscriptionPlan plan.");
        }

        foreach (['name', 'layout'] as $field) {
            if (!isset($data[$field]) || !is_string($data[$field])) {
                return false;
            }
        }

        // Proceed with page creation
        $page = parent::create([
            'name' => $data['name'],
            'layout' => $data['layout'],
        ]);

        if (!($page instanceof PageContract)) {
            throw new Exception("Page not of type PageContract");
        }

        return $this->replaceTranslations($page, $data);
    }

    /**
     * Fetch the current domain name from the directory path dynamically.
     *
     * @return string
     */
    protected function getDomainNameFromPath()
    {
        $baseDir = __DIR__;
        $pathParts = explode('/', $baseDir);

        // Assuming the domain name is the 3rd part in /var/www/domainName/
        return $pathParts[3];
    }

    /**
     * Get the user's subscription plan from the root database.
     *
     * @param string $domainName
     * @return string
     */
    protected function getSubscriptionPlan($domainName)
    {
        if ($this->rootDb === null) {
            $this->connectRootDatabase();
        }

        $query = "SELECT subscription FROM websites WHERE domain_name = ?";
        $stmt = $this->rootDb->prepare($query);
        $stmt->bind_param('s', $domainName);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['subscription'] ?? 'startup';  // Default to 'startup' if no subscription found
    }

    /**
     * Get the maximum number of pages allowed for the subscription plan.
     *
     * @param string $subscriptionPlan
     * @return int
     */
    protected function getMaxPagesByPlan($subscriptionPlan)
    {
        if ($this->rootDb === null) {
            $this->connectRootDatabase();
        }

        $query = "SELECT max_pages FROM website_restrictions WHERE plan = ?";
        $stmt = $this->rootDb->prepare($query);
        $stmt->bind_param('s', $subscriptionPlan);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['max_pages'] ?? 1;  // Default to 1 page if plan not found
    }

    /**
     * Connect to the root database dynamically.
     */
    protected function connectRootDatabase()
    {
        $this->rootDb = new \mysqli('localhost', 'aiwebgen_dbadmin', '4Tg0:6"@][q}', 'aiwebgenerator');

        if ($this->rootDb->connect_error) {
            die('Root database connection error: ' . $this->rootDb->connect_error);
        }
    }

    /**
     * Get the current number of pages for the website.
     *
     * @return int
     */
    protected function getCurrentPageCount()
    {
        if ($this->websiteDb === null) {
            $this->connectWebsiteDatabase();
        }

        $query = "SELECT COUNT(*) as total_pages FROM pages";
        $result = $this->websiteDb->query($query);
        $row = $result->fetch_assoc();

        return $row['total_pages'] ?? 0;
    }

    /**
     * Connect to the website's database using credentials from the configuration.
     */
    protected function connectWebsiteDatabase()
    {
        $config = $this->loadConfig();
        $dbConfig = $config['storage']['database'];

        $this->websiteDb = new \mysqli($dbConfig['host'], $dbConfig['username'], $dbConfig['password'], $dbConfig['database']);

        if ($this->websiteDb->connect_error) {
            die('Website database connection error: ' . $this->websiteDb->connect_error);
        }
    }

    /**
     * Load the PageBuilder config dynamically based on the domain.
     *
     * @return array
     */
    protected function loadConfig()
    {
        $domainName = $this->getDomainNameFromPath();
        $configPath = "/var/www/{$domainName}/public/phpagebuilder/config/config.php";

        if (file_exists($configPath)) {
            return require $configPath;
        } else {
            die("Configuration file not found at: $configPath");
        }
    }

    /**
     * Update the given page with the given updated data.
     *
     * @param $page
     * @param array $data
     * @return bool|object|null
     */
    public function update($page, array $data)
    {
        foreach (['name', 'layout'] as $field) {
            if (!isset($data[$field]) || !is_string($data[$field])) {
                return false;
            }
        }

        $this->replaceTranslations($page, $data);

        $updateResult = parent::update($page, [
            'name' => $data['name'],
            'layout' => $data['layout'],
        ]);
        $page->invalidateCache();
        return $updateResult;
    }

    /**
     * Replace the translations of the given page by the given data.
     *
     * @param PageContract $page
     * @param array $data
     * @return bool
     */
    protected function replaceTranslations(PageContract $page, array $data)
    {
        $activeLanguages = phpb_active_languages();
        foreach (['title', 'meta_title', 'meta_description', 'route'] as $field) {
            foreach ($activeLanguages as $languageCode => $languageTranslation) {
                if (!isset($data[$field][$languageCode])) {
                    return false;
                }
            }
        }

        $pageTranslationRepository = new PageTranslationRepository;
        $pageTranslationRepository->destroyWhere(phpb_config('page.translation.foreign_key'), $page->getId());
        foreach ($activeLanguages as $languageCode => $languageTranslation) {
            $pageTranslationRepository->create([
                phpb_config('page.translation.foreign_key') => $page->getId(),
                'locale' => $languageCode,
                'title' => $data['title'][$languageCode],
                'meta_title' => $data['meta_title'][$languageCode],
                'meta_description' => $data['meta_description'][$languageCode],
                'route' => $data['route'][$languageCode],
            ]);
        }

        return true;
    }

    /**
     * Update the given page data.
     *
     * @param $page
     * @param array $data
     * @return bool|object|null
     */
    public function updatePageData($page, array $data)
    {
        $updateResult = parent::update($page, [
            'data' => json_encode($data),
        ]);
        $page->invalidateCache();
        return $updateResult;
    }

    /**
     * Remove the given page from the database.
     *
     * @param $id
     * @return bool
     */
    public function destroy($id)
    {
        $this->findWithId($id)->invalidateCache();
        return parent::destroy($id);
    }
}