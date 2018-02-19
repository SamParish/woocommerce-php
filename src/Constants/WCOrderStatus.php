<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 14/07/2016
 * Time: 10:21
 */

namespace SamParish\WooCommerce\Constants;


abstract class WCOrderStatus
{
    const PENDING = 'pending';
    const PROCESSING = 'processing';
    const ON_HOLD = 'on-hold';
    const COMPLETED = 'completed';
    const CANCELLED = 'cancelled';
    const REFUNDED = 'refunded';
    const FAILED = 'failed';
}