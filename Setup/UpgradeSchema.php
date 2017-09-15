<?php

namespace Experius\PageNotFound\Setup;

use Experius\PageNotFound\Model\PageNotFound;
use Experius\PageNotFound\Model\PageNotFoundRepository;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use \Magento\Framework\DB\Ddl\Table;

/**
 * Upgrade the Catalog module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * @var PageNotFoundRepository
     */
    protected $pageRepository;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        PageNotFoundRepository $pageRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder
    ) {

        $this->pageRepository = $pageRepository;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->makeFromUrlUnique($setup);
        }

        $setup->endSetup();
    }

    protected function makeFromUrlUnique(SchemaSetupInterface $setup)
    {
        $tableName = $setup->getTable('experius_page_not_found');
        if ($setup->tableExists($tableName)) {
            $connection = $setup->getConnection();
            $connection->changeColumn(
                $tableName,
                'from_url',
                'from_url',
                'varchar(1024) COMMENT "from_url"'
            );
            $sql = "SELECT from_url, COUNT(*) c FROM {$tableName} GROUP BY from_url HAVING c > 1";
            $sql = $connection->select()
                ->from($tableName, ['from_url', new \Zend_Db_Expr('COUNT(*) c')]) // to select all fields
                ->group('from_url')
                ->having('c > 1');

            $duplicates = $connection->fetchAll($sql);
            foreach ($duplicates as $duplicate) {
                $fromUrl = $duplicate['from_url'];

                $this->searchCriteriaBuilder->addFilter(

                    $this->filterBuilder->setField('from_url')->setConditionType('eq')->setValue($fromUrl)->create()
                );
                $searchCriteria = $this->searchCriteriaBuilder->create();
                $pageResult = $this->pageRepository->getList($searchCriteria);
                $pageThatShouldStay = null;
                $pagesToDelete = [];
                foreach ($pageResult->getItems() as $page) {
                    if (!$pageThatShouldStay) {
                        $pageThatShouldStay = $page;
                        continue;
                    }
                    /** @var PageNotFound $pageThatShouldStay */
                    if (!$pageThatShouldStay->getToUrl()) {
                        $page->setCount($page->getCount() + $pageThatShouldStay->getCount());
                        $page->setCountRedirect($page->getCountRedirect() + $pageThatShouldStay->getCountRedirect());
                        $pagesToDelete[] = $pageThatShouldStay;
                        $pageThatShouldStay = $page;
                    } else {
                        $pageThatShouldStay->setCount($page->getCount() + $pageThatShouldStay->getCount());
                        $pageThatShouldStay->setCountRedirect(
                            $page->getCountRedirect() + $pageThatShouldStay->getCountRedirect()
                        );
                        $pagesToDelete[] = $page;
                    }
                }
                $this->pageRepository->save($pageThatShouldStay);
                foreach ($pagesToDelete as $page) {
                    $this->pageRepository->delete($page);
                }
            }
            $connection->addIndex(
                $tableName,
                $setup->getIdxName(
                    $tableName,
                    ['from_url'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['from_url'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE

            );
        }
    }
}
