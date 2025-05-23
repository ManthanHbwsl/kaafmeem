<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * System config Logo image field backend model
 */
namespace MGS\ThemeSettings\Model\Config\Backend;

class Font extends \Magento\Config\Model\Config\Backend\File
{
    /**
     * The tail part of directory path for uploading
     *
     */
    const UPLOAD_DIR = 'fonts';

    /**
     * Return path to directory for upload file
     *
     * @return string
     * @throw \Magento\Framework\Exception\LocalizedException
     */
    protected function _getUploadDir()
    {
        return $this->_mediaDirectory->getAbsolutePath($this->_appendScopeInfo(self::UPLOAD_DIR));
    }

    /**
     * Makes a decision about whether to add info about the scope.
     *
     * @return boolean
     */
    protected function _addWhetherScopeInfo()
    {
        return true;
    }
}
