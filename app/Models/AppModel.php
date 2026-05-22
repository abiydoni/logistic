<?php

namespace App\Models;

use CodeIgniter\Model;

class AppModel extends Model
{
    protected $beforeInsert = ['injectCreatedBy'];
    protected $beforeUpdate = ['injectUpdatedBy'];

    protected function injectCreatedBy(array $data)
    {
        $userId = session()->get('user_id');
        if ($userId) {
            if (in_array('created_by', $this->allowedFields)) {
                $data['data']['created_by'] = $userId;
            }
            if (in_array('updated_by', $this->allowedFields)) {
                $data['data']['updated_by'] = $userId;
            }
        }
        return $data;
    }

    protected function injectUpdatedBy(array $data)
    {
        $userId = session()->get('user_id');
        if ($userId) {
            if (in_array('updated_by', $this->allowedFields)) {
                $data['data']['updated_by'] = $userId;
            }
        }
        return $data;
    }

    /**
     * Custom soft delete with deleted_by support.
     */
    public function deleteWithAudit($id)
    {
        $userId = session()->get('user_id');
        if ($userId && in_array('deleted_by', $this->allowedFields)) {
            $this->update($id, ['deleted_by' => $userId]);
        }
        return $this->delete($id);
    }
}
