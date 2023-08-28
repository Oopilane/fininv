<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

use App\Controller\ApiController;
use App\Entity\Portfolio;
use App\Entity\Stock;
use App\Entity\StockPortfolio;
use App\Entity\Transaction;
use App\Entity\User;
use App\Exception\ApiException;

enum eTransactionType: int {
    case BUY = 0;
    case SELL = 1;
}

#[Route("/api/stocks")]
class StockController extends ApiController {


    #[Route("",name: "stocks", methods:["GET"])]
    public function getAllStocks(): JsonResponse {
        $stocks = $this->entityManager->getRepository(Stock::class)
                    ->createQueryBuilder('e')
                    ->select('e.name, e.symbol, e.value, e.created')
                    ->getQuery()
                    ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return new JsonResponse($stocks);
    }
    #[Route("/symbols",name: "stockSymbols", methods:["GET"])]
    public function getSymbols(): JsonResponse {
        $stocks = $this->entityManager->getRepository(Stock::class)
                    ->createQueryBuilder('e')
                    ->select('e.symbol')
                    ->getQuery()
                    ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        return new JsonResponse($stocks);
    }

    #[Route("/trade",name: "tradeStock",methods:["POST"])]
    public function tradeStock(Request $request, #[CurrentUser()] ?User $user): Response {
        // the authenticator should stop this situation from ever happening but can never be too sure
        if(is_null($user))
            throw new ApiException("Unauthorized",Response::HTTP_UNAUTHORIZED);

        $offer = $this->validateOffer($request->getContent());
        $this->createTransaction($offer,$user);


        return new Response("Transaction Complete");
    }



    // Helper Functions
    // Can be moved to a service later on
    private function validateOffer($offer) {
        if(is_null($offer))
            throw new ApiException("Missing offer");
        
        $decodedOffer = json_decode($offer,true);

        if(!isset($decodedOffer['symbol']))
            throw new ApiException("Missing Stock");
        if(!isset($decodedOffer['type']))
            throw new ApiException("Missing Type");
        if(!isset($decodedOffer['quantity']))
            throw new ApiException("Missing Quantity");

        return $decodedOffer;
    }

    private function createTransaction($offer, $user) {
        $stock = $this->entityManager->getRepository(Stock::class)->findBySymbol($offer['symbol']);
        if(!$stock)
            return new ApiException("Stock does not exist",Response::HTTP_BAD_REQUEST);


        // Everything below here should seriously be moved to a service or something separate

        $portfolio = $this->entityManager->getRepository(Portfolio::class)->findOneBy(array('User'=> $user->getId()));
        if(!$portfolio) {
            // Create a portfolio for the user
            $portfolio = new Portfolio();
            $portfolio->setUser($user);
            
            $this->entityManager->persist($portfolio);
            $this->entityManager->flush();
        }
        $stockPortfolio = $this->entityManager->getRepository(StockPortfolio::class)->findOneBy(array('Stock'=> $stock->getId(), 'Portfolio'=> $portfolio->getId()));

        // StockPort doesn't exist
        if(!$stockPortfolio) {
            // Can't sell whatcha don't own
            if($offer['type'] == eTransactionType::SELL->value) 
                throw new ApiException('You don\'t own that :p');
            // Create the stock relation
            $stockPortfolio = new StockPortfolio();
            $stockPortfolio->setPortfolio($portfolio);
            $stockPortfolio->setStock($stock);
            $stockPortfolio->setAmount(0);

            $this->entityManager->persist($stockPortfolio);
            $this->entityManager->flush();
        }
        // get the price to add/remove from your acount
        $price = $stock->getValue() * $offer['quantity'];
        
        $sold = false;
        $newBalance = 0;
        $curBalance = $user->getBalance();
        // Buy Stock
        if($offer['type'] == eTransactionType::BUY->value) {
            // get current stock and add amount you're buying
            $newValue = $stockPortfolio->getAmount() + $offer['quantity'];
            $stockPortfolio->setAmount($newValue);

            if($price > $curBalance) 
                throw new ApiException("You're broke!");
            $newBalance = $curBalance - $price;
            
        } 
        // Sell Stock
        else {
            // Determine Profitability
            $newValue = $stockPortfolio->getAmount() - $offer['quantity'];
            // Make sure you aren't selling more than you own
            if($newValue <= 0) {
                $offer['quantity'] = 0;
                $newValue = $stockPortfolio->getAmount();
                // Adjust price
                $price = $stock->getValue() * $newValue;
                // Remove from portfolioa
                $portfolio->removeStockPortfolio($stockPortfolio);
                $sold = true;
            }

            $user->setProfit($this->DetermineProfitability($user,$stock,$newValue));
            $stockPortfolio->setAmount($newValue);
            $newBalance = $curBalance + $price;
        }
        
        // Save Stock Portfolio 
        if($sold)
            $this->entityManager->remove($stockPortfolio);
        else 
            $this->entityManager->persist($stockPortfolio);
        $this->entityManager->flush();
        
        // Save Transaction
        $transaction = new Transaction();
        $transaction->setUser($user);
        $transaction->setStock($stock);
        $transaction->setPrice($stock->getValue());
        $transaction->setAmount($offer['quantity']);
        $transaction->setType($offer['type']);
        $transaction->setSold(false);
        $transaction->setTotalSold(0);

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        // Save user balance
        $user->setBalance($newBalance);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

    }

