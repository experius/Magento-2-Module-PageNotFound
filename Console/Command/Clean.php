<?php
declare(strict_types=1);

namespace Experius\PageNotFound\Console\Command;

use Experius\PageNotFound\Helper\UrlCleanUp;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Console\Cli;

class Clean extends Command
{

    const DAYS = "days";

    /**
     * @param UrlCleanUp $cleanUp
     */
    public function __construct(
        protected UrlCleanUp $cleanUp,
    )
    {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $options = [
            new InputOption(
                self::DAYS,
                null,
                InputOption::VALUE_REQUIRED,
                'Days you want to keep the reports'
            )
        ];

        $this->setName("experius_pagenotfound:clean")
            ->setDescription("Cleanup old reports.")
            ->setDefinition($options);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($days = $input->getOption(self::DAYS)) {
            if(!is_numeric($days)) {
                $output->writeln($days . " is not a number");
                return Cli::RETURN_FAILURE;
            }
            $output->writeln("Deleting everything older than " . $days . " days.");
            $deletionCount = $this->cleanUp->execute($days);
        } else {
            $output->writeln("No days given, using days from admin if available.");
            $deletionCount = $this->cleanUp->execute();
        }
        $output->writeln('Removed ' . $deletionCount . ' from 404 reports.');
        return Cli::RETURN_SUCCESS;
    }

}

