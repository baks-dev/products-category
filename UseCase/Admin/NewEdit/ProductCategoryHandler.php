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

use BaksDev\Files\Resources\Upload\Image\ImageUploadInterface;
use BaksDev\Products\Category\Entity;
use BaksDev\Products\Category\Repository\UniqCategoryUrl\UniqCategoryUrl;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ProductCategoryHandler
{
	private EntityManagerInterface $entityManager;
	private ValidatorInterface $validator;
	private LoggerInterface $logger;
	private ImageUploadInterface $imageUpload;
	private UniqCategoryUrl $uniqCategoryUrl;
	
	public function __construct(
		EntityManagerInterface $entityManager,
		ValidatorInterface $validator,
		LoggerInterface $logger,
		ImageUploadInterface $imageUpload,
		UniqCategoryUrl $uniqCategoryUrl
	)
	{
		$this->entityManager = $entityManager;
		$this->validator = $validator;
		$this->logger = $logger;
		$this->imageUpload = $imageUpload;
		$this->uniqCategoryUrl = $uniqCategoryUrl;
	}
	
	public function handle(
		ProductCategoryDTO $command,
		//?UploadedFile $cover = null
	) : string|Entity\ProductCategory
	{
		/* Валидация */
		$errors = $this->validator->validate($command);
		
		if(count($errors) > 0)
		{
			$uniqid = uniqid('', false);
			$errorsString = (string) $errors;
			$this->logger->error($uniqid.': '.$errorsString);
			return $uniqid;
		}
		
		
		if($command->getEvent())
		{
			$Event = $this->entityManager->getRepository(Entity\Event\ProductCategoryEvent::class)->find(
				$command->getEvent()
			);
			
			if($Event === null)
			{
				$uniqid = uniqid('', false);
				$errorsString = sprintf(
					'Not found %s by id: %s',
					Entity\Event\ProductCategoryEvent::class,
					$command->getEvent()
				);
				$this->logger->error($uniqid.': '.$errorsString);
				
				return $uniqid;
			}
			
			//$Event = $EventRepo->cloneEntity();
			
		}
		else
		{
			$Event = new Entity\Event\ProductCategoryEvent();
			$this->entityManager->persist($Event);
		}
		
		
		//$this->entityManager->clear();
		
		
		/** @var Entity\ProductCategory $Main */
		if($Event->getCategory())
		{
			$Main = $this->entityManager->getRepository(Entity\ProductCategory::class)->findOneBy(
				['event' => $command->getEvent()]
			);
			
			if(empty($Main))
			{
				$uniqid = uniqid('', false);
				$errorsString = sprintf(
					'Not found %s by event: %s',
					Entity\ProductCategory::class,
					$command->getEvent()
				);
				$this->logger->error($uniqid.': '.$errorsString);
				
				return $uniqid;
			}
			
		}
		else
		{
			
			$Main = new Entity\ProductCategory();
			$this->entityManager->persist($Main);
			
			$Event->setCategory($Main);
			$Main->setEvent($Event);
		}
		
		
		/** Проверяем уникальность семантической ссылки раздела
		 *
		 * @see Info\InfoDTO $infoDTO
		 */
		$infoDTO = $command->getInfo();
		$uniqCategoryUrl = $this->uniqCategoryUrl->exist($infoDTO->getUrl(), $Event->getId());
		
		if($uniqCategoryUrl)
		{
			$infoDTO->updateUrlUniq(); /* Обновляем URL на уникальный с префиксом */
		}
		
		
		$Event->setEntity($command);
		
		/** Загружаем файл обложки
		 *
		 * @var Cover\ProductCategoryCoverDTO $Cover
		 */
		$Cover = $command->getCover();
		if($Cover->file !== null)
		{
			$ProductCategoryCover = $Event->getUploadCover();
			$this->imageUpload->upload($Cover->file, $ProductCategoryCover);
		}
		
		
		/** Удаляем отстутсвующие объекты коллекци
		 *
		 * @see EntityState
		 */
		foreach($Event->getRemoveEntity() as $remove)
		{
			$this->entityManager->remove($remove);
		}
		
		$this->entityManager->flush();
		
		
		return $Main;
	}
}