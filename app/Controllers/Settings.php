<?php

namespace App\Controllers;

use App\Models\AppSettingsModel;
use App\Models\UserModel;

class Settings extends BaseController
{
    public function index()
    {
        $data['title']      = lang('App.menu_setting') . ' - AppsBeem';
        $data['page_title'] = lang('App.menu_setting');
        $data['app']        = (new AppSettingsModel())->getSettings();
        $data['is_admin']   = UserModel::isAdminRole(session()->get('role'));

        return view('settings', $data);
    }

    /**
     * Update company app settings (admin only).
     */
    public function update()
    {
        if (! UserModel::isAdminRole(session()->get('role'))) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => lang('App.access_denied'),
            ]);
        }

        $companyName = trim((string) $this->request->getPost('company_name'));

        if ($companyName === '') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => lang('App.company_name_required'),
            ]);
        }

        (new AppSettingsModel())->updateCompanyName($companyName);
        session()->set('company_name', $companyName);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => lang('App.settings_updated_success'),
        ]);
    }
}
