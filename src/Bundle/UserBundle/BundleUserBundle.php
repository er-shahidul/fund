<?php

namespace Bundle\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class BundleUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
