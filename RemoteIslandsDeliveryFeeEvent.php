<?php

/*
 * This file is part of the RemoteIslandsDeliveryFee
 *
 * Copyright (C) 2022 k.shibata
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\RemoteIslandsDeliveryFee;

use Eccube\Application;
use Eccube\Event\EventArgs;
use Eccube\Event\TemplateEvent;

class RemoteIslandsDeliveryFeeEvent
{

    /** @var  \Eccube\Application $app */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    // public function onFrontShoppingIndexInitialize(EventArgs $event)
    // {
    //     $app = $this->app;
    //     $Order = $event->getArgument('Order');
    //     $BaseInfo = $app['eccube.repository.base_info']->get();

    //     $Shippings = $Order->getShippings(); // 送り先取得
    //     $Shipping = $Shippings[0];
    //     $Address = $Shipping->getAddr01(); // 送り先住所
    //     $PrefId = $Shipping->getPref()->getId(); // 県のid取得
    //     $subTotal = $Order->getSubTotal();
    //     $DeliveryFee = $Shipping->getDeliveryFee()->getFee();

    //     $RemoteIslands = $app['plugin.remote_islands_delivery_fee.repository.remote_islands_delivery_fee']->getPref($PrefId);

    //     $d_fee = $Order->getDeliveryFeeTotal();
    //     $deliveryFreeAmount = $BaseInfo->getDeliveryFreeAmount();
    //     if($subTotal > $deliveryFreeAmount){
    //         $d_fee = 0;
    //     }else{
    //         $d_fee = $DeliveryFee;
    //     }
    //     if($RemoteIslands){
    //         foreach($RemoteIslands as $RemoteIsland){
    //             $RemoteIslandAddr = $RemoteIsland->getAddress();
    //             $Address2 = strpos($Address, $RemoteIslandAddr);
    //             if($Address2 !== false){
    //                 $shipping= $RemoteIsland->getValue();

    //                 $Order->setDeliveryFeeTotal($shipping + $d_fee);
    //                 $total = $Order->getTotal(); 
    //                 $Order->setTotal($total + $shipping + $d_fee);
    //                 $Order->setPaymentTotal($total + $shipping + $d_fee);
    //                 $app['orm.em']->persist($Order);
    //                 $app['orm.em']->flush($Order);
    //             }
    //         }
    //     }

    // }
    public function onFrontShoppingDeliveryInitialize(EventArgs $event){
        $app = $this->app;
        $Order = $event->getArgument('Order');
        $Shippings = $Order->getShippings(); // 送り先取得
        $Shipping = $Shippings[0];
        $Address = $Shipping->getAddr01(); // 送り先住所
        $PrefId = $Shipping->getPref()->getId(); // 県のid取得
        $d_fee = $Order->getDeliveryFeeTotal();

        $RemoteIslands = $app['plugin.remote_islands_delivery_fee.repository.remote_islands_delivery_fee']->getPref($PrefId);

        if($RemoteIslands){
            foreach($RemoteIslands as $RemoteIsland){
                $RemoteIslandAddr = $RemoteIsland->getAddress();
                $Address2 = strpos($Address, $RemoteIslandAddr);
                if($Address2 !== false){
                    $shipping= $RemoteIsland->getValue();
                    $Order->setDeliveryFeeTotal($shipping + $d_fee);
                    $this->app['orm.em']->persist($Order);
                    $this->app['orm.em']->flush($Order);
                }
            }
        }
    }

    public function onRenderRemoteIslands(TemplateEvent $event)
    {
        $app = $this->app;
        $parameters = $event->getParameters();
        $Order = $parameters['Order'];
        $Shippings = $Order->getShippings(); // 送り先取得
        $Shipping = $Shippings[0];
        $Address = $Shipping->getAddr01(); // 送り先住所
        $PrefId = $Shipping->getPref()->getId(); // 県のid取得

        $RemoteIslands = $app['plugin.remote_islands_delivery_fee.repository.remote_islands_delivery_fee']->getPref($PrefId);
        // 県が該当しない場合、レンダリングしない
        if (!$RemoteIslands) {
            return;
        }else{
            foreach($RemoteIslands as $RemoteIsland){
                $RemoteIslandAddr = $RemoteIsland->getAddress();
                $Address2 = strpos($Address, $RemoteIslandAddr);
                if($Address2 !== false){
                    $snipet = '<dt>送料　<span style="color: #c00000; font-size: 11px;">※離島のため追加が発生しております。</span></dt>';
                    $search = '<dt>送料</dt>';
                    $replace = $snipet;
                    $source = str_replace($search, $replace, $event->getSource());
                    $event->setSource($source);
                    $event->setParameters($parameters);
                }
            }
        }

        // twigコードにカテゴリコンテンツを挿入

        // twigパラメータにカテゴリコンテンツを追加
        // $parameters['CategoryContent'] = $CategoryContent;
    }
}