    /*
        Yeah this doesn't work
        If you sell less than the transaction holds, it doesn't get marked sold... 
        oh I guess we could just set the amount we already sold... 
        yeah that works. 
        this feels like it's waaaay more complicated than it has to be
    */
    private function DetermineProfitability($user, $stock, $amountSold): float {
        $transactions = $this->entityManager->getRepository(Transaction::class)->findBy(
            array(
                'user'=> $user->getId(),
                'stock' => $stock->getId(),
                'sold' => false
            ),
            array(
                'price' => 'ASC'
            )
        );

        $profitability = $user->getProfit();

        for($i = 0; $i < sizeof($transactions); $i++) {

            if($amountSold <= 0) {
                break;
            }

            /*
                okay
                we buy 20 stocks

                I sell 5 stocks

                the transaction now has 15 stocks left

                I sell 10 stocks

                now it has 5 stocks left

                i buy 15 stocks

                now we have 2 transactions
                one with 5 and one with 15 for a total of 20

                i sell 20 stocks

                transaction[0] total stocks 5

                    (15)            (20)            (5)
                $amountSold = $amountSold - total stock

                how do we figure out we only have 5 left. 
                
                am i over thinking this?
                    (0)        (20)        (15)        (15)
                $transAm = $amount - $totalSold - $amountSOld

                 (5)           (5)             (0)
                $amountSold = $amountSold - $transactionAmount;

                seriously what was I doing. sleep depraviation is a hellova drug
            */

            $transactionAmount = $transactions[$i]->getAmount() - $transactions[$i]->getTotalSold() - $amountSold;

            // You're selling more than this transaction gave you or you've sold it all
            if ($transactionAmount <= 0) {
                $transactions[$i]->setSold(true);
                $transactions[$i]->setTotalSold($transactions[$i]->getAmount());
            } else {
                $transactions[$i]->setTotalSold($transactions[$i]->getTotalSold() + $transactionAmount);
            }

            $amountSold = $amountSold - $transactionAmount;

            // Profitability += (stock current price - old price) * amount
            if ($transactionAmount <= 0)
                $profitability += ($transactions[$i]->getStock()->getValue() - $transactions[$i]->getPrice()) * $amountSold;
            else
                $profitability += ($transactions[$i]->getStock()->getValue() - $transactions[$i]->getPrice()) * $transactionAmount;


            $this->entityManager->persist($transactions[$i]);
            $this->entityManager->flush();
        }
        return $profitability;
    }
}