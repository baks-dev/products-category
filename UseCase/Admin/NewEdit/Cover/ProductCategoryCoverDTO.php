<?php
/*
 *  Copyright 2022.  Baks.dev <admin@baks.dev>
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *   limitations under the License.
 *
 */

namespace BaksDev\Products\Category\UseCase\Admin\NewEdit\Cover;

use BaksDev\Products\Category\Entity\Cover\ProductCategoryCoverInterface;
use BaksDev\Products\Category\Type\Event\CategoryEvent;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

final class ProductCategoryCoverDTO implements ProductCategoryCoverInterface
{
    /** Обложка категории */
    #[Assert\File(
      maxSize         : '2048k',
      mimeTypes       : [
        'image/png',
        'image/gif',
        'image/jpeg',
        'image/pjpeg',
        'image/webp',
      ],
      mimeTypesMessage: 'Please upload a valid file'
    )]
    public ?File $file = null;
    
    
    
    private ?string $name = null;
    
    private ?string $ext = null;
    
    private bool $cdn = false;
    
    private ?CategoryEvent $dir = null;
    
    
    /**
     * @return string|null
     */
    public function getName() : ?string
    {
        return $this->name;
    }
    
    /**
     * @param string|null $name
     */
    public function setName(?string $name) : void
    {
        $this->name = $name;
    }
    
    /**
     * @return string|null
     */
    public function getExt() : ?string
    {
        return $this->ext;
    }
    
    /**
     * @param string|null $ext
     */
    public function setExt(?string $ext) : void
    {
        $this->ext = $ext;
    }
    
    /**
     * @return bool
     */
    public function isCdn() : bool
    {
        return $this->cdn;
    }
    
    /**
     * @param bool $cdn
     */
    public function setCdn(bool $cdn) : void
    {
        $this->cdn = $cdn;
    }
    
    /**
     * @return CategoryEvent|null
     */
    public function getDir() : ?CategoryEvent
    {
        return $this->dir;
    }

    public function setDir(CategoryEvent $dir) : void
    {
        $this->dir = $dir;
    }
    
    
    

}

