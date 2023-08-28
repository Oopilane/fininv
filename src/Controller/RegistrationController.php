<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Controller\ApiController;
use App\Entity\User;
use App\Exception\ApiException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route("/api")]
class RegistrationController extends ApiController {

    #[Route("/register",name: "registration", methods:["POST"])]
    public function register(UserPasswordHasherInterface $passHash, Request $request) {
        $newUser = $request->getContent();
        
        $newUser = $this->validateUser($newUser);
        
        $user = new User();
        $user->setEmail($newUser["email"]);
        $user->setUsername($newUser["username"]);

        $password = $passHash->hashPassword($user,$newUser['password']);
        $user->setPassword($password);
        $user->setBalance(10000);
        $user->setProfit(0);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return new Response("User Created");
    }

    private function validateUser($user) {
        if(is_null($user))
            throw new ApiException("Missing User");

        $user = json_decode($user,true);
        
        if(!isset($user['email']))
            throw new ApiException("Missing Email");
        if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) 
            throw new ApiException("Invalid Email");
        if(!isset($user['username']))
            throw new ApiException("Missing Username");
        if(!isset($user['password']))
            throw new ApiException("Missing Password");

        return $user;
    }
}