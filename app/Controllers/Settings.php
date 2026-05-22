<?php

namespace App\Controllers;

use App\Models\AppSettingsModel;
use App\Models\KonfigurasiModel;
use App\Models\UserModel;

class Settings extends BaseController
{
    public function index()
    {
        $konfigModel = new KonfigurasiModel();

        $data['title']       = lang('App.menu_setting') . ' - AppsBeem';
        $data['page_title']  = lang('App.menu_setting');
        $data['app']         = (new AppSettingsModel())->getSettings();
        $data['wa']          = $konfigModel->getWaSettings();
        $data['wa_ready']    = $konfigModel->tableReady();
        $data['is_admin']    = UserModel::isAdminRole(session()->get('role'));

        return view('settings', $data);
    }

    /**
     * Update company + konfigurasi WA (admin only).
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

        $konfigModel = new KonfigurasiModel();

        if (! $konfigModel->tableReady()) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => lang('App.wa_config_table_missing'),
            ]);
        }

        $enabled = $this->request->getPost('wa_notify_enabled') ? 'true' : 'false';
        $days    = (int) $this->request->getPost('wa_notify_days');
        $groupId = trim((string) $this->request->getPost('wa_group_id'));
        $apiUrl  = trim((string) $this->request->getPost('api_url_group'));
        $report  = trim((string) $this->request->getPost('report_expired'));

        if ($days < 1 || $days > 365) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => lang('App.wa_notify_days_invalid'),
            ]);
        }

        if ($enabled === 'true' && $groupId === '') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => lang('App.wa_group_id_required'),
            ]);
        }

        if ($enabled === 'true' && $apiUrl === '') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => lang('App.wa_api_url_required'),
            ]);
        }

        if ($report === '') {
            $report = KonfigurasiModel::WA_DEFAULTS['report_expired'];
        }

        (new AppSettingsModel())->updateCompanyName($companyName);
        session()->set('company_name', $companyName);

        $konfigModel->saveWaSettings([
            'wa_notify_enabled' => $enabled,
            'wa_notify_days'    => (string) $days,
            'wa_group_id'       => $groupId,
            'api_url_group'     => $apiUrl,
            'report_expired'    => $report,
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => lang('App.settings_updated_success'),
        ]);
    }
}
