<?php

namespace Experius\PageNotFound\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;


class Import extends Command
{

    const TO_URL_REPLACE_OPTION = "to_url_replace_with";
    const FROM_URL_REPLACE_OPTION = "from_url_replace_with";
    const FILENAME_OPTION = "file";
    const DRY_RUN_OPTION = 'dry_run';

    CONST FILENAME_IMPORT = 'pagenotfound.csv';

    protected $fileDirectory = \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR;

    protected $storeId = 0;

    protected $csv;
    protected $state;
    protected $directoryList;

    protected $input;
    protected $output;
    
    protected $pageNotFoundFactory;

    public function __construct(
        \Magento\Framework\File\Csv $csv,
        \Magento\Framework\App\State $state,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Experius\PageNotFound\Model\PageNotFoundFactory $pageNotFoundFactory
    ){
        $this->csv = $csv;
        $this->state = $state;
        $this->directoryList = $directoryList;
        $this->pageNotFoundFactory = $pageNotFoundFactory;

        return parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {

        $this->input = $input;
        $this->output = $output;

        $this->state->setAreaCode('adminhtml');

        $csvRows = $this->readCsv();

        $convertedRows = $this->convertRows($csvRows);

        if(!$this->input->getOption(self::DRY_RUN_OPTION)) {
            $this->importRedirects($convertedRows);
        }

        $this->renderConversion($convertedRows);

    }

    protected function convertRows($csvRows){
        $rows = [];
        $count = 1;
        foreach($csvRows as $csvRow){

            if(!$csvRow[1]){
                continue;
            }

            $fromUrl = $this->replaceFromUrlDomain($csvRow[0]);
            $toUrl = $this->replaceToUrlDomain($csvRow[1]);

            $rows[] = ['from_url'=>$fromUrl,'to_url'=>$toUrl];

            if($this->input->getOption(self::DRY_RUN_OPTION) && $count>=10){
                break;
            }

            $count++;
        }
        return $rows;
    }

    protected function replaceToUrlDomain($url){
        if($this->input->getOption(self::TO_URL_REPLACE_OPTION)){
            $domain = $this->input->getOption(self::TO_URL_REPLACE_OPTION);
            $url = $this->replaceDomain($url,$domain);
        }
        return $url;
    }

    protected function replaceFromUrlDomain($url){
        if($this->input->getOption(self::FROM_URL_REPLACE_OPTION)){
            $domain = $this->input->getOption(self::FROM_URL_REPLACE_OPTION);
            $url = $this->replaceDomain($url,$domain);
        }
        return $url;
    }

    protected function replaceDomain($url,$domain){
        $urlParts = parse_url($url);

        $params = (isset($urlParts['query']) && $urlParts['query']) ? '?' . $urlParts['query'] : '';
        $path = (isset($urlParts['path']) && $urlParts['path']) ? $urlParts['path'] : '';
        $url = $domain . $path . $params;

        return $url;
    }

    protected function importRedirects($rows){
        foreach($rows as $row){
            $this->savePageNotFound($row['from_url'],$row['to_url']);
        }
    }

    protected function savePageNotFound($fromUrl,$toUrl){
       /* @var $pageNotFound \Experius\PageNotFound\Model\PageNotFound */
       $pageNotFound = $this->pageNotFoundFactory->create();

       $pageNotFound->load($fromUrl,'from_url');

       $pageNotFound->setFromUrl($fromUrl);
       $pageNotFound->setToUrl($toUrl);
       $pageNotFound->save();
    }

    protected function readCsv(){

        $filePath = ($this->input->getOption(self::FILENAME_OPTION)) ? $this->input->getOption(self::FILENAME_OPTION) : $this->directoryList->getPath($this->fileDirectory) . "/" . self::FILENAME_IMPORT;

        if(!file_exists($filePath)){
            $this->output->writeln("File not found");
            return false;
        }

        $rows = $this->csv->setDelimiter(';')->setEnclosure('"')->getData($filePath);

        return $rows;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("experius_pagenotfound:import");

        $this->setDescription("Import 404 redirects");

        $this->addOption(
            self::FROM_URL_REPLACE_OPTION,'fr',
            InputOption::VALUE_OPTIONAL,
            'Replace to url domain with'
        );

        $this->addOption(
            self::TO_URL_REPLACE_OPTION,'to',
            InputOption::VALUE_OPTIONAL,
            'Replace to url domain with'
        );

        $this->addOption(
            self::FILENAME_OPTION,'f',
            InputOption::VALUE_OPTIONAL,
            'Path to file'
        );

        $this->addOption(
            self::DRY_RUN_OPTION,'d',
            InputOption::VALUE_OPTIONAL,
            'Dry run option'
        );

        parent::configure();
    }

    private function renderConversion($data)
    {
        $table = new Table($this->output);

        $table->setHeaders(array('From url', 'To url'));
        $table->setRows($data);
        $table->render($data);
    }
}
