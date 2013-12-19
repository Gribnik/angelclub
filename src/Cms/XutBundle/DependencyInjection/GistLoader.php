<?php

namespace Cms\XutBundle\DependencyInjection;

class GistLoader
{
    public function renderGist($gistId = NULL)
    {

        if (TRUE != is_null($gistId)) {
            return "Block $gistId is being rendered here";
        }
    }
}