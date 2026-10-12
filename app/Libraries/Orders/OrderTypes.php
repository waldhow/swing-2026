<?php

namespace App\Libraries\Orders;

final class OrderTypes
{
    /** Ordertyp → värdet swing-order förväntar sig på type:-raden i mejlet. */
    public const MAIL_TYPES = [
        'SPELPOTT'    => 'Spelpott',
        'PRESENTKORT' => 'Presentkort',
        'FRISKVARD'   => 'Friskvård',
    ];
}
