<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */
namespace Custom\City\Controller\Adminhtml\City;

use Custom\City\Controller\Adminhtml\City;
use Magento\Framework\DB\Select;

class Importcities extends City
{
    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $isPost = $this->getRequest()->isPost();
        try{
            if ($isPost) {
                $file = $_FILES['import_city'];
                $data = $this->getRequest()->getParam('import_city');
                $this->_getSession()->setFormData($data);
                $country_id = $data['country_id'];
                if (!isset($file['tmp_name']['csv'])) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
                }
                $csvProcessor = $this->csv;
                $importProductRawData = $csvProcessor->getData($file['tmp_name']['csv']);
                $counter=1;
                $import = 0;
                $not_exists = '';
                $locales = [];
                $otherLocaleFound = true;
                foreach ($importProductRawData as $rowIndex => $dataRow) {
                    if (count($dataRow) > 2 && $rowIndex == 0) {
                        foreach ($dataRow as $key => $data) {
                            if($key == 0 || $key ==1){
                                continue;
                            }
                            $otherLocaleFound = true;
                            $locales[$key] = trim($data);
                        }
                    }
                    if(isset($dataRow[0]) && trim($dataRow[0])!='City' && $counter==1){
                        $this->messageManager->addError(__('Columns City is not exists in csv file.'));
                        $this->_redirect();
                        return;
                    }
                    if($counter > 1 && isset($dataRow[0]) && $dataRow[0]!=""){
                        $state_id = 0;
                        if(isset($dataRow[1]) && trim($dataRow[1])!=""){
                            $this->stateCollection->clear()->getSelect()->reset(Select::WHERE);
                            $_states = $this->stateCollection
                                ->addFieldToFilter('default_name',trim($dataRow[1]))
                                ->addFieldToFilter('country_id', $country_id)->load();
                            if($_states->count() > 0) {
                                $state_data = $_states->getFirstItem();
                                $state_id = $state_data->getRegionId();
                            }
                        }
                        $this->cityCollection->clear()->getSelect()->reset(Select::WHERE);
                        $city_check = $this->cityCollection;
                        $city_check = $city_check->addFieldToFilter('state_id',$state_id)->addFieldToFilter('country_id', $country_id)
                            ->addFieldToFilter('city', trim($dataRow[0]));
                        if($city_check->count() == 0){
                            $data = array('city'=>trim($dataRow[0]),'state_id'=>$state_id,'country_id'=>$country_id,'status'=>1,'created_at'=>date('Y-m-d'));
                            $cityModel = $this->_cityFactory->create();
                            $cityModel->setData($data);
                            try {
                                // Save city
                                $cityModel->save();
                                $cityId = $cityModel->getId();
                                if ($otherLocaleFound) {
                                    foreach ($dataRow as $key => $data) {
                                        $locale = null;
                                        if ($key == 0){
                                            $locale = 'en_US';
                                        }
                                        if($key ==1){
                                            continue;
                                        }
                                        $locale = ($locale === null) ? $locales[$key] : $locale;
                                        if ($data !== null && $locale!== null) {
                                            $translationData = ['locale' => $locale, 'name' => $data, 'city_id' => $cityId];
                                            $cityNameModel = $this->cityNameFactory->create();
                                            $collection = $cityNameModel->getCollection()
                                                ->addFieldToFilter('city_id', $cityId)
                                                ->addFieldToFilter('locale', $locale);
                                            if($collection->getSize() > 0) {
                                                $collection = $collection->getFirstItem();
                                                $cityNameModel->load($collection->getId());
                                                $cityNameModel->setName($data);
                                            }else{
                                                $cityNameModel->setData($translationData);
                                            }
                                            try {
                                                // Save city names
                                                $cityNameModel->save();
                                            } catch (\Exception $e) {
                                                continue;
                                            }
                                        }
                                    }
                                }
                                $import++;
                            }catch (\Exception $e) {
                                $this->messageManager->addError($e->getMessage());
                            }
                        }else{
                            $not_exists.='<br />'.__('City').' <b>"'.$dataRow[0].'"</b> '.__('is already exists.');
                        }
                    }
                    $counter++;
                }
                if($import > 0){
                    $this->messageManager->addSuccess(__('Cities imported successfully.').$not_exists);
                }else{
                    $this->messageManager->addError(__('No city imported, either already exists or data is not correct, check your file.').$not_exists);
                }

            }
        }catch(\Exception $e){
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('*/*/import');
    }

}
