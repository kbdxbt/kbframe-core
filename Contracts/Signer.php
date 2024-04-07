<?php

namespace Modules\Core\Contracts;

interface Signer
{
    public function sign(array $payload): string;

    public function validate(string $signature, array $payload): bool;
}
