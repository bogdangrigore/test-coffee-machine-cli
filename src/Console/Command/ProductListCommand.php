<?php
namespace App\Console\Command;

use App\Products\Service\ProductsService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProductListCommand extends Command
{
    private const ONLY_AVAILABLE = 'only-available';
    protected static $defaultName = 'coffee:list-products';
    /**
     * @var ProductsService
     */
    private $productsService;

    public function __construct(ProductsService $productsService, string $name = null)
    {
        $this->productsService = $productsService;
        parent::__construct($name);
    }

    protected function configure()
    {
        $definition = new InputDefinition([
            new InputOption(self::ONLY_AVAILABLE, 'a', InputOption::VALUE_NONE, 'List only available products.')
        ]);

        $this->setDescription('List all beverages sold by this coffee vending machine.')
            ->setDefinition($definition);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $onlyAvailable = (bool)$input->getOption(self::ONLY_AVAILABLE);
        $products = $this->productsService->getAllProducts($onlyAvailable);
        $outputData = [];
        foreach ($products as $key => $product) {
            $outputData[$key][]= $product->getId();
            $outputData[$key][]= $product->getName();
            $outputData[$key][]= (double)$product->getPrice() / 100;
        }
        $table = new Table($output);
        $table ->setHeaders(['ID', 'Name', 'Price']);
        $table->setRows($outputData);
        $table->render();
    }
}