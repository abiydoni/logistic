<?php

namespace App\Controllers;

use App\Models\UserModel;

class Profile extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $user = $userModel->find(session()->get('user_id'));

        if (! $user) {
            return redirect()->to(base_url('auth/login'));
        }

        $data['title']      = lang('App.profile') . ' - AppsBeem';
        $data['page_title'] = lang('App.profile');
        $data['user']       = $user;

        return view('profile', $data);
    }

    public function update()
    {
        $userId    = session()->get('user_id');
        $userModel = new UserModel();
        $user      = $userModel->find($userId);

        if (! $user) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('App.failed')]);
        }

        $fullName        = trim((string) $this->request->getPost('full_name'));
        $currentPassword = (string) $this->request->getPost('current_password');
        $newPassword     = (string) $this->request->getPost('new_password');
        $confirmPassword = (string) $this->request->getPost('confirm_password');
        $lang            = $this->request->getPost('language');
        $theme           = $this->request->getPost('theme');

        if ($fullName === '') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => lang('App.full_name_required'),
            ]);
        }

        $lang  = normalize_locale($lang);
        $theme = normalize_theme($theme);

        $data = [
            'full_name' => $fullName,
            'language'  => $lang,
            'theme'     => $theme,
        ];

        if ($newPassword !== '' || $confirmPassword !== '' || $currentPassword !== '') {
            if ($newPassword === '' || $confirmPassword === '') {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => lang('App.password_fill_all'),
                ]);
            }

            if ($newPassword !== $confirmPassword) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => lang('App.password_mismatch'),
                ]);
            }

            if (strlen($newPassword) < 4) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => lang('App.password_too_short'),
                ]);
            }

            if (! $userModel->verifyPassword($currentPassword, $user['password'])) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => lang('App.current_password_wrong'),
                ]);
            }

            $data['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        $userModel->update($userId, $data);

        session()->set([
            'name'  => $fullName,
            'lang'  => $lang,
            'theme' => $theme,
        ]);

        service('request')->setLocale($lang);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => lang('App.profile_updated_success'),
            'theme'   => $theme,
            'lang'    => $lang,
        ]);
    }

    /**
     * Quick theme toggle from header (persists to DB + session).
     */
    public function updateTheme()
    {
        $userId = session()->get('user_id');
        if (! $userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('App.failed')]);
        }

        $theme = normalize_theme($this->request->getPost('theme'));

        (new UserModel())->update($userId, ['theme' => $theme]);
        session()->set('theme', $theme);

        return $this->response->setJSON([
            'status'  => 'success',
            'theme'   => $theme,
            'message' => lang('App.change_theme_success'),
        ]);
    }

    /**
     * Quick language switch from header (persists to DB + session).
     */
    public function updateLocale()
    {
        $userId = session()->get('user_id');
        if (! $userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('App.failed')]);
        }

        $locale = normalize_locale($this->request->getPost('locale'));

        (new UserModel())->update($userId, ['language' => $locale]);
        session()->set('lang', $locale);
        service('request')->setLocale($locale);

        return $this->response->setJSON([
            'status'  => 'success',
            'locale'  => $locale,
            'message' => lang('App.change_language_success'),
        ]);
    }
}
