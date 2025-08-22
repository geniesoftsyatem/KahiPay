<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Libraries\Pagination;
use App\Controllers\BaseController;

class HomeController extends BaseController
{

    protected $db;
    protected $pagination;

    public function __construct()
    {
        // Initialize models
        $this->db = \Config\Database::connect();
        $this->pagination = new Pagination();
    }

    public function index()
    {
        set_title('Dashboard | ' . SITE_NAME);

        $data = [];
        return view('frontend/index', $data);
    }
}
