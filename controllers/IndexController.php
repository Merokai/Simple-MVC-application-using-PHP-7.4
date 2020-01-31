<?php

namespace Controllers;

use Core\ViewLoader;

class IndexController
{
    public function getIndex()
    {
        ViewLoader::getInstance()->render(
            'Layout/Layout.html',
            []
        );
    }
}