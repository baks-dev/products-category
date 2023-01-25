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

namespace BaksDev\Products\Category\UseCase\Admin\NewEdit\Landing;


use BaksDev\Products\Category\Entity\Event\Event;
use BaksDev\Products\Category\Entity\Landing\LandingInterface;
use BaksDev\Products\Category\Type\Event\CategoryEvent;
use BaksDev\Core\Type\Locale\Locale;

final class LandingCollectionDTO implements LandingInterface
{
    
    private ?CategoryEvent $event = null;
    
    /**
     * @var Locale
     */
    private Locale $local;
    
    /** Верхний посадочный блок */
    private ?string $header = null;
    
    /**
     * Нижний посадочный блок
     * @var string|null
     */
    private ?string $bottom = null;
    

    public function getEquals() : ?CategoryEvent
    {
        return $this->event;
    }
    
    /**
     * @param Event $event
     */
    public function setEvent(Event $event) : void
    {
        $this->event = $event->getId();
    }
    
    
    /**
     * @return Locale
     */
    public function getLocal() : Locale
    {
        return $this->local;
    }
    
    
    /**
     * @param string|Locale $local
     */
    public function setLocal(string|Locale $local) : void
    {
        $this->local = $local instanceof Locale ? $local : new Locale($local) ;
    }


    public function getHeader() : ?string
    {
        return $this->header;
    }
    

    public function setHeader(?string $header) : void
    {
        $this->header = $header;
    }
    
    /**
     * @return string|null
     */
    public function getBottom() : ?string
    {
        return $this->bottom;
    }
    
    /**
     * @param string|null $bottom
     */
    public function setBottom(?string $bottom) : void
    {
        $this->bottom = $bottom;
    }
    
    
    
    
    
    
    
//
//    public function getLocal() : Locale
//    {
//        return $this->locale;
//    }
//
//    /**
//     * @return string
//     */
//    public function getHeader() : ?string
//    {
//        return $this->header;
//    }
//
//    /**
//     * @return string|null
//     */
//    public function getBottom() : ?string
//    {
//        return $this->bottom;
//    }
//
//

}

