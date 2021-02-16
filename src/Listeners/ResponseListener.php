<?php
namespace App\Listeners;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class ResponseListener
{
    public function onKernelResponse(ResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($this->disableThisPageCache($request->getPathInfo())){
        $headers = $event->getResponse()->headers;
        $headers->set('Cache-Control', 'no-cache, no-store, must-revalidate'); // HTTP 1.1.
        $headers->set('Pragma', 'no-cache'); // HTTP 1.0.
        $headers->set('Expires', '0'); // Proxies.
        }
    }

    private function disableThisPageCache($currentPath)
    {
        $paths = array('/admin', '/', '^/user');

        foreach ($paths as $path) {
            if ($this->checkPathBegins($currentPath, $path)) {
                return true;
            }
        }

        return false;
    }

    private function checkPathBegins($path, $string)
    {
        return substr($path, 0, strlen($string)) === $string;
    }
}