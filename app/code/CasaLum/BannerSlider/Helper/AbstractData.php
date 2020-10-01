<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace CasaLum\BannerSlider\Helper;

use Exception;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Backend\App\Config;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Area;

class AbstractData extends AbstractHelper {
    const CONFIG_MODULE_PATH = 'casalum';

     /**
     * @type array
     */
    protected $_data = [];

    /**
     * @type StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @type ObjectManagerInterface
     */
    protected $objectManager;


    /**
     * AbstractData constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager
    ) {
        $this->objectManager = $objectManager;
        $this->storeManager = $storeManager;

        parent::__construct($context);
    }

    /**
     * @param $ver
     * @param string $operator
     *
     * @return mixed
     */
    public function versionCompare($ver, $operator = '>=')
    {
        $productMetadata = $this->objectManager->get(ProductMetadataInterface::class);
        $version = $productMetadata->getVersion(); //will return the magento version

        return version_compare($version, $ver, $operator);
    }

    /**
     * @param $data
     *
     * @return string
     */
    public function serialize($data)
    {
        if ($this->versionCompare('2.2.0')) {
            return self::jsonEncode($data);
        }

        return $this->getSerializeClass()->serialize($data);
    }

    /**
     * @param $string
     *
     * @return mixed
     */
    public function unserialize($string)
    {
        if ($this->versionCompare('2.2.0')) {
            return self::jsonDecode($string);
        }

        return $this->getSerializeClass()->unserialize($string);
    }

    /**
     * Encode the mixed $valueToEncode into the JSON format
     *
     * @param mixed $valueToEncode
     *
     * @return string
     */
    public static function jsonEncode($valueToEncode)
    {
        try {
            $encodeValue = self::getJsonHelper()->jsonEncode($valueToEncode);
        } catch (Exception $e) {
            $encodeValue = '{}';
        }

        return $encodeValue;
    }

    /**
     * Decodes the given $encodedValue string which is
     * encoded in the JSON format
     *
     * @param string $encodedValue
     *
     * @return mixed
     */
    public static function jsonDecode($encodedValue)
    {
        try {
            $decodeValue = self::getJsonHelper()->jsonDecode($encodedValue);
        } catch (Exception $e) {
            $decodeValue = [];
        }

        return $decodeValue;
    }

    /**
     * @return JsonHelper|mixed
     */
    public static function getJsonHelper()
    {
        return ObjectManager::getInstance()->get(JsonHelper::class);
    }

}