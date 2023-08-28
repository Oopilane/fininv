<?php

namespace App\Command;

use App\Entity\Stock;
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:stock:setPrice', 'Deletes rejected and spam comments from the database')]
class StockPriceCommand extends Command {

    public function __construct(private EntityManagerInterface $entityManager) {
        parent::__construct();
        $this->entityManager = $entityManager;
    }
    protected function configure()
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $stockRepository = $this->entityManager->getRepository(Stock::class);
        $stocks = $stockRepository->findall();

        for($i = 0; $i < sizeof($stocks); $i++) {

            $priceAdjustment = rand(0,10);
            $centAdjustment = rand(0,10)/10;
            $priceAdjustment = $priceAdjustment + $centAdjustment;

            $newPrice = 0;
            $type = rand(0,1);
            if($type > 0) {
                $newPrice = $stocks[$i]->getValue() - $priceAdjustment;
            } else {
                $newPrice = $stocks[$i]->getValue() + $priceAdjustment;
            }
            if($newPrice < 0) {
                $newPrice = $stocks[$i]->getValue();
            }
            $stocks[$i]->setValue($newPrice);

            $this->entityManager->persist($stocks[$i]);
            $this->entityManager->flush();


        }

        return Command::SUCCESS;
    }
}