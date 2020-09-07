<?php
/**
 * This file is part of OXID Maxpay module.
 */

/**
 * Metadata version
 */
$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'maxpay',
    'title'        => 'MaxPay',
    'description'  => array(
        'de' => 'Modul für die Zahlung mit MaxPay.',
        'en' => 'Module for MaxPay payment.',
    ),
    'thumbnail'    => 'logo.png',
    'version'      => '1.0.0',
    'author'       => 'Dinarys GmbH',
    'url'          => 'https://dinarys.com',
    'email'        => 'admin@maxpay.com',
    'extend'       => array(
        \OxidEsales\Eshop\Core\ViewConfig::class                              => \Maxpay\MaxpayModule\Core\ViewConfig::class,
        \OxidEsales\Eshop\Application\Controller\PaymentController::class     => \Maxpay\MaxpayModule\Controller\PaymentController::class,
        \OxidEsales\Eshop\Application\Controller\ThankYouController::class    => \Maxpay\MaxpayModule\Controller\ThankYouController::class,
        \OxidEsales\Eshop\Application\Controller\FrontendController::class    => \Maxpay\MaxpayModule\Controller\FrontendController::class,
        \OxidEsales\Eshop\Application\Model\User::class                       => \Maxpay\MaxpayModule\Model\User::class,
        \OxidEsales\Eshop\Application\Model\Order::class                      => \Maxpay\MaxpayModule\Model\Order::class,
        \OxidEsales\Eshop\Application\Model\PaymentGateway::class             => \Maxpay\MaxpayModule\Model\PaymentGateway::class,
    ),
    'controllers' => array(
        'maxpaystandarddispatcher'        => \Maxpay\MaxpayModule\Controller\StandardDispatcher::class,
        'maxpayorder'                     => \Maxpay\MaxpayModule\Controller\FrontendController::class,
        'maxpayrefund_maxpay'              => \Maxpay\MaxpayModule\Controller\Admin\RefundController::class
    ),
    'events'       => array(
        'onActivate'   => '\Maxpay\MaxpayModule\Core\Events::onActivate',
        'onDeactivate' => '\Maxpay\MaxpayModule\Core\Events::onDeactivate'
    ),
    'templates' => array(
        'order_maxpay.tpl' => 'maxpay/views/admin/tpl/order_maxpay.tpl',
        'refund_success.tpl' => 'maxpay/views/admin/tpl/refund_success.tpl',
        'refund_failed.tpl' => 'maxpay/views/admin/tpl/refund_failed.tpl',
        'order_history.tpl' => 'maxpay/views/tpl/page/account/order_history.tpl',
        'postback.tpl' => 'maxpay/views/tpl/postback.tpl',
    ),
    'blocks' => array(
        array('template' => 'page/checkout/payment.tpl',          'block'=>'select_payment',                        'file'=>'/views/tpl/maxpaypaymentselector.tpl'),
        array('template' => 'page/checkout/thankyou.tpl',         'block'=>'checkout_thankyou_proceed',             'file'=>'/views/tpl/thankyou.tpl'),
        array('template' => 'page/account/order.tpl',             'block'=>'account_order_history',                 'file'=>'/views/tpl/page/account/order.tpl'),
     ),
    'settings' => array(
        array('group' => 'maxpay_settings',         'name' => 'sMaxpayPublickKey',              'type' => 'str',      'value' => ''),
        array('group' => 'maxpay_settings',         'name' => 'sMaxpayPrivateKey',              'type' => 'str',      'value' => ''),
        array('group' => 'maxpay_development', 'name' => 'blMaxpaySandboxMode',             'type' => 'bool',     'value' => 'false'),
        array('group' => 'maxpay_development', 'name' => 'sMaxpayTestPublicKey',            'type' => 'str',      'value' => ''),
        array('group' => 'maxpay_development', 'name' => 'sMaxpayTestPrivateKey',           'type' => 'str',      'value' => ''),
    )
);
