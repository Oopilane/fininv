<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Component\HttpFoundation\JsonResponse;

class LoginController extends ApiController {
    
    // Actually doesn't do much since lexik handles everything
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(#[CurrentUser()] ?User $user)
    {
        if(is_null($user))
            return new Response("Incorrect username or password", Response::HTTP_UNAUTHORIZED);
    }

    // Frontend startup
    #[Route('/api/check', name: 'api_check', methods: ['GET'])]
    public function check(#[CurrentUser()] ?User $user)
    {
        if(is_null($user))
            return new Response("Incorrect username or password", Response::HTTP_UNAUTHORIZED);

        $response = $this->serializer->serialize(
            $user,
            JsonEncoder::FORMAT,
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['password','email','roles','userIdentifier','transactions']]
        );


        return new Response($response);
    }
}