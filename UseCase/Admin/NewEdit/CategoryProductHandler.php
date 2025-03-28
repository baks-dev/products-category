<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
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

declare(strict_types=1);

namespace BaksDev\Products\Category\UseCase\Admin\NewEdit;

use BaksDev\Core\Entity\AbstractHandler;
use BaksDev\Products\Category\Entity\CategoryProduct;
use BaksDev\Products\Category\Entity\Event\CategoryProductEvent;
use BaksDev\Products\Category\Messenger\ProductCategoryMessage;
use DomainException;

final class CategoryProductHandler extends AbstractHandler
{
    public function handle(CategoryProductDTO $command): string|CategoryProduct
    {

        //        if($command->getOffer()?->isOffer())
        //        {
        //            $offer = $command->getOffer();
        //
        //            if($offer?->getVariation()->isVariation())
        //            {
        //                $variation = $offer?->getVariation();
        //
        //                if($variation->getModification()->isModification())
        //                {
        //
        //
        //                }
        //
        //            }
        //
        //        }

        /** Делаем сброс иерархии настроек торговых предложений  */
        $command->resetOffer();

        /** Валидация DTO  */
        $this->validatorCollection->add($command);

        $this->main = new CategoryProduct();
        $this->event = new CategoryProductEvent();

        try
        {
            $command->getEvent() ? $this->preUpdate($command, false) : $this->prePersist($command);
        }
        catch(DomainException $errorUniqid)
        {
            return $errorUniqid->getMessage();
        }


        /** Загружаем файл обложки раздела */

        if(method_exists($command, 'getCover'))
        {
            $Cover = $command->getCover();

            if($Cover && $Cover->file !== null)
            {
                $ProductCategoryCover = $this->event->getUploadCover();
                $this->imageUpload->upload($Cover->file, $ProductCategoryCover);
            }
        }


        /** Валидация всех объектов */
        if($this->validatorCollection->isInvalid())
        {
            return $this->validatorCollection->getErrorUniqid();
        }


        $this->flush();

        /* Отправляем событие в шину  */
        $this->messageDispatch
            ->addClearCacheOther('products-product')
            ->dispatch(
            message: new ProductCategoryMessage($this->main->getId(), $this->main->getEvent(), $command->getEvent()),
            transport: 'products-category'
        );

        return $this->main;
    }


}
