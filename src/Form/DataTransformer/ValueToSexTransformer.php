<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Form\DataTransformer;

use App\Entity\VO\Sex;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ValueToSexTransformer implements DataTransformerInterface
{
    private const INT_MAP = [
        0 => false,
        1 => true,
    ];

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }

        // try to cast to int
        $value = self::INT_MAP[$value] ?? $value;

        if (!\is_bool($value)) {
            throw new TransformationFailedException('Expected a boolean value.');
        }

        return new Sex($value);
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof Sex) {
            throw new TransformationFailedException('Expected a Sex object.');
        }

        return $value->toValue();
    }
}
