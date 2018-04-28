<?php

namespace Stratadox\TableLoader;

use Throwable;

/**
 * Notifies the client code that the table mapping could not be produced.
 *
 * @author Stratadox
 */
interface CannotMakeTableMapping extends Throwable
{
}
