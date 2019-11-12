<?php


namespace App\Console\Command;


use App\Common\Traits\ChecksSystemPassword;
use App\Database\EntityService;
use App\Entity\Ingredient;
use App\Ingredients\Service\IngredientService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class IngredientsAddCommand extends Command
{
    use ChecksSystemPassword;

    private const INGREDIENT_NAME = 'name';
    private const INGREDIENT_QUANTITY = 'quantity';
    private const ADMIN_PASSWORD = 'password';
    protected static $defaultName = 'ingredients:add-quantity';
    /**
     * @var IngredientService
     */
    private $ingredientService;
    /**
     * @var \App\Database\EntityService
     */
    private $entityManagerService;

    public function __construct(IngredientService $ingredientService, EntityService $entityManagerService, string $name = null)
    {
        $this->ingredientService = $ingredientService;
        $this->entityManagerService = $entityManagerService;
        parent::__construct($name);
    }

    protected function configure()
    {
        $definition = new InputDefinition([
            new InputOption(
                self::INGREDIENT_NAME,
                'in',
                InputOption::VALUE_REQUIRED,
                'Name of the ingredient. If the name does not match a known ingredient the command will fail.'
            ),
            new InputOption(
                self::INGREDIENT_QUANTITY,
                'iq',
                InputOption::VALUE_REQUIRED,
                'Quantity of the ingredient.'

            ),
            new InputOption(
                self::ADMIN_PASSWORD,
                'pass',
                InputOption::VALUE_REQUIRED,
                'Password of the system admin'

            )
        ]);

        $this->setDescription('Add a certain quantity of a certain ingredient. Available only for machine admins.')
            ->setDefinition($definition);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //First we need to see if this is an admin
        //Only admins can update the quantity of ingredients
        $password = $input->getOption(self::ADMIN_PASSWORD) ?? '';
        $this->checkSystemPassword($password);
        $potentialIngredientName = $input->getOption(self::INGREDIENT_NAME);
        $potentialIngredientQuantity = $input->getOption(self::INGREDIENT_QUANTITY);
        if (empty($potentialIngredientQuantity || $potentialIngredientName)) {
            throw new \InvalidArgumentException('All ingredient data is needed to refill the machine.');
        }
        $isKnown = false;

        //Then we check to see if this is an ingredient that we already know of
        /** @var Ingredient[] $allIngredients */
        $allIngredients = $this->ingredientService->getAllIngredients();
        foreach ($allIngredients as $knownIngredient) {
            if ($knownIngredient->getName() === $potentialIngredientName) {
                $isKnown = true;
                break;
            }
        }

        //If we don't know the ingredient we do not update its quantity
        if (!$isKnown) {
            throw new \InvalidArgumentException('Ingredient' . $potentialIngredientName . ' is not known.');
        }

        $this->ingredientService->update($potentialIngredientName, (int)$potentialIngredientQuantity);
        $this->entityManagerService->commitEntities();
    }
}