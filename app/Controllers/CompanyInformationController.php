<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CompanyInformationModel;

class CompanyInformationController extends BaseController
{

    public function index()
    {
        $companyModel = new CompanyInformationModel();
        $company = $companyModel->first();

        return view('admin/company_information', ['company' => $company]);
    }

    // Save company info (create or update)
    public function save()
    {
        $companyModel = new CompanyInformationModel();
        $data = [
            'company_name' => $this->request->getPost('company_name'),
            'address'      => $this->request->getPost('address'),
            'city'         => $this->request->getPost('city'),
            'state'        => $this->request->getPost('state'),
            'country'      => $this->request->getPost('country'),
            'pincode'      => $this->request->getPost('zipcode'),
            'phone'        => $this->request->getPost('phone'),
            'email'        => $this->request->getPost('email'),
            'website'      => $this->request->getPost('website'),
        ];

        $company = $companyModel->first();
        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $newName = $logo->getRandomName();
            $logo->move(FCPATH  . 'uploads/company', $newName);
            $data['logo'] = $newName;

            // Delete old image if updating
            if ($company['id']) {
                $oldImage = $companyModel->find($company['id'])['logo'];
                if ($oldImage && file_exists(FCPATH  . 'uploads/company/' . $oldImage)) {
                    unlink(FCPATH  . 'uploads/company/' . $oldImage);
                }
            }
        }

        if ($company) {
            // Update existing record
            $companyModel->update($company['id'], $data);
        } else {
            // Insert new record
            $companyModel->insert($data);
        }

        return redirect()->to('settings/company-info')->with('success', 'Company information saved successfully.');
    }
}
