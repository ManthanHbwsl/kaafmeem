<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Portfolio\Model\ResourceModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Stdlib\DateTime\DateTime;
use MGS\Portfolio\Model\Portfolio as ModelPortfolio;

class Portfolio extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	protected $_filesystem;

    /**
     * File Uploader factory
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;
	
	/**
     * @var \Magento\Framework\Stdlib\DateTime
     */
	protected $_date;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
		\Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        parent::__construct($context);
		$this->_date = $date;
        $this->_filesystem = $filesystem;
        $this->_fileUploaderFactory = $fileUploaderFactory;
    }
	
    /**
     * Initialize connection and table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mgs_portfolio_items', 'portfolio_id');
    }
	
	protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->isObjectNew()) {
            $object->setCreatedTime($this->_date->gmtDate());
        }

        $object->setUpdateTime($this->_date->gmtDate());

        return parent::_beforeSave($object);
    }
	
	/**
     * Perform operations after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
			$portfolioId = $object->getId();
            $categories = $this->lookupCategoryIds($portfolioId);
            $object->setData('category_id', $categories);
            $object->setData('categories', $categories);
			
			$storeIds = $this->lookupStoreIds($portfolioId);
            $object->setData('store_id', $storeIds);
            $object->setData('stores', $storeIds);
        }

        return parent::_afterLoad($object);
    }
	
	
	protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
		$connection = $this->getConnection();
        if(isset($_FILES['thumbnail_image']['name']) && $_FILES['thumbnail_image']['name'] != '') {
			try {
				$uploader = $this->_fileUploaderFactory->create(['fileId' => 'thumbnail_image']);
				$uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
				$uploader->setAllowRenameFiles(true);
				$uploader->setFilesDispersion(true);
				
			} catch (\Exception $e) {
				return $this;
			}
			$path = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('mgs/portfolio/thumbnail/');
			$uploader->save($path);
			$fileName = $uploader->getUploadedFileName();
			if ($fileName) {
				$object->setData('thumbnail_image', $fileName);
				$object->save();
			}
		}
		
		$oldCategories = $this->lookupCategoryIds($object->getId());
        $newCategories = (array)$object->getCategories();
		
		$table = $this->getTable('mgs_portfolio_category_items');
        $insert = array_diff($newCategories, $oldCategories);
        $delete = array_diff($oldCategories, $newCategories);

        if ($delete) {
            $where = ['portfolio_id = ?' => (int)$object->getId(), 'category_id IN (?)' => $delete];

            $connection->delete($table, $where);
        }

        if ($insert) {
            $data = [];

            foreach ($insert as $storeId) {
                $data[] = ['portfolio_id' => (int)$object->getId(), 'category_id' => (int)$storeId];
            }

            $connection->insertMultiple($table, $data);
        }
		if(!count($newCategories)){
			$datass[] = ['portfolio_id' => (int)$object->getId(), 'category_id' => 0];
			$connection->insertMultiple($table, $datass);
		}
        
        $connection->delete($this->getTable('stores_portfolio'), ['portfolio_id = ?' => $object->getId()]);
		
		$portfolio = $object->getStoreIds();
		if (!is_array($portfolio)) {
            $portfolio = [];
        }
		
		
		foreach ($portfolio as $stores) {
            $data = [];
            $data['store_id'] = $stores;
            $data['portfolio_id'] = $object->getId();
            $connection->insert($this->getTable('stores_portfolio'), $data);
        }
		return $this;
		
		return parent::_afterSave($object);
    }
	
	/**
     * Process block data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['portfolio_id = ?' => (int)$object->getId()];

        $this->getConnection()->delete($this->getTable('mgs_portfolio_category_items'), $condition);
        $this->getConnection()->delete($this->getTable('stores_portfolio'), $condition);

        return parent::_beforeDelete($object);
    }
	
	/**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupCategoryIds($id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('mgs_portfolio_category_items'),
            'category_id'
        )->where(
            'portfolio_id = :portfolio_id'
        );

        $binds = [':portfolio_id' => (int)$id];

        return $connection->fetchCol($select, $binds);
    }
	/**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('stores_portfolio'),
            'store_id'
        )->where(
            'portfolio_id = :portfolio_id'
        );

        $binds = [':portfolio_id' => (int)$id];

        return $connection->fetchCol($select, $binds);
    }
	
	public function joinFilter($collection, $categoryId){
		$tableName = $this->getTable('mgs_portfolio_category_items');
		$collection->getSelect()->joinLeft(array('cat_items'=>$tableName), 'main_table.portfolio_id =cat_items.portfolio_id', array(''))->where('(cat_items.category_id = '.$categoryId.')');
		return $collection;
	}
	
	public function getRelatedPortfolio($portfolios, $id, $catString){
		$storeTable = $this->getTable('mgs_portfolio_category_items');
		$portfolios->getSelect()->distinct()->joinLeft(array('store'=>$storeTable), 'main_table.portfolio_id = store.portfolio_id', array(''))
					->where('category_id in ('.$catString.')')
					->where('main_table.portfolio_id <> '.$id);
		return $portfolios;
	}
	
	public function filterByCategories($collection, $categories){
		$storeTable = $this->getTable('mgs_portfolio_category_items');
		$collection->getSelect()->distinct()->joinLeft(array('store'=>$storeTable), 'main_table.portfolio_id = store.portfolio_id', array(''))
				->where('category_id in ('.$categories.')');
		return $collection;
	}
	public function getStores(ModelPortfolio $object)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('stores_portfolio'),
            'store_id'
        )->where(
            'portfolio_id = :portfolio_id'
        );

        if (!($result = $connection->fetchCol($select, ['portfolio_id' => $object->getId()]))) {
            $result = [];
        }

        return $result;
    }
}
