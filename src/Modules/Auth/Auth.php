<?php

namespace PHPageBuilder\Modules\Auth;

use PHPageBuilder\Contracts\AuthContract;

class Auth implements AuthContract
{
    private $mysqli;
    private $sessionKey = 'phpb_logged_in';
    
    public function __construct()
    {
        $this->connectDatabase();
    }

    /**
     * Connect to the MySQL database.
     */
    private function connectDatabase()
    {
        $this->mysqli = new \mysqli('localhost', 'aiwebgen_dbadmin', '4Tg0:6"@][q}', 'aiwebgenerator');

        if ($this->mysqli->connect_error) {
            log_message('error', 'Database connection error: ' . $this->mysqli->connect_error);
            $this->redirectWithError('Er is een fout opgetreden tijdens de verbinding met de database.');
        }
    }

    /**
     * Handle incoming request based on the action parameter.
     *
     * @param string $action
     */
    public function handleRequest($action)
    {
        if ($action === 'login') {
            $this->handleLogin();
        } elseif ($action === 'logout') {
            $this->handleLogout();
        }
    }

    /**
     * Handle the login process.
     */
    private function handleLogin()
    {
        $token = isset($_GET['token']) ? trim($_GET['token']) : null;
        $userId = isset($_GET['userid']) ? (int) $_GET['userid'] : null;
        $websiteId = isset($_GET['website_id']) ? (int) $_GET['website_id'] : null;

        if (empty($token) || empty($userId) || empty($websiteId)) {
            $this->redirectWithWarning('De SSO authenticatie was niet helemaal goede structuur');
            return;
        }

        if ($this->isTokenValid($token, $userId, $websiteId)) {
            $_SESSION[$this->sessionKey] = true;
            $this->clearToken($token, $userId, $websiteId);
            phpb_redirect(phpb_url('website_manager'));
        } else {
            $this->redirectWithWarning('De SSO authenticatie was niet juist');
        }
    }

    /**
     * Handle user logout.
     */
    private function handleLogout()
    {
        unset($_SESSION[$this->sessionKey]);
        phpb_redirect(phpb_url('website_manager'));
    }

    /**
     * Check if the token is valid.
     *
     * @param string $token
     * @param int $userId
     * @param int $websiteId
     * @return bool
     */
    private function isTokenValid($token, $userId, $websiteId)
    {
        $stmt = $this->mysqli->prepare("SELECT id, expires_at FROM websites_tokens WHERE sso_token = ? AND user_id = ? AND website_id = ?");
        
        if ($stmt === false) {
            log_message('error', 'Prepare statement failed: ' . $this->mysqli->error);
            $this->redirectWithError('Er is een fout opgetreden tijdens de query.');
            return false;
        }

        $stmt->bind_param('sii', $token, $userId, $websiteId);
        $stmt->execute();
        $stmt->bind_result($id, $expiresAt); // extra security measures? we do not really need to because it simply deletes SSO on authentication succesful
        $stmt->fetch();

        $isValid = $id && strtotime($expiresAt) > time();

        $stmt->close();

        return $isValid;
    }

    /**
     * Clear the token from the database.
     *
     * @param string $token
     * @param int $userId
     * @param int $websiteId
     */
    private function clearToken($token, $userId, $websiteId)
    {
        $stmt = $this->mysqli->prepare("UPDATE websites_tokens SET sso_token = NULL WHERE sso_token = ? AND user_id = ? AND website_id = ?");
        
        if ($stmt === false) {
            log_message('error', 'Prepare statement failed: ' . $this->mysqli->error);
            $this->redirectWithError('Er is een fout opgetreden tijdens de update.');
            return;
        }

        $stmt->bind_param('sii', $token, $userId, $websiteId);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Redirect with an error message.
     *
     * @param string $message
     */
    private function redirectWithError($message)
    {
        phpb_redirect(phpb_url('website_manager'), [
            'message-type' => 'error',
            'message' => $message
        ]);
        $this->mysqli->close();
    }

    /**
     * Redirect with a warning message.
     *
     * @param string $message
     */
    private function redirectWithWarning($message)
    {
        phpb_redirect(phpb_url('website_manager'), [
            'message-type' => 'warning',
            'message' => $message
        ]);
        $this->mysqli->close();
    }

    /**
     * Check if the user is authenticated.
     *
     * @return bool
     */
    public function isAuthenticated()
    {
        return isset($_SESSION[$this->sessionKey]);
    }

    /**
     * If the user is not authenticated, show the login form.
     */
    public function requireAuth()
    {
        if (!$this->isAuthenticated()) {
            $this->renderLoginForm();
            exit();
        }
    }

    /**
     * Render the login form.
     */
    public function renderLoginForm()
    {
        $viewFile = 'login-form';
        require __DIR__ . '/resources/views/layout.php';
    }
}