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

namespace App\Module\Products\Category\UseCase;

use App\Module\Files\Res\Upload\Image\ImageUploadInterface;
use App\Module\Products\Category\Entity as EntityCategory;
use App\Module\Products\Category\Entity\Event\EventInterface;
use App\Module\Products\Category\UseCase\Admin\NewEdit\Cover\CoverDTO;
use App\System\Type\Modify\ModifyActionEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class CategoryAggregate
{
    private EntityManagerInterface $entityManager;
    private ImageUploadInterface $imageUpload;
    
    public function __construct(
      EntityManagerInterface $entityManager,
      ImageUploadInterface $imageUpload,
    
    )
    {
        $this->entityManager = $entityManager;
        $this->imageUpload = $imageUpload;
    }
    
    public function handle(
      EventInterface $command,
      ?UploadedFile $cover = null
    ) : bool|string
    {
        
        if($command->getEvent())
        {
            $Event = $this->entityManager->getRepository(EntityCategory\Event\Event::class)->find($command->getEvent());
            
            //$EventRepo = $this->entityManager->getRepository(Entity\Event\Event::class)->find($command->getEvent());
            //$Event = $EventRepo->cloneEntity();
        }
        else
        {
            $Event = new EntityCategory\Event\Event($command->getParent());
            $this->entityManager->persist($Event);
        }
        
        $Event->setEntity($command);
        
       
        //dump($EventRepo);
        //dd($Event);
        
        //$Event->updCategoryEvent($command);
        
        /* Загрузка файла изображения */
        if($cover !== null)
        {
            /** @var CoverDTO $Avatar */
            $Cover = $command->getCover();
            
            if(!empty($Cover?->file))
            {
                //$Cover->setCdn(false);
                $this->imageUpload->upload('category_cover_dir', $Cover->file, $Event->getUploadCover());
            }
            
        }
        
        
        
        
        //dump($command);
        //dd($Event);
        
        //$this->entityManager->clear();
        //$this->entityManager->persist($Event);
        
        /** @var EntityCategory\Category $Category */
        if($Event->getCategory())
        {
            /* Восстанавливаем из корзины */
            if($Event->isModifyActionEquals(ModifyActionEnum::RESTORE))
            {
                $Category = new EntityCategory\Category();
                $Category->setId($Event->getCategory());
                $this->entityManager->persist($Category);
                
                $remove = $this->entityManager->getRepository(EntityCategory\Event\Event::class)
                  ->find($command->getEvent());
                $this->entityManager->remove($remove);
                
            }
            else
            {
                $Category = $this->entityManager->getRepository(EntityCategory\Category::class)->findOneBy(
                  ['event' => $command->getEvent()]);
            }
            
            if(empty($Category))
            {
                return false;
            }
        }
        else
        {
            $Category = new EntityCategory\Category();
            $this->entityManager->persist($Category);
            
            $Event->setCategory($Category);
            
        }
        
        $Category->setEvent($Event);
        
        /* Удаляем категорию */
        if($Event->isModifyActionEquals(ModifyActionEnum::DELETE))
        {
            $this->entityManager->remove($Category);
        }
        
        $this->entityManager->flush();
        
        return true;
    }
    
}