<?php

namespace Config;

use CodeIgniter\Config\BaseService;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    /*
     * public static function example($getShared = true)
     * {
     *     if ($getShared) {
     *         return static::getSharedInstance('example');
     *     }
     *
     *     return new \CodeIgniter\Example();
     * }
     */

    /**
     * Prisreglerna för simulatortid (validerade mot Config\Swing).
     */
    public static function pricing(bool $getShared = true): \App\Libraries\Pricing
    {
        if ($getShared) {
            return static::getSharedInstance('pricing');
        }

        return new \App\Libraries\Pricing(config('Swing'));
    }

    /**
     * Kanalen som skickar beställningar vidare till swing-order.
     */
    public static function orderGateway(bool $getShared = true): \App\Libraries\Orders\OrderGateway
    {
        if ($getShared) {
            return static::getSharedInstance('orderGateway');
        }

        $config = config('Orders');

        return $config->channel === 'api'
            ? new \App\Libraries\Orders\ApiOrderGateway($config)
            : new \App\Libraries\Orders\MailOrderGateway($config);
    }

    public static function orderService(bool $getShared = true): \App\Libraries\Orders\OrderService
    {
        if ($getShared) {
            return static::getSharedInstance('orderService');
        }

        return new \App\Libraries\Orders\OrderService(
            model(\App\Models\OrderModel::class),
            static::orderGateway(),
            config('Orders'),
            config('Swing'),
        );
    }

    public static function inquiryService(bool $getShared = true): \App\Libraries\InquiryService
    {
        if ($getShared) {
            return static::getSharedInstance('inquiryService');
        }

        return new \App\Libraries\InquiryService(
            model(\App\Models\InquiryModel::class),
            config('Inquiries'),
            config('Swing'),
        );
    }

    public static function pageRenderer(bool $getShared = true): \App\Libraries\PageRenderer
    {
        if ($getShared) {
            return static::getSharedInstance('pageRenderer');
        }

        return new \App\Libraries\PageRenderer(config('Pages'));
    }

    public static function structuredData(bool $getShared = true): \App\Libraries\StructuredData
    {
        if ($getShared) {
            return static::getSharedInstance('structuredData');
        }

        return new \App\Libraries\StructuredData(config('Swing'), static::pricing());
    }

    public static function faq(bool $getShared = true): \App\Libraries\Faq
    {
        if ($getShared) {
            return static::getSharedInstance('faq');
        }

        return new \App\Libraries\Faq(config('Swing'), config('Booking'), static::pricing());
    }
}
