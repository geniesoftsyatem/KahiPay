<?php

namespace App\Controllers\API;

use App\Models\RechargeModel;
use App\Models\ComplaintModel;
use CodeIgniter\API\ResponseTrait;
use App\Controllers\BaseController;
use App\Models\RechargeStatusLogModel;

class RechargeController extends BaseController
{
    use ResponseTrait;

    public function createRecharge()
    {
        $apiToken = '265bfdfd-5e93-4186-9e26-6fced4cd0c2e';

        // Get input from POST request
        $mobileNo = $this->request->getPost('mobile_no');
        $amount = $this->request->getPost('amount');
        $operatorId = $this->request->getPost('operator_id');
        $requestTxnId = strtoupper(substr(bin2hex(random_bytes(5)), 0, 10));

        // Construct URL
        $url = "https://spmfintech.com/Api/Service/Recharge2?ApiToken={$apiToken}&MobileNo={$mobileNo}&Amount={$amount}&OpId={$operatorId}&RefTxnId={$requestTxnId}";

        // Call API using CURL
        $client = \Config\Services::curlrequest();
        $response = $client->get($url);

        $result = json_decode($response->getBody(), true);
        // Save into DB
        $rechargeModel = new RechargeModel();
        $rechargeModel->insert([
            'request_txn_id' => $requestTxnId,
            'mobile_no' => $mobileNo,
            'amount' => $amount,
            'operator_id' => $operatorId,
            'status' => $result['STATUS'] ?? 0,
            'message' => $result['MESSAGE'] ?? '',
            'error_code' => $result['ERRORCODE'] ?? '',
            'operator_txn_id' => $result['OPTXNID'] ?? '',
            'txn_no' => $result['TXNNO'] ?? '',
            'http_code' => $result['HTTPCODE'] ?? 0,
        ]);

        // Return API response
        return $this->respond($result);
    }

    public function checkRechargeStatus()
    {
        $apiToken = '265bfdfd-5e93-4186-9e26-6fced4cd0c2e';

        // Get request_txn_id from POST or GET
        $requestTxnId = $this->request->getVar('request_txn_id');

        if (!$requestTxnId) {
            return $this->failNotFound('Missing request_txn_id');
        }

        $url = "https://spmfintech.com/Api/service/statuscheck?ApiToken={$apiToken}&RefTxnId={$requestTxnId}";

        // Call API
        $client = \Config\Services::curlrequest();
        try {
            $response = $client->get($url);
            $body = $response->getBody();
            $result = json_decode($body, true);

            // Insert into status logs
            $logModel = new RechargeStatusLogModel();
            $logModel->insert([
                'request_txn_id'   => $requestTxnId,
                'customer_no'      => $result['CUSTOMERNO'] ?? null,
                'operator'         => $result['OPERATOR'] ?? null,
                'amount'           => $result['AMOUNT'] ?? null,
                'status'           => $result['STATUS'] ?? null,
                'message'          => $result['MESSAGE'] ?? null,
                'circle'           => $result['CIRCLE'] ?? null,
                'error_code'       => $result['ERRORCODE'] ?? null,
                'txn_no'           => $result['TXNNO'] ?? null,
                'operator_txn_id'  => $result['OPTXNID'] ?? null,
                'http_code'        => $result['HTTPCODE'] ?? null,
            ]);

            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->failServerError('API request failed: ' . $e->getMessage());
        }
    }

    public function checkBalance()
    {
        $apiToken = '265bfdfd-5e93-4186-9e26-6fced4cd0c2e';

        $url = "https://spmfintech.com/Api/Service/Balance?at={$apiToken}";

        // Call API using CURL
        $client = \Config\Services::curlrequest();

        try {
            $response = $client->get($url);
            $body = $response->getBody();
            $result = json_decode($body, true);

            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->failServerError('Balance check failed: ' . $e->getMessage());
        }
    }

    public function raiseComplaint()
    {
        $apiToken = '265bfdfd-5e93-4186-9e26-6fced4cd0c2e';

        // Get required parameters from query or post
        $requestTxnId = $this->request->getVar('remark');
        $remark = $this->request->getVar('request_txn_id');

        if (!$requestTxnId || !$remark) {
            return $this->failNotFound('Missing required parameters: rq (request_txn_id) or rm (remark)');
        }

        // Construct the URL
        $url = "https://spmfintech.com/Api/Service/complaint?at={$apiToken}&rq={$requestTxnId}&rm=" . urlencode($remark);

        // Call the API
        $client = \Config\Services::curlrequest();

        try {
            $response = $client->get($url);
            $result = json_decode($response->getBody(), true);

            return $this->respond($result);
        } catch (\Exception $e) {
            return $this->failServerError('Complaint request failed: ' . $e->getMessage());
        }
    }

    public function rechargeCallback()
    {
        $status      = $this->request->getGet('STATUS');
        $operatorTxn = $this->request->getGet('OPTXNID');
        $requestTxn  = $this->request->getGet('YOURREQID');

        if (!$status || !$operatorTxn || !$requestTxn) {
            return $this->failNotFound('Missing one or more required parameters: STATUS, OPTXNID, YOURREQID');
        }

        // Load the model
        $rechargeModel = new RechargeModel();

        // Find the record
        $recharge = $rechargeModel->where('request_txn_id', $requestTxn)->first();

        if (!$recharge) {
            return $this->failNotFound("Recharge request not found for request_txn_id: $requestTxn");
        }

        // Update the recharge status and operator txn ID
        $rechargeModel->update($recharge['id'], [
            'status'          => $status,
            'operator_txn_id' => $operatorTxn,
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);

        return $this->respond([
            'message'         => 'Recharge status updated successfully via callback.',
            'request_txn_id'  => $requestTxn,
            'status'          => $status,
            'operator_txn_id' => $operatorTxn,
        ]);
    }

    public function complaintCallback()
    {
        $data = [
            'complaint_id'      => $this->request->getGet('ComplaintId'),
            'complaint_status'  => $this->request->getGet('Complaint_Status'),
            'user_remark'       => $this->request->getGet('Remark'),
            'our_remark'        => $this->request->getGet('Commment'),
            'operator_txn_id'   => $this->request->getGet('OPtxnId'),
            'our_txn_id'        => $this->request->getGet('OurTxnId'),
            'requester_txn_id'  => $this->request->getGet('RequesterTxnId'),
            'mobile_no'         => $this->request->getGet('Mobileno'),
            'amount'            => $this->request->getGet('Amount'),
            'recharge_status'   => $this->request->getGet('recharge_Status'),
        ];

        // Validate required fields
        if (!$data['complaint_id'] || !$data['complaint_status'] || !$data['requester_txn_id']) {
            return $this->failNotFound("Missing required parameters.");
        }

        // Save to complaint table
        $model = new ComplaintModel();
        $model->insert($data);

        return $this->respond([
            'message'   => 'Complaint received and stored successfully.',
            'data'      => $data,
        ]);
    }
}
