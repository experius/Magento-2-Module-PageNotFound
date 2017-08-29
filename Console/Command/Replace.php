<?php

namespace Experius\PageNotFound\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;


class Replace extends Command
{

    const FIND = "find";
    const REPLACE = "replace";
    const DRY_RUN_OPTION = 'dry_run';

    private $connection;
    private $resourceConnection;

    protected $input;
    protected $output;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\App\State $state
    ){
        $this->resourceConnection = $resourceConnection;
        $this->connection = $this->resourceConnection->getConnection();
        $this->state = $state;

        return parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ){

        $this->input = $input;
        $this->output = $output;

        $this->state->setAreaCode('adminhtml');

        if(!$this->input->getOption(self::FIND)) {
            $this->output->writeln("No Find value");
            return false;
        }

        if(!$this->input->getOption(self::REPLACE)) {
            $this->output->writeln("No Replace value");
            return false;
        }

        $find = $this->input->getOption(self::FIND);
        $replace = $this->input->getOption(self::REPLACE);


        foreach(['from_url','to_url'] as $field) {
            $this->findAndReplace('experius_page_not_found', $field, $find, $replace);
        }

    }

    protected function findAndReplace($table,$field,$find,$replace){
        $tableName  = $this->resourceConnection->getTableName($table);

        /* @var $result \Magento\Framework\DB\Statement\Pdo\Mysql */

        if($this->input->getOption(self::DRY_RUN_OPTION)) {
            $result = $this->connection->query("SELECT `{$field}` FROM `{$tableName}` WHERE `{$field}` LIKE '%{$find}%'");
            $this->output->writeln(sprintf("%d values would be replaced with this command in the %s column",$result->rowCount(),$field));
        } else {
            $result = $this->connection->query("UPDATE `{$tableName}` SET `{$field}` = REPLACE(`from_url`,'{$find}','{$replace}') WHERE `{$field}` LIKE '%{$find}%'");
            $this->output->writeln(sprintf("%d values replaced in the %s column",$result->rowCount(),$field));
        }

    }

    protected function configure()
    {
        $this->setName("experius_pagenotfound:replace");

        $this->setDescription("Replace values in the redirect table");

        $this->addOption(
            self::FIND,'f',
            InputOption::VALUE_REQUIRED,
            'Find'
        );

        $this->addOption(
            self::REPLACE,'r',
            InputOption::VALUE_REQUIRED,
            'Replace'
        );

        $this->addOption(
            self::DRY_RUN_OPTION,'d',
            InputOption::VALUE_OPTIONAL,
            'Replace'
        );

        parent::configure();
    }
}