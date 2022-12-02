<?php
/*
* Plugin Name : RemoteIslandsDeliveryFee
*
* Copyright (C) 2007 avielan Co., Ltd. All Rights Reserved.
* http://www.avielan.co.jp/
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Plugin\RemoteIslandsDeliveryFee\Service;

use Eccube\Application;

use Doctrine\ORM\EntityManager;
use Eccube\Common\Constant;
use Eccube\Entity\Order;
use Eccube\Entity\Shipping;
use Eccube\Entity\Delivery;

class ShoppingService extends \Eccube\Service\ShoppingService
{

    public function getShippingDeliveryFeeTotal($shippings)
    {
        $app = $this->app;

        $deliveryFeeTotal = 0;
        foreach ($shippings as $Shipping) {
            $deliveryFeeTotal += $Shipping->getShippingDeliveryFee();
        }
        $Shipping = $shippings[0];
        $Address = $Shipping->getAddr01(); // 送り先住所
        $PrefId = $Shipping->getPref()->getId(); // 県のid取得
        $RemoteIslands = $app['plugin.remote_islands_delivery_fee.repository.remote_islands_delivery_fee']->getPref($PrefId);
        if($RemoteIslands){
            foreach($RemoteIslands as $RemoteIsland){
                $RemoteIslandAddr = $RemoteIsland->getAddress();
                $Address2 = strpos($Address, $RemoteIslandAddr);
                if($Address2 !== false){
                    $shipping= $RemoteIsland->getValue();
                    $deliveryFeeTotal = $deliveryFeeTotal +$shipping;
                }
            }
        }
        dump($deliveryFeeTotal);
        return $deliveryFeeTotal;

    }

    public function setDeliveryFreeAmount(Order $Order)
    {
        // 配送料無料条件(合計金額)
        $app = $this->app;
        $Shippings = $Order->getShippings(); // 送り先取得
        $Shipping = $Shippings[0];
        $Address = $Shipping->getAddr01(); // 送り先住所
        $PrefId = $Shipping->getPref()->getId(); // 県のid取得
        $RemoteIslands = $app['plugin.remote_islands_delivery_fee.repository.remote_islands_delivery_fee']->getPref($PrefId);
        $deliveryFreeAmount = $this->BaseInfo->getDeliveryFreeAmount();
        dump($deliveryFreeAmount, $Order->getSubTotal());
        if (!is_null($deliveryFreeAmount)) {
            // 合計金額が設定金額以上であれば送料無料
            if ($Order->getSubTotal() >= $deliveryFreeAmount) {
                $Order->setDeliveryFeeTotal(0);
                if($RemoteIslands){
                    foreach($RemoteIslands as $RemoteIsland){
                        $RemoteIslandAddr = $RemoteIsland->getAddress();
                        $Address2 = strpos($Address, $RemoteIslandAddr);
                        if($Address2 !== false){
                            $shipping= $RemoteIsland->getValue();
                            $Order->setDeliveryFeeTotal($shipping);
                        }
                    }
                }
                $shippings = $Order->getShippings();
                dump(444);

                foreach ($shippings as $Shipping) {
                    $Shipping->setShippingDeliveryFee(0);
                }
            }
        }
    }
}
