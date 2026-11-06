<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class AddItemRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 100)]
        public readonly string $sku,

        #[Assert\NotNull]
        #[Assert\Positive]
        public readonly int $quantity,
    ) {
    }
}
