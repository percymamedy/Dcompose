<?php

namespace App\Support\Artifacts;

interface Artifact
{
    /**
     * Create a new instance of the Artifact.
     *
     * @param array $args
     *
     * @return Artifact
     */
    public static function make(...$args): Artifact;

    /**
     * Saves the artifact and return true
     * if saved was successful.
     *
     * @return bool
     */
    public function save(): bool;
}
