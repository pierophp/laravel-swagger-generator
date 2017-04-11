<?php

namespace LaravelSwaggerGenerator\Mapping;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 */
class Get extends Annotation
{   public $path;
    public $summary;
    public $description;
    public $operationId;
    public $consumes;
    public $produces;
}