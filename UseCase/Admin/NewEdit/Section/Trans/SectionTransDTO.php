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

namespace BaksDev\Products\Category\UseCase\Admin\NewEdit\Section\Trans;


use BaksDev\Products\Category\Entity\Section\Section;
use BaksDev\Products\Category\Entity\Section\Trans\TransInterface;
use BaksDev\Products\Category\Type\Section\Id\SectionUid;
use BaksDev\Core\Type\Locale\Locale;
use Symfony\Component\Validator\Constraints as Assert;

final class SectionTransDTO implements TransInterface
{
    private ?SectionUid $section = null;
    
    private ?Locale $local;
    
    /** Название секции (строка с точкой, нижнее подчеркивание тире процент скобки) */
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^[\w \.\,\_\-\(\)\%]+$/iu')]
    private ?string $name = null;
    
    /** Краткое описание (строка с точкой, нижнее подчеркивание тире процент скобки) */
    #[Assert\Regex(pattern: '/^[\w \.\,\_\-\(\)\%]+$/iu')]
    private ?string $description = null;
    
    
    /**
     * @return Section|null
     */
    public function getEquals() : ?SectionUid
    {
        return $this->section;
    }
    
    /**
     * @param Section $section
     */
    public function setSection(Section $section) : void
    {
        $this->section = $section->getId();
    }
    
    /**
     * @return Locale|null
     */
    public function getLocal() : ?Locale
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
    

    
    
    
    
    /**
     * @param string|null $name
     */
    public function setName(?string $name) : void
    {
        $this->name = $name;
    }
    
    /**
     * @param string|null $description
     */
    public function setDescription(?string $description) : void
    {
        $this->description = $description;
    }
    

    
    /**
     * @return string|null
     */
    public function getName() : ?string
    {
        return $this->name;
    }
    
    /**
     * @return string|null
     */
    public function getDescription() : ?string
    {
        return $this->description;
    }
    

    
    
    
    
    
    
    
    
    
//    public function __construct(Locale $locale = null, string $name = null, string $description = null)
//    {
//        $this->local = $locale;
//        $this->name = $name;
//        $this->description = $description;
//    }
//
//    /**
//     * @param string|Locale $local
//     */
//    public function setLocal(string|Locale $local) : void
//    {
//        $this->local = $local instanceof Locale ? $local : new Locale($local) ;
//    }
//
//    public function getLocal() : Locale
//    {
//        return $this->local;
//    }
//
//    public function getName() : string
//    {
//        return $this->name;
//    }
//
//    public function getDesc() : ?string
//    {
//        return $this->desc;
//    }
    
}

