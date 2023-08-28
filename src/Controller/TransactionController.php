<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

use App\Controller\ApiController;
use App\Entity\Transaction;
use App\Entity\User;
use App\Exception\ApiException;

#[Route("/api/transactions")]
class TransactionController extends ApiController {

    #[Route("",name: "transactions", methods:["GET"])]
    public function getUserTransactions(#[CurrentUser()] ?User $user) {
        // the authenticator blah blah
        if(is_null($user))
            throw new ApiException("Unauthorized",Response::HTTP_UNAUTHORIZED);

        $transactions = $this->entityManager->getRepository(Transaction::class)->findByUser($user);

        return new JsonResponse($transactions);
    }

    #[Route("/all",name: "transactions", methods:["GET"])]
    public function getAllTransactions() {
        /*
        $transactions = $this->entityManager->getRepository(Transaction::class)
        ->createQueryBuilder('t')
        ->select('t')
        ->orderBy('t.created')
        ->getQuery()
        ->getArrayResult();
        */
        $temp = $this->entityManager->getRepository(Transaction::class)->findAll();
        $transactions = $this->serializer->serialize(
            $temp,
            JsonEncoder::FORMAT,
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['password','email','roles','userIdentifier','transactions']]
        );
        return new Response($transactions);
    }

    // Oops. Guess i could move it here but i like the sound of stocks/trade better than /transactions :cirno_uwu:
    // Leave it for now, remove later if need be
    #[Route("",name: "performTransaction",methods:["POST"])]
    public function newTransaction(Request $request) {
        
    }
    
}