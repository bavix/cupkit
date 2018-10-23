<?php

/**
 * Corundum Upload (Cup)
 *
 * @author Babichev Maxim <info@babichev.net>
 * @version 1.0.0
 */

namespace Bavix\CupKit;

use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Polyfill\NotSupportingVisibilityTrait;

class CupAdapter extends AbstractAdapter
{

    use NotSupportingVisibilityTrait;

    /**
     * @inheritdoc
     */
    public function getMetadata($path)
    {

    }

    /**
     * @inheritdoc
     */
    public function getTimestamp($path)
    {
        return $this->getMetadata($path);
    }

}
