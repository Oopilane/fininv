<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

use App\Controller\ApiController;
use App\Entity\Portfolio;
use App\Entity\User;
use App\Exception\ApiException;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route("/api/portfolio")]
class PortfolioController extends ApiController {


    #[Route("",name: "portfolio", methods:["GET"])]
    public function getAllStocks(#[CurrentUser()] ?User $user): Response {
        if(is_null($user))
            throw new ApiException("Unauthorized",Response::HTTP_UNAUTHORIZED);

        $temp = $this->entityManager->getRepository(Portfolio::class)->findBy(array('User' => $user->getId()));
        if(!$temp)
            return new Response();
        $portfolio = $this->serializer->serialize(
            $temp,
            JsonEncoder::FORMAT,
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['password','email','roles','userIdentifier','transactions']]
        );
        return new Response($portfolio);
    }
}