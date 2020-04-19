<?php

namespace App\Support\Artifacts;

abstract class AbstractArtifact implements Artifact
{
    /**
     * Create a new instance of the Artifact.
     *
     * @param  array  $args
     *
     * @return Artifact
     */
    public static function make(...$args): Artifact
    {
        return new static(...$args);
    }
}
