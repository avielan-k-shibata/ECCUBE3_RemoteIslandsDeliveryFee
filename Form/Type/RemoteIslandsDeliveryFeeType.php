<?php

/*
 * This file is part of the RemoteIslandsDeliveryFee
 *
 * Copyright (C) 2022 k.shibata
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\RemoteIslandsDeliveryFee\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RemoteIslandsDeliveryFeeType extends AbstractType
{

    protected $app;

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'text', array(
                'label' => 'ID',
                'required' => true,
                
            ))
            ->add('pref', 'pref', array(
                'label' => '都道府県',
                'required' => true,
            ))
            ->add('address', 'text', array(
                'label' => '市町村',
                'required' => true,
            ))
            ->add('value', 'text', array(
                'label' => 'プラス料金',
                'required' => true,
            ));
    }

    public function getName()
    {
        return 'remoteislandsdeliveryfee';
    }

}
