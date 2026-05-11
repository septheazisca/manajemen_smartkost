<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
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
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. cek sudah login atau belum
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // 2. cek must_change_password
        // kalau harus ganti password, paksa redirect ke halaman ganti password
        // kecuali kalau memang sedang di halaman change-password itu sendiri
        $currentUrl = current_url();
        $isChangePw = str_contains($currentUrl, 'change-password');

        if (session()->get('must_change_password') == 1 && !$isChangePw) {
            return redirect()->to('/change-password')
                ->with('info', 'Kamu harus mengganti password sebelum melanjutkan.');
        }

        // 3. cek role
        $userRole = session()->get('role');

        if ($arguments && !in_array($userRole, $arguments)) {
            return redirect()->to('/unauthorized');
        }
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
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
