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

namespace App\Module\Products\Category\UseCase\Admin\NewEdit\Offers\Trans;


use App\Module\Products\Category\Entity\Offers\Offers;
use App\Module\Products\Category\Entity\Offers\Trans\TransInterface;
use App\Module\Products\Category\Entity\Section\Field\Field;
use App\Module\Products\Category\Type\Offers\Id\OffersUid;
use App\System\Type\Locale\Locale;
use Symfony\Component\Validator\Constraints as Assert;

final class OffersTransDTO implements TransInterface
{
    private ?OffersUid $offer = null;
    
    private ?Field $field = null;
    
    private ?Locale $local;
    
    /** Название торгового предложения (строка с точкой, нижнее подчеркивание тире процент скобки) */
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^[\w \.\,\_\-\(\)\%]+$/iu')]
    private ?string $name = null;
    
    /** Краткое описание (строка с точкой, нижнее подчеркивание тире процент скобки) */
    #[Assert\Regex(pattern: '/^[\w \.\,\_\-\(\)\%]+$/iu')]
    private ?string $description = null;
    
    /**
     * @return ?Offers
     */
    public function getEquals() : ?OffersUid
    {
        return $this->offer;
    }
    
    /**
     * @param Offers $offer
     */
    public function setOffer(Offers $offer) : void
    {
        $this->offer = $offer->getId();
    }
    
    
    
    
    /**
     * @return Field|null
     */
    public function getField() : ?Field
    {
        return $this->field;
    }
    

    public function setField(Field $field) : void
    {
        $this->field = $field;
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
    public function getDescription() : ?string
    {
        return $this->description;
    }
    
    /**
     * @param string|null $description
     */
    public function setDescription(?string $description) : void
    {
        $this->description = $description;
    }
    

    
    
//    public function __construct(Locale $local = null, string $name = null, ?string $description = null)
//    {
//        $this->local = $local;
//        $this->name = $name;
//        $this->description = $description;
//    }
    
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
//        return $this->locale;
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

