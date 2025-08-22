<?php

namespace App\Controllers\API;

use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;

class ManagerController extends BaseController
{
    use ResponseTrait;

    public function getReportingManagersByCompany()
    {
        $companyId = $this->request->getGet('company_id');
        $db = \Config\Database::connect();

        // Step 1: Get distinct manager IDs who manage employees of this company
        $builder = $db->table('reporting_managers rm');
        $builder->select('DISTINCT(rm.manager_id)');
        $builder->join('employees e', 'rm.employee_id = e.employee_id');
        $builder->where('e.company_id', $companyId);
        $managerResults = $builder->get()->getResultArray();

        if (empty($managerResults)) {
            return $this->respond([
                'status' => false,
                'message' => 'No reporting managers found for this company.',
                'data' => [],
            ]);
        }

        $managerIds = array_column($managerResults, 'manager_id');

        // Step 2: Get full manager details + company name and address
        $builder = $db->table('employees e');
        $builder->select('e.*, c.company_name, c.address as company_address');
        $builder->join('companies c', 'e.company_id = c.company_id', 'left');
        $builder->where('e.company_id', $companyId);
        $builder->whereIn('e.employee_id', $managerIds);
        $managers = $builder->get()->getResultArray();

        return $this->respond([
            'status' => true,
            'message' => 'Reporting managers retrieved successfully.',
            'data' => $managers,
        ]);
    }
}
