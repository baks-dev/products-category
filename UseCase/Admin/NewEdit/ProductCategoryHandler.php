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

declare(strict_types=1);

namespace BaksDev\Products\Category\UseCase\Admin\NewEdit;

use BaksDev\Core\Entity\AbstractHandler;
use BaksDev\Core\Messenger\MessageDispatchInterface;
use BaksDev\Files\Resources\Upload\Image\ImageUploadInterface;
use BaksDev\Products\Category\Entity\Cover\ProductCategoryCover;
use BaksDev\Products\Category\Entity\Event\ProductCategoryEvent;
use BaksDev\Products\Category\Entity\ProductCategory;
use BaksDev\Products\Category\Messenger\ProductCategoryMessage;
use BaksDev\Products\Category\Repository\UniqCategoryUrl\UniqCategoryUrl;
use BaksDev\Products\Category\UseCase\Admin\NewEdit\Cover\ProductCategoryCoverDTO;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ProductCategoryHandler extends AbstractHandler
{
//    private EntityManagerInterface $entityManager;
//
//    private ValidatorInterface $validator;
//
//    private LoggerInterface $logger;
//
//    private ImageUploadInterface $imageUpload;
//
//    private UniqCategoryUrl $uniqCategoryUrl;
//
//    private MessageDispatchInterface $messageDispatch;
//
//    public function __construct(
//        EntityManagerInterface $entityManager,
//        ValidatorInterface $validator,
//        LoggerInterface $logger,
//        ImageUploadInterface $imageUpload,
//        UniqCategoryUrl $uniqCategoryUrl,
//        MessageDispatchInterface $messageDispatch
//    )
//    {
//        $this->entityManager = $entityManager;
//        $this->validator = $validator;
//        $this->logger = $logger;
//        $this->imageUpload = $imageUpload;
//        $this->uniqCategoryUrl = $uniqCategoryUrl;
//
//        $this->messageDispatch = $messageDispatch;
//    }


    public function handle(ProductCategoryDTO $command,): string|ProductCategory
    {
        /** Валидация DTO  */
        $this->validatorCollection->add($command);

        $this->main = new ProductCategory();
        $this->event = new ProductCategoryEvent();

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

        $this->entityManager->flush();

        /* Отправляем событие в шину  */
        $this->messageDispatch->dispatch(
            message: new ProductCategoryMessage($this->main->getId(), $this->main->getEvent(), $command->getEvent()),
            transport: 'products-category'
        );

        return $this->main;
    }


}
