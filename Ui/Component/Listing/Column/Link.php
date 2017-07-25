<?php 

namespace Experius\PageNotFound\Ui\Component\Listing\Column;

class Link extends \Magento\Ui\Component\Listing\Columns\Column
{

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
	
		
	public function prepareDataSource(array $dataSource)
    {
		if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if(!empty($item[$fieldName])){
                    $javascript = 'window.open(this.href,"_blank"); return false;';
                    $item[$fieldName] = "<a onclick='".$javascript."' href='".$item[$fieldName]."' target='_blank'>".$item[$fieldName]."</a>";
                }
            }
        }
              
        return $dataSource;
    }

} 
	