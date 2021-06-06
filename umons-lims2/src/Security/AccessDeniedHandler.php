<?php


namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\Security\Http\HttpUtils;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{

    private $httpUtils;
    private $security;

    public function __construct(HttpUtils $httpUtils, Security $security)
    {
        $this->httpUtils = $httpUtils;
        $this->security = $security;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        if($this->security->isGranted('ROLE_USER') && $accessDeniedException->getAttributes() == ['ROLE_ADMIN']) {
                return $this->httpUtils->createRedirectResponse($request, 'login');
        }
    }
}