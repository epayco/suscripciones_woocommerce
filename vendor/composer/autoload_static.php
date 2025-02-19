<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit29e4698e2f640ef461d5ff51b7c3
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'EpaycoSubscription\\Woocommerce\\' => 24,
            'EpaycoSdk\\Sdk\\' => 19,
            'Epayco\\' => 18,
        ),
        'W' =>
            array (
                'WpOrg\\Requests\\' => 15,
            ),
        'E' =>
            array (
                'Epayco\\' => 7,
            ),
    );

    public static $prefixDirsPsr4 = array (
        'EpaycoSubscription\\Woocommerce\\' =>
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'WpOrg\\Requests\\' =>
            array (
                0 => __DIR__ . '/..' . '/rmccue/requests/src',
            ),
        'EpaycoSubscription\\Sdk\\' =>
            array (
                0 => __DIR__ . '/../..' . '/packages/epayco/sdk/src',
        ),
        'Epayco\\' =>
            array (
                0 => __DIR__ . '/..' . '/epayco/epayco-php/src',
            ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'EpaycoSubscription\\Woocommerce\\Dependencies' => __DIR__ . '/../..' . '/src/Dependencies.php',
        'EpaycoSubscription\\Woocommerce\\Helpers' => __DIR__ . '/../..' . '/src/Helpers.php',
        'EpaycoSubscription\\Woocommerce\\Configs\\Store' => __DIR__ . '/../..' . '/src/Configs/Store.php',
        'EpaycoSubscription\\Woocommerce\\Blocks\\AbstractBlock' => __DIR__ . '/../..' . '/src/Blocks/AbstractBlock.php',
        'EpaycoSubscription\\Woocommerce\\Blocks\\SubscriptionBlock' => __DIR__ . '/../..' . '/src/Blocks/SubscriptionBlock.php',
        'EpaycoSubscription\\Woocommerce\\Gateways\\AbstractGateway' => __DIR__ . '/../..' . '/src/Gateways/AbstractGateway.php',
        'EpaycoSubscription\\Woocommerce\\Gateways\\EpaycoSuscription' => __DIR__ . '/../..' . '/src/Gateways/EpaycoSuscription.php',
        'EpaycoSubscription\\Woocommerce\\Helpers\\Paths' => __DIR__ . '/../..' . '/src/Helpers/Paths.php',
        'EpaycoSubscription\\Woocommerce\\Helpers\\Form' => __DIR__ . '/../..' . '/src/Helpers/Form.php',
        'EpaycoSubscription\\Woocommerce\\Helpers\\Session' => __DIR__ . '/../..' . '/src/Helpers/Session.php',
        'EpaycoSubscription\\Woocommerce\\Helpers\\Strings' => __DIR__ . '/../..' . '/src/Helpers/Strings.php',
        'EpaycoSubscription\\Woocommerce\\Helpers\\Gateways' => __DIR__ . '/../..' . '/src/Helpers/Gateways.php',
        'EpaycoSubscription\\Woocommerce\\Helpers\\Template' => __DIR__ . '/../..' . '/src/Helpers/Template.php',
        'EpaycoSubscription\\Woocommerce\\Helpers\\Url' => __DIR__ . '/../..' . '/src/Helpers/Url.php',
        'EpaycoSubscription\\Woocommerce\\Startup' => __DIR__ . '/../..' . '/src/Startup.php',
        'EpaycoSubscription\\Woocommerce\\Hooks' => __DIR__ . '/../..' . '/src/Hooks.php',
        'EpaycoSubscription\\Woocommerce\\Hooks\\Admin' => __DIR__ . '/../..' . '/src/Hooks/Admin.php',
        'EpaycoSubscription\\Woocommerce\\Hooks\\Blocks' => __DIR__ . '/../..' . '/src/Hooks/Blocks.php',
        'EpaycoSubscription\\Woocommerce\\Hooks\\Checkout' => __DIR__ . '/../..' . '/src/Hooks/Checkout.php',
        'EpaycoSubscription\\Woocommerce\\Hooks\\Endpoints' => __DIR__ . '/../..' . '/src/Hooks/Endpoints.php',
        'EpaycoSubscription\\Woocommerce\\Hooks\\Scripts' => __DIR__ . '/../..' . '/src/Hooks/Scripts.php',
        'EpaycoSubscription\\Woocommerce\\Hooks\\Gateway' => __DIR__ . '/../..' . '/src/Hooks/Gateway.php',
        'EpaycoSubscription\\Woocommerce\\Hooks\\Options' => __DIR__ . '/../..' . '/src/Hooks/Options.php',
        'EpaycoSubscription\\Woocommerce\\Hooks\\Plugin' => __DIR__ . '/../..' . '/src/Hooks/Plugin.php',
        'EpaycoSubscription\\Woocommerce\\Hooks\\Template' => __DIR__ . '/../..' . '/src/Hooks/Template.php',
        'EpaycoSubscription\\Woocommerce\\Interfaces\\EpaycoSubscriptionGatewayInterface' => __DIR__ . '/../..' . '/src/Interfaces/EpaycoSubscriptionGatewayInterface.php',
        'EpaycoSubscription\\Woocommerce\\Interfaces\\EpaycoSubscriptionPaymentBlockInterface' => __DIR__ . '/../..' . '/src/Interfaces/EpaycoSubscriptionPaymentBlockInterface.php',
        'EpaycoSubscription\\Woocommerce\\Funnel\\Funnel' => __DIR__ . '/../..' . '/src/Funnel/Funnel.php',
        'EpaycoSubscription\\Woocommerce\\Transactions\\SubscriptionTransaction' => __DIR__ . '/../..' . '/src/Transactions/SubscriptionTransaction.php',
        'EpaycoSubscription\\Woocommerce\\WoocommerceEpaycoSubscription' => __DIR__ . '/../..' . '/src/WoocommerceEpaycoSubscription.php',
        'Requests' => __DIR__ . '/..' . '/rmccue/requests/library/Requests.php',
        'Epayco' => __DIR__ . '/..' . '/epayco/epayco-php/src/Epayco.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit29e4698e2f640ef461d5ff51b7c3::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit29e4698e2f640ef461d5ff51b7c3::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit29e4698e2f640ef461d5ff51b7c3::$classMap;

        }, null, ClassLoader::class);
    }
}
