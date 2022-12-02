<?php

/*
 * This file is part of the RemoteIslandsDeliveryFee
 *
 * Copyright (C) 2022 k.shibata
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\RemoteIslandsDeliveryFee\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Plugin\RemoteIslandsDeliveryFee\Entity\RemoteIslandsDeliveryFee;

class RemoteIslandsDeliveryFeeController
{

    /**
     * RemoteIslandsDeliveryFee画面
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {

        // add code...
        $RemoteIslands = $app['plugin.remote_islands_delivery_fee.repository.remote_islands_delivery_fee']->findAll();

        return $app->render('RemoteIslandsDeliveryFee/Resource/template/admin/index.twig', array(
            // add parameter...
            'RemoteIslands' => $RemoteIslands ,
        ));
    }
    public function edit(Application $app, Request $request, $id = null){
        $RemoteIsland = null;
        $em = $app['orm.em'];

        if(!$id){
            $RemoteIsland = new RemoteIslandsDeliveryFee();
            $lastId = $app['plugin.remote_islands_delivery_fee.repository.remote_islands_delivery_fee']->getMaxId();
            if(!$lastId){
                $newId = 1;
            }else{
                $newId = $lastId[0]->getId()+1 ;
            }
            $RemoteIsland -> setId($newId);
        }else{
            $RemoteIsland = $app['plugin.remote_islands_delivery_fee.repository.remote_islands_delivery_fee']->find($id);
        }
        $form = $app['form.factory']->createBuilder('remoteislandsdeliveryfee', $RemoteIsland)->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {

            $data = $form->getData();
            $em->persist($data);
            $em->flush($data);
            $app->addSuccess('登録完了しました。', 'admin');
            }
            else{
                $app->addError('登録できませんでした。', 'admin');

            }
            return $app->redirect($app->url('plugin_RemoteIslandsDeliveryFee_list'));

        }
        return $app->render('RemoteIslandsDeliveryFee/Resource/template/admin/edit.twig', array(
            // add parameter...
            'form' => $form->createView(),
            'RemoteIslands' => $RemoteIsland,
        ));
    }

    public function delete(Application $app, Request $request, $id)
    {
        $em = $app['orm.em'];
        $RemoteIsland = $app['plugin.remote_islands_delivery_fee.repository.remote_islands_delivery_fee']->find($id);
        if (!$RemoteIsland) {
            $app->addError('admin.plugin.coupon.notfound', 'admin');

            return $app->redirect($app->url('plugin_RemoteIslandsDeliveryFee_list'));
        } else {
            // 離島情報を削除する

            // $app->addSuccess('admin.plugin.RemoteIslandsDeliveryFee.delete.success', 'admin');
            $app->addSuccess('離島追加料金を削除しました。', 'admin');
            log_info('Delete a remoteIslands with ', array('ID' => $id));
            $em->remove($RemoteIsland);
            $em->flush($RemoteIsland);
        }

        return $app->redirect($app->url('plugin_RemoteIslandsDeliveryFee_list'));
    }
}
