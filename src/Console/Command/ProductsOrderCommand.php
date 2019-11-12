<?php


namespace App\Console\Command;


use App\Database\EntityService;
use App\Entity\OrderTransaction;
use App\Entity\PaymentMethod;
use App\Entity\Product;
use App\Ingredients\Service\IngredientService;
use App\Monetary\Service\BalanceService;
use App\PaymentMethod\Service\PaymentMethodService;
use App\Products\Service\ProductsService;
use App\Transaction\Service\TransactionService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProductsOrderCommand extends Command
{
    use LockableTrait;

    private const OPTION_PNAME = 'product-name';
    private const OPTION_PID = 'product-id';
    private const OPTION_PAYMETHOD = 'payment-method';
    private const OPTION_PAYMENT_AMOUNT_RON = 'payment-amount-ron';
    private const OPTION_PAYMENT_AMOUNT_BAN = 'payment-amount-ban';
    private const OPTION_QUANTITY = 'quantity';

    protected static $defaultName = 'coffee:order-product';
    /**
     * @var ProductsService
     */
    private $productsService;
    /**
     * @var IngredientService
     */
    private $ingredientService;
    /**
     * @var PaymentMethodService
     */
    private $paymentMethodService;
    /**
     * @var BalanceService
     */
    private $balanceService;
    /**
     * @var EntityService
     */
    private $entityManagerService;
    /**
     * @var TransactionService
     */
    private $transactionService;

    public function __construct(ProductsService $productsService,
                                IngredientService $ingredientService,
                                PaymentMethodService $paymentMethodService,
                                BalanceService $balanceService,
                                TransactionService $transactionService,
                                EntityService $entityManagerService,
                                string $name = null)
    {
        $this->productsService = $productsService;
        $this->ingredientService = $ingredientService;
        $this->paymentMethodService = $paymentMethodService;
        $this->balanceService = $balanceService;
        $this->entityManagerService = $entityManagerService;
        $this->transactionService = $transactionService;
        parent::__construct($name);
    }

    protected function configure()
    {
        $definition = new InputDefinition([
            new InputOption(self::OPTION_PNAME, 'pn', InputOption::VALUE_REQUIRED, 'Name of the product you want to order.'),
            new InputOption(self::OPTION_PID, 'pid', InputOption::VALUE_REQUIRED, 'ID of the product you want to order.'),
            new InputOption(self::OPTION_PAYMETHOD, 'pm', InputOption::VALUE_REQUIRED, 'Name of the payment method you want to use. Can be either cash or card.'),
            new InputOption(self::OPTION_PAYMENT_AMOUNT_RON, 'par', InputOption::VALUE_REQUIRED, 'If using cash, how much RON you put into the machine. Machine accepts5 or 10 RON banknotes.'),
            new InputOption(self::OPTION_PAYMENT_AMOUNT_BAN, 'pab', InputOption::VALUE_REQUIRED, 'If using cash, how much BAN you put into the machine. Machine accepts 50 bani coins'),
            new InputOption(self::OPTION_QUANTITY, 'pq', InputOption::VALUE_REQUIRED, 'How much product you want.', 1),
        ]);

        $this->setDefinition($definition);
        $this->setDescription('Order one of the drinks available in this coffee machine.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //Machine needs to be locked while a customer orders the drink
        if (!$this->lock()) {
            throw new \RuntimeException('Machine already in use by another user');
        }

        //Validate supplied options
        $productName = $input->getOption(self::OPTION_PNAME);
        $productId = $input->getOption(self::OPTION_PID);
        $productQuantity = (int)$input->getOption(self::OPTION_QUANTITY);
        $paymentMethodName = $input->getOption(self::OPTION_PAYMETHOD);
        $paymentAmountRon = (int)$input->getOption(self::OPTION_PAYMENT_AMOUNT_RON);
        $paymentAmountBan = (int)$input->getOption(self::OPTION_PAYMENT_AMOUNT_BAN);

        if ($productName && $productId) {
            $this->release();
            throw new \UnexpectedValueException('You cannot use both the name and the ID to specify the product you want.');
        }

        if ($paymentAmountBan % 50 !== 0) {
            $this->release();
            throw new \InvalidArgumentException('The bani amount provided is incorrect. Use only one or multiple 50 bani coins.');
        }

        if ($paymentAmountRon % 10 !== 0) {
            if ($paymentAmountRon % 5) {
                $this->release();
                throw new \InvalidArgumentException('The RON amount provided is incorrect. Use only one or multiple 5 or 10 RON banknotes.');
            }
        }

        $paymentMethod = $this->paymentMethodService->getPaymentMethodByName($paymentMethodName);
        if (empty($paymentMethod)) {
            $this->release();
            throw new \InvalidArgumentException('Incorrect payment method supplied. Please use cash or card as options.');
        }
        /** @var PaymentMethod $paymentMethod */
        $paymentMethod = $paymentMethod[0];

        //Internally we use the ID so we need to obtain that
        if ($productName) {
            /** @var Product[] $potentialProducts */
            $potentialProducts = $this->productsService->getProductsByName($productName);
            if (count($potentialProducts) > 1) {
                $this->release();
                throw new \InvalidArgumentException('Please indicate an exact product name.');
            }
            $productId = $potentialProducts[0]->getId();
        }

        //Check if product is available
        if (!$this->productsService->isProductAvailable($productId, $productQuantity)) {
            $this->release();
            throw new \InvalidArgumentException('The product indicated cannot be ordered. Not enough ingredients');
        }

        $product = $this->productsService->getProductById($productId);

        //If payment method is cash we need to see if we can give the customer their change back
        if ($paymentMethod->isCash()) {
            $this->balanceService->addCashToBalance($paymentAmountRon, $paymentAmountBan);
            $this->entityManagerService->commitEntities();
            try {
                $this->validateCashOperation($paymentAmountRon, $paymentAmountBan, $product->getPrice(), $productQuantity);
            } catch (\Exception $e) {
                $this->balanceService->giveOutChange($paymentAmountRon, $paymentAmountBan, 0, 0);
                $this->giveChange($output);
                throw $e;
            }
        }

        //Adjust the quantities of the ingredients in the product
        foreach ($product->getProductIngredients() as $ingredient) {
            $this->ingredientService->reduceQuantity($ingredient->getIngredientId(), $ingredient->getQuantity() * $productQuantity);
        }

        //Adjust balance of the cash reserves
        if ($paymentMethod->isCash()) {
            if ($paymentAmountRon * 100 + $paymentAmountBan - $product->getPrice() * $productQuantity > 0) {
                $this->balanceService->giveOutChange($paymentAmountRon, $paymentAmountBan, $product->getPrice(), $productQuantity);
                $this->giveChange($output);
            }
        }

        //Save transaction to the database
        $transaction = new OrderTransaction();
        $transaction->setProduct($product);
        $transaction->setPaymentMethod($paymentMethod);
        $transaction->setQuantity($productQuantity);
        $this->transactionService->persistTransaction($transaction);

        //Seeing that we are updating a number of entities we need to flush after we make sure all actions can be applied correctly
        $this->entityManagerService->commitEntities();
        $this->release();

        //Output result
        $output->writeln([
            'Your beverage is ready',
            '      )  (',
            '      (   ) )',
            '      ) ( (',
            '    _______)_',
            ' .-\'---------|',
            '( C|/\/\/\/\/|',
            ' \'-./\/\/\/\/',
            '   \'_________\'',
            '    \'-------\'',
        ]);
    }

    /**
     * @param int $paymentAmountRon
     * @param int $paymentAmountBan
     * @param int $productPrice
     * @param int $productQuantity
     * @return bool
     */
    private function validateCashOperation(int $paymentAmountRon, int $paymentAmountBan, int $productPrice, int $productQuantity) : void
    {
        //We need either RON or bani provided in order to pay
        if (empty($paymentAmountRon) && empty($paymentAmountBan)) {
            $this->release();
            throw new \InvalidArgumentException('You need to specify the amount of money you are using to pay.');
        }

        //Verify if the provided amount is enough to cover the price of the product
        $providedAmount = $paymentAmountRon * 100 + $paymentAmountBan;
        if ($providedAmount - $productPrice * $productQuantity < 0) {
            $this->release();
            throw new \InvalidArgumentException('You did not provide enough cash to pay for your product.');
        }

        //Check to see if we can provide the change necessary
        if (!$this->balanceService->canGiveBackChange($providedAmount - $productPrice * $productQuantity)) {
            $this->release();
            throw new \UnexpectedValueException('Please use the card payment method. The machine cannot give you back the change.');
        }
    }

    private function giveChange(OutputInterface $output)
    {
        $output->writeln([
            'Here is your change',
            '                ______________',
            '    __,.,---\'\'\'\'\'              \'\'\'\'\'---...',
            ' ,-\'             .....:::\'\'::.:            \'`-.',
            '\'           ...:::.....       \'',
            '            \'\'\':::\'\'\'\'\'       .               ,',
            '|\'-.._           \'\'\'\'\':::..::\':          __,,-',
            ' \'-.._\'\'`---.....______________.....---\'\'__,,-',
            '      \'\'`---.....______________.....---\'\'',
        ]);
    }
}