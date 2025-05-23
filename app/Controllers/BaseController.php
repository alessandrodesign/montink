<?php

namespace App\Controllers;

use App\Enums\UserRole;
use App\Libraries\Auth;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['form', 'url', 'text'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        $this->session = service('session');
    }

    /**
     * Set flash message
     */
    protected function setMessage(string $message, string $type = 'success')
    {
        $this->session->setFlashdata('message', [
            'type' => $type,
            'text' => $message,
        ]);
    }

    /**
     * Set validation errors as flash message
     */
    protected function setValidationErrors(array $errors)
    {
        $this->session->setFlashdata('errors', $errors);
    }

    /**
     * Check if user is logged in
     */
    protected function isLoggedIn(): bool
    {
        return Auth::getInstance()->isLogged();
    }

    /**
     * Check if user is admin
     */
    protected function isAdmin(): bool
    {
        return Auth::getInstance()->isAdmin();
    }

    /**
     * Redirect if not logged in
     */
    protected function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            $this->setMessage('Please login to continue', 'error');
            return redirect()->to('/auth/login');
        }
    }

    /**
     * Redirect if not admin
     */
    protected function requireAdmin()
    {
        $this->requireLogin();

        if (!$this->isAdmin()) {
            $this->setMessage('Access denied', 'error');
            return redirect()->to('/');
        }
    }
}
