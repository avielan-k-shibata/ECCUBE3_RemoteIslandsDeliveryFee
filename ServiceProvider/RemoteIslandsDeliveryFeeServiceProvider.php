<?php

/*
 * This file is part of the RemoteIslandsDeliveryFee
 *
 * Copyright (C) 2022 k.shibata
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\RemoteIslandsDeliveryFee\ServiceProvider;

use Eccube\Common\Constant;
use Plugin\RemoteIslandsDeliveryFee\Form\Type\RemoteIslandsDeliveryFeeConfigType;
use Plugin\RemoteIslandsDeliveryFee\Form\Type\RemoteIslandsDeliveryFeeType;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class RemoteIslandsDeliveryFeeServiceProvider implements ServiceProviderInterface
{

    public function register(BaseApplication $app)
    {
        // 管理画面定義
        $admin = $app['controllers_factory'];
        // 強制SSL
        if ($app['config']['force_ssl'] == Constant::ENABLED) {
            $admin->requireHttps();
        }

        // プラグイン用設定画面
        $admin->match('/plugin/RemoteIslandsDeliveryFee/config', 'Plugin\RemoteIslandsDeliveryFee\Controller\ConfigController::index')->bind('plugin_RemoteIslandsDeliveryFee_config');

        // 離島配送設定画面
        $admin->match('/plugin/RemoteIslandsDeliveryFee/list', 'Plugin\RemoteIslandsDeliveryFee\Controller\RemoteIslandsDeliveryFeeController::index')->bind('plugin_RemoteIslandsDeliveryFee_list');

        $admin->match('/plugin/RemoteIslandsDeliveryFee/new', 'Plugin\RemoteIslandsDeliveryFee\Controller\RemoteIslandsDeliveryFeeController::edit')->value('id', null)->bind('plugin_RemoteIslandsDeliveryFee_new');

        // 離島配送の編集
        $admin->match('/plugin/RemoteIslandsDeliveryFee/{id}/edit', 'Plugin\RemoteIslandsDeliveryFee\Controller\RemoteIslandsDeliveryFeeController::edit')->value('id', null)->assert('id', '\d+|')->bind('plugin_RemoteIslandsDeliveryFee_edit');
        // 離島配送の削除
        $admin->delete('/plugin/RemoteIslandsDeliveryFee/{id}/delete', 'Plugin\RemoteIslandsDeliveryFee\Controller\RemoteIslandsDeliveryFeeController::delete')->value('id', null)->assert('id', '\d+|')->bind('plugin_RemoteIslandsDeliveryFee_delete');


        $app->mount('/'.trim($app['config']['admin_route'], '/').'/', $admin);

        // フロント画面定義
        $front = $app['controllers_factory'];
        // 強制SSL
        if ($app['config']['force_ssl'] == Constant::ENABLED) {
            $front->requireHttps();
        }

        // 独自コントローラ
        // $front->match('/plugin/remoteislandsdeliveryfee/hello', 'Plugin\RemoteIslandsDeliveryFee\Controller\RemoteIslandsDeliveryFeeController::index')->bind('plugin_RemoteIslandsDeliveryFee_hello');

        $app->mount('', $front);

        // Form
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new RemoteIslandsDeliveryFeeConfigType();
            $types[] = new RemoteIslandsDeliveryFeeType($app);

            return $types;
        }));

        // Repository
        $app['plugin.remote_islands_delivery_fee.repository.remote_islands_delivery_fee'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\RemoteIslandsDeliveryFee\Entity\RemoteIslandsDeliveryFee');
        });
        // Service
        $app['eccube.service.shopping'] = $app->share(function () use ($app) {
            return new \Plugin\RemoteIslandsDeliveryFee\Service\ShoppingService($app, $app['eccube.service.cart'], $app['eccube.service.order']);
        });

        // メッセージ登録
//       $file = __DIR__ . '/../Resource/locale/message.' . $app['locale'] . '.yml';
//       $app['translator']->addResource('yaml', $file, $app['locale']);

        // load config
        // プラグイン独自の定数はconfig.ymlの「const」パラメータに対して定義し、$app['remoteislandsdeliveryfeeconfig']['定数名']で利用可能
//       if (isset($app['config']['RemoteIslandsDeliveryFee']['const'])) {
//           $config = $app['config'];
//           $app['remoteislandsdeliveryfeeconfig'] = $app->share(function () use ($config) {
//               return $config['RemoteIslandsDeliveryFee']['const'];
//           });
//       }
            $app['config'] = $app->share($app->extend('config', function ($config) {
                $addNavi['id'] = 'plugin_RemoteIslandsDeliveryFee';
                $addNavi['name'] = '離島配送料金設定';
                $addNavi['url'] = 'plugin_RemoteIslandsDeliveryFee_list';

                $nav = $config['nav'];
                foreach ($nav as $key => $val) {
                    if ('setting' == $val['id']) {
                        $nav[$key]['child'][] = $addNavi;
                    }
                }
                $config['nav'] = $nav;

                return $config;
            }));
 
        // ログファイル設定
        $app['monolog.logger.remoteislandsdeliveryfee'] = $app->share(function ($app) {
            $config = array(
                'name' => 'remoteislandsdeliveryfee',
                'filename' => 'remoteislandsdeliveryfee',
                'delimiter' => '_',
                'dateformat' => 'Y-m-d',
                'log_level' => 'INFO',
                'action_level' => 'ERROR',
                'passthru_level' => 'INFO',
                'max_files' => '90',
                'log_dateformat' => 'Y-m-d H:i:s,u',
                'log_format' => '[%datetime%] %channel%.%level_name% [%session_id%] [%uid%] [%user_id%] [%class%:%function%:%line%] - %message% %context% %extra% [%method%, %url%, %ip%, %referrer%, %user_agent%]',
            );
            return $app['eccube.monolog.factory']($config);
        });

    }

    public function boot(BaseApplication $app)
    {
    }

}
