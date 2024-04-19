<?php

declare(strict_types=1);

namespace yamaneyuta;

/**
 * @return string ULID format.
 */
function ulid(): string
{
    return (string)new Ulid();
}
