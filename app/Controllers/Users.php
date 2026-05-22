<?php

namespace App\Controllers;

use App\Models\UserModel;

class Users extends BaseController
{
    /**
     * Display users list or save a user.
     */
    public function index()
    {
        // Security check: Only Admin can access
        if (! UserModel::isAdminRole(session()->get('role'))) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Akses ditolak! Halaman ini hanya untuk Administrator.');
        }

        $userModel = new UserModel();

        if ($this->request->getMethod() === 'POST' || $this->request->getMethod() === 'post') {
            $id = $this->request->getPost('id');
            $username = trim($this->request->getPost('username'));
            $fullName = trim($this->request->getPost('full_name'));
            $role = UserModel::normalizeRole($this->request->getPost('role'));
            $password = $this->request->getPost('password');

            // Simple validation
            if (empty($username) || empty($fullName) || empty($role)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Username, Nama Lengkap, dan Peran tidak boleh kosong!'
                ]);
            }

            // Check duplicate username
            $existing = $userModel->where('username', $username)->first();
            if ($existing) {
                if (!$id || $existing['id'] != $id) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Username "' . esc($username) . '" sudah digunakan oleh pengguna lain!'
                    ]);
                }
            }

            $isActive = (int) $this->request->getPost('is_active') === 1 ? 1 : 0;

            if ($id && (int) $id === (int) session()->get('user_id') && $isActive === 0) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => lang('App.cannot_deactivate_self'),
                ]);
            }

            $data = [
                'username'  => $username,
                'full_name' => $fullName,
                'role'      => $role,
                'is_active' => $isActive,
            ];

            // Only update password if it's provided
            if (!empty($password)) {
                $data['password'] = password_hash($password, PASSWORD_BCRYPT);
            } elseif (!$id) {
                // If it is a new user, password is required
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Password wajib diisi untuk pengguna baru!'
                ]);
            }

            if ($id) {
                $userModel->update($id, $data);
                
                // If the updated user is the currently logged in user, update session as well
                if ($id == session()->get('user_id')) {
                    session()->set([
                        'name' => $fullName,
                        'role' => $role,
                    ]);
                }
            } else {
                // Set default preference for new users
                $data['language']  = 'id';
                $data['theme']     = 'light';
                $data['is_active'] = $data['is_active'] ?? 1;
                $userModel->insert($data);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => lang('App.success_save_user')
            ]);
        }

        // GET request - listing users
        $data['users'] = $userModel->findAll();
        $data['title'] = 'Manage Users - AppsBeem';
        $data['page_title'] = lang('App.users_list');

        return view('users', $data);
    }

    /**
     * Delete a user via AJAX POST request.
     */
    public function delete()
    {
        // Security check: Only Admin can delete
        if (! UserModel::isAdminRole(session()->get('role'))) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Akses ditolak!'
            ]);
        }

        $id = $this->request->getPost('id');

        // Prevent self-deletion
        if ($id == session()->get('user_id')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif!'
            ]);
        }

        $userModel = new UserModel();
        
        // Find if user exists
        $user = $userModel->find($id);
        if (!$user) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Pengguna tidak ditemukan!'
            ]);
        }

        // Perform deletion
        $userModel->delete($id);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Pengguna berhasil dihapus!'
        ]);
    }

    /**
     * Toggle user active status (AJAX).
     */
    public function toggleStatus()
    {
        if (! UserModel::isAdminRole(session()->get('role'))) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('App.failed')]);
        }

        $id        = (int) $this->request->getPost('id');
        $userModel = new UserModel();
        $user      = $userModel->find($id);

        if (! $user) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('App.failed')]);
        }

        $newStatus = UserModel::isActive($user['is_active'] ?? 1) ? 0 : 1;

        if ($id === (int) session()->get('user_id') && $newStatus === 0) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => lang('App.cannot_deactivate_self'),
            ]);
        }

        $userModel->update($id, ['is_active' => $newStatus]);

        return $this->response->setJSON([
            'status'    => 'success',
            'message'   => lang('App.status_updated'),
            'is_active' => $newStatus,
        ]);
    }
}
