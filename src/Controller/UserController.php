<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

use App\Controller\ApiController;
use App\Entity\User;
use App\Exception\ApiException;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route("/api/users")]
class UserController extends ApiController {


    #[Route("/profitable",name: "profitableUsers", methods:["GET"])]
    public function getProfitableUsers(#[CurrentUser()] ?User $user): Response {
        if(is_null($user))
            throw new ApiException("Unauthorized",Response::HTTP_UNAUTHORIZED);

        $temp = $this->entityManager->getRepository(User::class)->findAll(array(),array('profit'=> 'ASC'));
        if(!$temp)
            return new Response();
        $users = $this->serializer->serialize(
            $temp,
            JsonEncoder::FORMAT,
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['password','email','roles','userIdentifier','transactions']]
        );
        return new Response($users);
    }
}