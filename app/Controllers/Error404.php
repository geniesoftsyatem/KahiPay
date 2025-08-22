<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Error404 extends Controller
{
    public function index()
    {
        // Load the view to show for the 404 error
        echo view('404'); // Ensure you have a view file at `app/Views/errors/404.php`
    }
}
