<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateItemRequest
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Positive]
        public readonly int $quantity,
    ) {
    }
}
