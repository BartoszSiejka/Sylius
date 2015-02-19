<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\ImportExport\Factory;

use Doctrine\Common\Collections\ArrayCollection;

interface ArrayIteratorFactoryInterface 
{
    public function createIteratorFromArray(array $array);
    
    public function createIteratorFromArrayCollection(ArrayCollection $arrayCollection);
}