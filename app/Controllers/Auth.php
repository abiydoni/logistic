<?php

namespace App\Controllers;

use App\Models\AppSettingsModel;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        $session = session();
        
        // If already logged in, redirect to dashboard
        if ($session->get('logged_in')) {
            return redirect()->to(base_url('dashboard'));
        }

        $userModel = new UserModel();

        // Seed default admin if table is empty
        try {
            if ($userModel->countAllResults() === 0) {
                $userModel->insert([
                    'username'  => 'admin',
                    'password'  => password_hash('admin', PASSWORD_BCRYPT),
                    'full_name' => 'Administrator Beem',
                    'role'      => 'admin',
                    'is_active' => 1,
                ]);
            }
        } catch (\Exception $e) {
            // Quietly pass or log if table hasn't been created yet
        }

        if ($this->request->getMethod() === 'POST' || $this->request->getMethod() === 'post') {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            $user = $userModel->where('username', $username)->first();

            if ($user && $userModel->verifyPassword($password, $user['password'])) {
                if (! UserModel::isActive($user['is_active'] ?? 1)) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => lang('App.user_inactive'),
                    ]);
                }

                $companyName = (new AppSettingsModel())->getCompanyName();

                $session->set([
                    'user_id'       => $user['id'],
                    'username'      => $user['username'],
                    'name'          => $user['full_name'],
                    'company_name'  => $companyName,
                    'role'          => UserModel::normalizeRole($user['role'] ?? 'staff'),
                    'lang'          => normalize_locale($user['language'] ?? 'id'),
                    'theme'         => normalize_theme($user['theme'] ?? 'light'),
                    'logged_in'     => true,
                ]);

                $theme  = normalize_theme($user['theme'] ?? 'light');
                $locale = normalize_locale($user['language'] ?? 'id');

                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => lang('App.success'),
                    'theme'   => $theme,
                    'lang'    => $locale,
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => lang('App.login_failed')
                ]);
            }
        }

        // Return view
        return view('auth/login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('auth/login'));
    }
}
