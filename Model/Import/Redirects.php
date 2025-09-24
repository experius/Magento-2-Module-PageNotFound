<?php
namespace Experius\PageNotFound\Model\Import;

use Exception;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\ImportExport\Helper\Data as ImportHelper;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\ResourceModel\Helper;
use Magento\ImportExport\Model\ResourceModel\Import\Data;

/**
* Class Courses
*/
class Redirects extends AbstractEntity
{
    const ENTITY_CODE = 'pagenotfound';
    const TABLE = 'experius_page_not_found';
    const ENTITY_ID_COLUMN = 'page_not_found_id';

    /**
    * If we should check column names
    */
    protected $needColumnCheck = true;

    /**
    * Need to log in import history
    */
    protected $logInHistory = true;

    /**
    * Permanent entity columns.
    */
    protected $_permanentAttributes = [
'page_not_found_id'
];

    /**
    * Valid column names
    */
    protected $validColumnNames = [
'page_not_found_id',
'from_url',
'to_url'
];

    /**
    * @var AdapterInterface
    */
    protected $connection;

    /**
    * @var ResourceConnection
    */
    private $resource;

    /**
    * Courses constructor.
    *
    * @param JsonHelper $jsonHelper
    * @param ImportHelper $importExportData
    * @param Data $importData
    * @param ResourceConnection $resource
    * @param Helper $resourceHelper
    * @param ProcessingErrorAggregatorInterface $errorAggregator
    */
    public function __construct(
        JsonHelper $jsonHelper,
        ImportHelper $importExportData,
        Data $importData,
        ResourceConnection $resource,
        Helper $resourceHelper,
        ProcessingErrorAggregatorInterface $errorAggregator
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->resource = $resource;
        $this->connection = $resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator = $errorAggregator;
    }

    /**
    * Entity type code getter.
    *
    * @return string
    */
    public function getEntityTypeCode()
    {
        return static::ENTITY_CODE;
    }

    /**
    * Get available columns
    *
    * @return array
    */
    public function getValidColumnNames(): array
    {
        return $this->validColumnNames;
    }

    /**
    * Row validation
    *
    * @param array $rowData
    * @param int $rowNum
    *
    * @return bool
    */
    public function validateRow(array $rowData, $rowNum): bool
    {
        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }

        $this->_validatedRows[$rowNum] = true;

        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    /**
    * Import data
    *
    * @return bool
    *
    * @throws Exception
    */
    protected function _importData(): bool
    {
        switch ($this->getBehavior()) {
case Import::BEHAVIOR_DELETE:
$this->deleteEntity();
break;
case Import::BEHAVIOR_REPLACE:
$this->saveAndReplaceEntity();
break;
case Import::BEHAVIOR_APPEND:
$this->saveAndReplaceEntity();
break;
}

        return true;
    }

    /**
    * Delete entities
    *
    * @return bool
    */
    private function deleteEntity(): bool
    {
        $rows = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                $this->validateRow($rowData, $rowNum);

                if (!$this->getErrorAggregator()->isRowInvalid($rowNum)) {
                    $rowId = $rowData[static::ENTITY_ID_COLUMN];
                    $rows[] = $rowId;
                }

                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                }
            }
        }

        if ($rows) {
            return $this->deleteEntityFinish(array_unique($rows));
        }

        return false;
    }

    /**
    * Save and replace entities
    *
    * @return void
    */
    private function saveAndReplaceEntity()
    {
        $behavior = $this->getBehavior();
        $rows = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityList = [];

            foreach ($bunch as $rowNum => $row) {
                if (!$this->validateRow($row, $rowNum)) {
                    continue;
                }

                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);

                    continue;
                }

                $rowId = $row[static::ENTITY_ID_COLUMN];
                $rows[] = $rowId;
                $columnValues = [];

                foreach ($this->getAvailableColumns() as $columnKey) {
                    $columnValues[$columnKey] = $row[$columnKey];
                }

                $entityList[$rowId][] = $columnValues;
                $this->countItemsCreated += (int) !isset($row[static::ENTITY_ID_COLUMN]);
                $this->countItemsUpdated += (int) isset($row[static::ENTITY_ID_COLUMN]);
            }

            if (Import::BEHAVIOR_REPLACE === $behavior) {
                if ($rows && $this->deleteEntityFinish(array_unique($rows))) {
                    $this->saveEntityFinish($entityList);
                }
            } elseif (Import::BEHAVIOR_APPEND === $behavior) {
                $this->saveEntityFinish($entityList);
            }
        }
    }

    /**
    * Save entities
    *
    * @param array $entityData
    *
    * @return bool
    */
    private function saveEntityFinish(array $entityData): bool
    {
        if ($entityData) {
            $tableName = $this->connection->getTableName(static::TABLE);
            $rows = [];

            foreach ($entityData as $entityRows) {
                foreach ($entityRows as $row) {
                    $rows[] = $row;
                }
            }

            if ($rows) {
                $this->connection->insertOnDuplicate($tableName, $rows, $this->getAvailableColumns());

                return true;
            }

            return false;
        }
    }

    /**
    * Delete entities
    *
    * @param array $entityIds
    *
    * @return bool
    */
    private function deleteEntityFinish(array $entityIds): bool
    {
        if ($entityIds) {
            try {
                $this->countItemsDeleted += $this->connection->delete(
                    $this->connection->getTableName(static::TABLE),
                    $this->connection->quoteInto(static::ENTITY_ID_COLUMN . ' IN (?)', $entityIds)
                );

                return true;
            } catch (Exception $e) {
                return false;
            }
        }

        return false;
    }

    /**
    * Get available columns
    *
    * @return array
    */
    private function getAvailableColumns(): array
    {
        return $this->validColumnNames;
    }
}
