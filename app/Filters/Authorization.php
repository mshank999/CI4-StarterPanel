<?php

namespace App\Filters;

use App\Models\ApplicationModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Authorization implements FilterInterface
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

    protected $ApplicationModel;
    public function before(RequestInterface $request, $arguments = null)
    {
        $uri                     = service('uri');
        $this->ApplicationModel  = new ApplicationModel();
        $segment                 = $uri->getSegment(1);

        if ($segment) {
            $menu         = $this->ApplicationModel->getMenuByUrl($segment);
            if (!$menu) {
                //not found
                return redirect()->to(base_url('/'));
            } else {
                $dataAccess = [
                    'roleID' => session()->get('role'),
                    'menuItemID' => $menu['id']
                ];
                $userAccess = $this->ApplicationModel->checkUserAccess($dataAccess);
                if (!$userAccess) {
                    // not granted
                    return redirect()->to(base_url('blocked'));
                }
            }
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
