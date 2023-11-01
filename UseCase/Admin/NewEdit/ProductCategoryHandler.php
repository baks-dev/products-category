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
use BaksDev\Products\Category\Entity\Event\ProductCategoryEvent;
use BaksDev\Products\Category\Entity\ProductCategory;
use BaksDev\Products\Category\Messenger\ProductCategoryMessage;
use BaksDev\Products\Category\Repository\UniqCategoryUrl\UniqCategoryUrl;
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
            return $errorUniqid;
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


    public function OLDhandle(ProductCategoryDTO $command,): string|ProductCategory
    {
        /* Валидация DTO */
        $errors = $this->validator->validate($command);

        if(count($errors) > 0)
        {
            /** Ошибка валидации */
            $uniqid = uniqid('', false);
            $this->logger->error(sprintf('%s: %s', $uniqid, $errors), [__FILE__.':'.__LINE__]);

            return $uniqid;
        }

        if($command->getEvent())
        {
            $Event = $this->entityManager->getRepository(ProductCategoryEvent::class)->find(
                $command->getEvent()
            );

            if($Event === null)
            {
                $uniqid = uniqid('', false);
                $errorsString = sprintf(
                    'Not found %s by id: %s',
                    ProductCategoryEvent::class,
                    $command->getEvent()
                );
                $this->logger->error($uniqid.': '.$errorsString);

                return $uniqid;
            }

            //$Event = $EventRepo->cloneEntity();
        }
        else
        {
            $Event = new ProductCategoryEvent();
            $this->entityManager->persist($Event);
        }

        $Event->setEntity($command);


        /* @var ProductCategory $Main */
        if($Event->getCategory())
        {
            $Main = $this->entityManager->getRepository(ProductCategory::class)
                ->findOneBy(['event' => $command->getEvent()]);

            if(empty($Main))
            {
                $uniqid = uniqid('', false);
                $errorsString = sprintf(
                    'Not found %s by event: %s',
                    ProductCategory::class,
                    $command->getEvent()
                );
                $this->logger->error($uniqid.': '.$errorsString);

                return $uniqid;
            }
        }
        else
        {
            $Main = new ProductCategory();
            $this->entityManager->persist($Main);

            $Event->setMain($Main);
            $Main->setEvent($Event);
        }

        /**
         * Проверяем уникальность семантической ссылки раздела.
         *
         * @see Info\InfoDTO $infoDTO
         */
        $infoDTO = $command->getInfo();
        $uniqCategoryUrl = $this->uniqCategoryUrl->exist($infoDTO->getUrl(), $Event->getId());

        if($uniqCategoryUrl)
        {
            $infoDTO->updateUrlUniq(); /* Обновляем URL на уникальный с префиксом */
        }


        /** Загружаем файл обложки.
         *
         * @var Cover\ProductCategoryCoverDTO $Cover
         */
        $Cover = $command->getCover();
        if($Cover->file !== null)
        {
            $ProductCategoryCover = $Event->getUploadCover();
            $this->imageUpload->upload($Cover->file, $ProductCategoryCover);
        }

        //dump($this->entityManager->getUnitOfWork());
        //dd($Event);

//        /**
//         * Удаляем отстутсвующие объекты коллекци
//         * @see EntityState
//         */
//        foreach($Event->getRemoveEntity() as $remove)
//        {
//            $this->entityManager->remove($remove);
//        }


        /**
         * Валидация Event
         */

        $errors = $this->validator->validate($Event);

        if(count($errors) > 0)
        {
            /** Ошибка валидации */
            $uniqid = uniqid('', false);
            $this->logger->error(sprintf('%s: %s', $uniqid, $errors), [__FILE__.':'.__LINE__]);

            return $uniqid;
        }


        $this->entityManager->flush();


        /* Отправляем событие в шину  */
        $this->messageDispatch->dispatch(
            message: new ProductCategoryMessage($Main->getId(), $Main->getEvent(), $command->getEvent()),
            transport: 'products-category'
        );

        return $Main;
    }
}
