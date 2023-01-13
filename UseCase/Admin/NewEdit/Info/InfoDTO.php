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

namespace App\Module\Products\Category\UseCase\Admin\NewEdit\Info;

use App\Module\Products\Category\Entity\Info\InfoInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class InfoDTO implements InfoInterface
{
    /* INFO */
    
    /** Семантическая ссылка на раздел (строка с тире и нижним подчеркиванием) */
    #[Assert\NotBlank]
    #[Assert\Regex(
      pattern: '/^[a-z0-9\_\-]+$/i'
    )]
    private ?string $url = null;
    
    /** Статус активности раздела
     * @var bool
     */
    private bool $active = false;
    
    
    
    /**
     * @return string|null
     */
    public function getUrl() : ?string
    {
        return $this->url;
    }
    
    public function setUrl(string $url) : void
    {
        $this->url = $url;
    }
    
    /**
     * @return bool
     */
    public function isActive() : bool
    {
        return $this->active;
    }
    
    /**
     * @param bool $active
     */
    public function setActive(bool $active) : void
    {
        $this->active = $active;
    }
    
    
    
    
//    /**
//     * @param string|null $url
//     * @param bool $active
//     */
//    public function __construct(?string $url, bool $active = false)
//    {
//        $this->url = $url;
//        $this->active = $active;
//    }



    
}

