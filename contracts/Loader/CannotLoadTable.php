<?php

namespace Stratadox\TableLoader\Loader;

use Throwable;

/**
 * Notifies the client code that the table could not be mapped.
 *
 * @author Stratadox
 */
interface CannotLoadTable extends Throwable
{
}
