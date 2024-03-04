<?php

declare(strict_types=1);

namespace App\Media;

use Siganushka\MediaBundle\AbstractChannel;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Mapping\GenericMetadata;

class TestImg extends AbstractChannel
{
    protected function loadConstraints(GenericMetadata $metadata): void
    {
        $constraint = new Image();
        $constraint->maxSize = '2M';
        $constraint->mimeTypes = ['image/png'];

        $metadata->addConstraint($constraint);
    }
}
