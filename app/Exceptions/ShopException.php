<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * A business-rule failure whose message is safe to show to the customer / admin
 * (e.g. "Only 3 left in stock").
 */
class ShopException extends RuntimeException
{
}
