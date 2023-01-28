<?php
/*
 *  Copyright 2023.  Baks.dev <admin@baks.dev>
 *  
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *  
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *  
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

namespace BaksDev\Products\Category\Entity\Offers\Trans;

use BaksDev\Products\Category\Entity\Offers\Offers;
use BaksDev\Core\Entity\EntityState;
use BaksDev\Core\Type\Locale\Locale;
use BaksDev\Products\Category\Entity\Offers\ProductCategoryOffers;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;

/* Перевод Торговых предложений */

#[ORM\Entity]
#[ORM\Table(name: 'product_category_offers_trans')]
class ProductCategoryOffersTrans extends EntityState
{
	public const TABLE = 'product_category_offers_trans';
	
	/** Связь на торговое предложение */
	#[ORM\Id]
	#[ORM\ManyToOne(targetEntity: ProductCategoryOffers::class, inversedBy: "translate")]
	#[ORM\JoinColumn(name: 'offer', referencedColumnName: 'id')]
	private readonly ProductCategoryOffers $offer;
	
	/** Локаль */
	#[ORM\Id]
	#[ORM\Column(type: Locale::TYPE, length: 2, nullable: false)]
	private readonly Locale $local;
	
	/** Название */
	#[ORM\Column(type: Types::STRING, length: 100, nullable: false)]
	private string $name;
	
	
	public function __construct(ProductCategoryOffers $offer)
	{
		$this->offer = $offer;
	}
	
	public function getDto($dto) : mixed
	{
		if($dto instanceof ProductCategoryOffersTransInterface)
		{
			return parent::getDto($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	
	public function setEntity($dto) : mixed
	{
		if($dto instanceof ProductCategoryOffersTransInterface)
		{
			return parent::setEntity($dto);
		}
		
		throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
	}
	
	
	
	/*    public function equals($dto) : bool
		{
			if($dto instanceof TransInterface)
			{
				return  ($this->offer->getId() === $dto->getEquals() &&
				  $this->local->getValue() === $dto->getLocal()?->getValue());
				
			}
			
			throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
		}*/
	
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
