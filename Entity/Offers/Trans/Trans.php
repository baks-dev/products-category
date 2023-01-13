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

namespace App\Module\Products\Category\Entity\Offers\Trans;

use App\Module\Products\Category\Entity\Offers\Offers;
use App\System\Services\EntityEvent\EntityEvent;
use App\System\Type\Locale\Locale;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;

/* Перевод Торговых предложений */

#[ORM\Entity]
#[ORM\Table(name: 'product_category_offers_trans')]
class Trans extends EntityEvent
{
    public const TABLE = 'product_category_offers_trans';
    
    /** Связь на торговое предложение */
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Offers::class, cascade: ["remove", "persist"], inversedBy: "trans")]
    #[ORM\JoinColumn(name: 'offer_id', referencedColumnName: 'id')]
    protected Offers $offer;
    
    /** Локаль */
    #[ORM\Id]
    #[ORM\Column(type: Locale::TYPE, length: 2, nullable: false)]
    protected Locale $local;
    
    /** Название */
    #[ORM\Column(type: Types::STRING, length: 100, nullable: false)]
    protected string $name;
    
    /** Описание */
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    protected ?string $description;

    /**
     * @param Offers $offer
     */
    public function __construct(Offers $offer) { $this->offer = $offer; }
    
    /**
     * Метод заполняет объект DTO свойствами сущности и возвращает
     * @throws Exception
     */
    public function getDto($dto) : mixed
    {
        if($dto instanceof TransInterface)
        {
            return parent::getDto($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    /**
     * Метод присваивает свойствам значения из объекта DTO
     * @throws Exception
     */
    public function setEntity($dto) : mixed
    {
        if($dto instanceof TransInterface)
        {
            return parent::setEntity($dto);
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    
    
    public function equals($dto) : bool
    {
        if($dto instanceof TransInterface)
        {
            return  ($this->offer->getId() === $dto->getEquals() &&
              $this->local->getValue() === $dto->getLocal()?->getValue());
            
        }
        
        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
    
    //    public function updOfferTrans(TransInterface $offerTrans) : void
//    {
//        if(property_exists($offerTrans, 'name'))
//        {
//            $this->name = $offerTrans->name;
//        }
//
//        if(property_exists($offerTrans, 'description'))
//        {
//            $this->description = $offerTrans->description;
//        }
//    }
    
    

    
}
