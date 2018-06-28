<?php
namespace Magefox\SSOIntegration\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magefox\SSOIntegration\Model\Auth0\ApiFactory;

class Auth0Sync extends Command
{
    const COMMAND = 'sso:auth0:sync';

    /**
     * @var State
     */
    protected $appState;

    /**
     * @var CollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var ApiFactory
     */
    protected $apiFactory;

    public function __construct(
        State $appState,
        CollectionFactory $customerCollectionFactory,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        ApiFactory $apiFactory
    ) {
        $this->appState = $appState;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->customerRegistry = $customerRegistry;
        $this->apiFactory = $apiFactory;

        parent::__construct();
    }

    /**
     * Configure command options
     */
    protected function configure()
    {
        $this->setName(self::COMMAND)
            ->setDescription('Sync magento users to Auth0.')
        ;

        parent::configure();
    }

    /**
     * Execute command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question = new \Symfony\Component\Console\Question\ConfirmationQuestion('<info>Sync customers to Auth0, Your customers will have to change password manually. Are you sure? (Yes/No):</info> ', false);
        if(!$helper->ask($input, $output, $question))
            return;

        $this->appState->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
        $output->setDecorated(true);

        $customerIds = $this->customerCollectionFactory
            ->create()
            ->getAllIds()
        ;

        $progress = new \Symfony\Component\Console\Helper\ProgressBar($output, count($customerIds));
        $progress->setFormat("<comment>Syncing customers to Auth0</comment> %current%/%max% [%bar%] %percent:3s%% %elapsed%\n");

        try {
            foreach ($customerIds as $customerId) {
                /**
                 * @var $customer \Magento\Customer\Model\Customer
                 */
                $customer = $this->customerRegistry->retrieve($customerId);
                $data = [
                    "name"              => $customer->getFirstname(),
                    "email"             => $customer->getEmail(),
                    "password"          => $customer->getPasswordHash(),
                    "email_verified"    => true,
                    "user_metadata"     => [
                        "firstname"     => $customer->getFirstname(),
                        "lastname"      => $customer->getLastname()
                    ],
                    "connection"        => "Username-Password-Authentication"
                ];

                $this->apiFactory->create()
                    ->createUser($data);
                // $response['statusCode'] === 409: User already exists

                $progress->advance();
            }
        } catch (\Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        $output->write("\n");
        return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
    }
}
