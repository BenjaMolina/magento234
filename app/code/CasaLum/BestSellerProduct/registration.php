<?php
/* https://www.dckap.com/blog/how-to-display-bestseller-products-in-magento-2/ 
    https://magento.stackexchange.com/questions/201260/magento-2-insert-a-bestsellers-widget-via-the-magento-admin
    https://github.com/emizentech/magento2-best-seller
*/

\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'CasaLum_BestSellerProduct',
    __DIR__
);