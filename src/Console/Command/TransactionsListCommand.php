<?php


namespace App\Console\Command;


use App\Common\Traits\ChecksSystemPassword;
use App\Transaction\Service\TransactionService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TransactionsListCommand extends Command
{
    use ChecksSystemPassword;

    private const ADMIN_PASSWORD = 'password';
    protected static $defaultName = 'transactions:list';
    /**
     * @var TransactionService
     */
    private $transactionService;

    public function __construct(TransactionService $transactionService, string $name = null)
    {
        $this->transactionService = $transactionService;
        parent::__construct($name);
    }

    protected function configure()
    {
        $definition = new InputDefinition([
            new InputOption(
                self::ADMIN_PASSWORD,
                'pass',
                InputOption::VALUE_REQUIRED,
                'Password of the system admin'

            )
        ]);

        $this->setDescription('List all transactions in the system.')
            ->setDefinition($definition);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //First we need to see if this is an admin
        //Only admins can list the transactions
        $password = $input->getOption(self::ADMIN_PASSWORD) ?? '';
        $this->checkSystemPassword($password);
        $transactions = $this->transactionService->getAllTransactions();
        $outputData = [];
        foreach ($transactions as $key => $transatcion) {
            $outputData[$key][]= $transatcion->getId();
            $outputData[$key][]= $transatcion->getProduct()->getName();
            $outputData[$key][]= (double)$transatcion->getProduct()->getPrice() / 100;
            $outputData[$key][]= $transatcion->getPaymentMethod()->getName();
        }
        $table = new Table($output);
        $table ->setHeaders(['Transaction ID', 'Product Name', 'Product Price', 'Payment Method']);
        $table->setRows($outputData);
        $table->render();
    }
}