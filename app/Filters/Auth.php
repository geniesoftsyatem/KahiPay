<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

class Auth implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $userId = $session->get('user_id');
        $isLoggedIn = $session->get('logged_in');

        // Redirect to login if not logged in
        if (!$isLoggedIn) {
            return redirect()->to(site_url('login'));
        }

        // // Refresh user permissions and module access if logged in
        // if ($userId) {
        //     $userModel = new UserModel();
        //     $user = $userModel->where('user_id', $userId)->first();

        //     if ($user) {
        //         $session->set('permissions', json_decode($user['permissions'], true));

        //         // Store module access in session
        //         $session->set('module_access', json_decode($user['module_access'], true));
        //     }
        // }

        // // Check user role if specified in filter arguments
        // if (isset($arguments[0]) && $arguments[0]) {
        //     // Redirect or show an error if the user role does not match
        //     return redirect()->to(site_url('no_access'));
        // }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
