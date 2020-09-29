<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace CasaLum\BannerSlider\Block\Adminhtml\Slider\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/*
* Class DeleteButton
*/
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
   /**
    * @inheritDoc
    */
   public function getButtonData()
   {
       $data = [];
       if ($this->getSliderId()) {
           $data = [
               'label' => __('Delete Slider'),
               'class' => 'delete',
               'on_click' => 'deleteConfirm(\'' . __(
                   'Are you sure you want to do this?'
               ) . '\', \'' . $this->getDeleteUrl() . '\', {"data": {}})',
               'sort_order' => 20,
           ];
       }
       return $data;
   }

   /**
    * URL to send delete requests to.
    *
    * @return string
    */
   public function getDeleteUrl()
   {
       return $this->getUrl('*/*/delete', ['banner_id' => $this->getSliderId()]);
   }
}