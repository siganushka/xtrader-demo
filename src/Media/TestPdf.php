<?php

declare(strict_types=1);

namespace App\Media;

use Siganushka\MediaBundle\AbstractChannel;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Mapping\GenericMetadata;

class TestPdf extends AbstractChannel
{
    protected function loadConstraints(GenericMetadata $metadata): void
    {
        $constraint = new File();
        $constraint->maxSize = '2M';
        $constraint->mimeTypes = ['application/pdf', 'application/x-pdf'];

        $metadata->addConstraint($constraint);
    }
}
