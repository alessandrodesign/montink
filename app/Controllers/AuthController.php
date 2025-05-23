<?php

namespace App\Controllers;

use App\Enums\UserRole;
use App\Models\PasswordResetModel;
use App\Services\Auth\AuthService;
use App\Services\Email\EmailService;
use Random\RandomException;

class AuthController extends BaseController
{
    protected AuthService $authService;
    protected EmailService $emailService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->emailService = new EmailService();
    }

    public function login()
    {
        if ($this->isLoggedIn()) {
            return redirect()->to('/');
        }

        if ($this->request->is('post')) {
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            $user = $this->authService->login($email, $password);

            if ($user) {
                $this->setMessage('Login successful');
                return redirect()->to('/');
            } else {
                $this->setMessage('Invalid email or password', 'error');
                return redirect()->back()->withInput();
            }
        }

        $data = [
            'title' => 'Login',
        ];

        return view('auth/login', $data);
    }

    public function register()
    {
        if ($this->isLoggedIn()) {
            return redirect()->to('/');
        }

        if ($this->request->is('post')) {
            $userData = [
                'name' => $this->request->getPost('name'),
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'),
                'role' => UserRole::USER,
            ];

            $user = $this->authService->register($userData);

            if ($user) {
                // Send welcome email
                // $this->emailService->sendWelcomeEmail($user);

                $this->setMessage('Registration successful. Please login.');
                return redirect()->to('/auth/login');
            } else {
                $this->setValidationErrors($this->authService->getErrors());
                return redirect()->back()->withInput();
            }
        }

        $data = [
            'title' => 'Register',
        ];

        return view('auth/register', $data);
    }

    public function logout()
    {
        $this->authService->logout();
        $this->setMessage('Logout successful');
        return redirect()->to('/');
    }

    public function profile()
    {
        $this->requireLogin();

        $user = $this->authService->getCurrentUser();

        if ($this->request->is('post')) {
            $userData = [
                'id' => $user->id,
                'name' => $this->request->getPost('name'),
                'email' => $this->request->getPost('email'),
            ];

            // Only update password if provided
            $password = $this->request->getPost('password');
            if (!empty($password)) {
                $userData['password'] = $password;
            }

            $user = $this->authService->updateProfile($userData);

            if ($user) {
                $this->setMessage('Profile updated successfully');
                return redirect()->to('/auth/profile');
            } else {
                $this->setValidationErrors($this->authService->getErrors());
                return redirect()->back()->withInput();
            }
        }

        $data = [
            'title' => 'My Profile',
            'user' => $user,
        ];

        return view('auth/profile', $data);
    }

    /**
     * @throws \ReflectionException
     * @throws RandomException
     */
    public function forgotPassword()
    {
        if ($this->isLoggedIn()) {
            return redirect()->to('/');
        }

        if ($this->request->is('post')) {
            $email = $this->request->getPost('email');

            $user = $this->authService->userModel->findByEmail($email);

            if (!$user) {
                $this->setMessage('Email not found', 'error');
                return redirect()->back()->withInput();
            }

            $token = bin2hex(random_bytes(32));

            $passwordResetModel = new PasswordResetModel();

            $passwordResetModel->where('user_id', $user->id)->delete();

            $passwordResetModel->save([
                'user_id' => $user->id,
                'token' => $token,
                'expires_at' => date('Y-m-d H:i:s', strtotime('+1 hour')),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $resetLink = site_url("auth/reset-password/{$token}");

            $this->emailService->sendPasswordReset($user, $resetLink);

            $this->setMessage('Password reset instructions sent to your email');


            return redirect()->to('/auth/login');
        }

        $data = [
            'title' => 'Forgot Password',
        ];

        return view('auth/forgot_password', $data);
    }

    public function resetPassword(string $token = null)
    {
        if (!$token) {
            return redirect()->to('/auth/forgot-password');
        }

        $passwordResetModel = new PasswordResetModel();
        $reset = $passwordResetModel->where('token', $token)
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->first();

        if (!$reset) {
            $this->setMessage('Invalid or expired token.', 'error');
            return redirect()->to('/auth/forgot-password');
        }

        if ($this->request->is('post')) {
            $password = $this->request->getPost('password');
            $passwordConfirm = $this->request->getPost('password_confirm');

            if ($password !== $passwordConfirm) {
                $this->setMessage('Passwords do not match.', 'error');
                return redirect()->back()->withInput();
            }

            // Atualizar senha do usuário
            $user = $this->authService->userModel->find($reset['user_id']);
            $user->password = $password;
            $this->authService->userModel->save($user);

            // Remover token usado
            $passwordResetModel->delete($reset['id']);

            $this->setMessage('Password reset successfully. Please log in.');
            return redirect()->to('/auth/login');
        }

        return view('auth/reset_password', ['token' => $token, 'title' => 'Reset Password']);
    }
}