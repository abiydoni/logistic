<?php

namespace App\Filters;

use App\Models\AppSettingsModel;
use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }

        $userId = $session->get('user_id');
        if ($userId) {
            $user = (new UserModel())->find($userId);
            if ($user) {
                if (! UserModel::isActive($user['is_active'] ?? 1)) {
                    $session->destroy();

                    return redirect()->to(base_url('auth/login'))->with('error', lang('App.user_inactive'));
                }

                $role = $user['role'] ?? '';
                if ($role === '' && ($user['username'] ?? '') === 'admin') {
                    $role = 'admin';
                }
                $session->set([
                    'role'  => UserModel::normalizeRole($role),
                    'name'  => $user['full_name'] ?? $session->get('name'),
                    'lang'  => normalize_locale($user['language'] ?? $session->get('lang')),
                    'theme' => normalize_theme($user['theme'] ?? $session->get('theme')),
                ]);
            }
        }

        $session->set('company_name', (new AppSettingsModel())->getCompanyName());
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
