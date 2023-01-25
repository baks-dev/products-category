<?php
/*
*  Copyright Baks.dev <admin@baks.dev>
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

namespace BaksDev\Products\Category\Controller\Admin\Recyclebin;

use BaksDev\Core\Services\Security\RoleSecurity;
use BaksDev\Products\Category\Entity\Event\Event;
use BaksDev\Products\Category\UseCase\Admin\Recyclebin\Restore\Category\CategoryDTO;
use BaksDev\Products\Category\UseCase\Admin\Recyclebin\Restore\RestoreForm;
use BaksDev\Products\Category\UseCase\CategoryAggregate;
use BaksDev\Core\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[RoleSecurity(['ROLE_ADMIN', 'ROLE_PRODUCT_CATEGORY_RESTORE'])]
final class RestoreController extends AbstractController
{
    #[Route('/admin/product/category/recyclebin/restore/{id}', name: 'admin.category.recyclebin.restore', methods: [
      'GET',
      'POST'
    ])]
    public function restore(
      Request $request,
      CategoryAggregate $handler,
      Event $Event,

        //        TranslatorInterface $translator,
        //        Handler $handler,
        //        Event $eventCategory,
        //        TransName $getTransName,
    ) : Response
    {
    
        $category = new CategoryDTO();
        $Event->getDto($category);
    
        /* Генерируем форму */
        $form = $this->createForm(RestoreForm::class, $category, [
          'action' => $this->generateUrl('ProductCategory:admin.category.recyclebin.restore', ['id' => $category->getEvent()]),
        ]);
        $form->handleRequest($request);
        

        if($form->isSubmitted() && $form->isValid())
        {
            if($form->has('restore'))
            {
                $handle = $handler->handle($category);
    
                if($handle)
                {
                    $this->addFlash('success', 'admin.category.restore.success', 'products.category');
                    return $this->redirectToRoute('ProductCategory:admin.category.recyclebin.index');
                }
                
                
                /* Восстанавливаем сущность */
//                $restore = $handler->handle($eventCategory);
//
//                if($restore['status'] != 200)
//                {
//                    $this->addFlash(
//                      'success',
//                      $translator->trans(
//                                'admin.category.restore.danger',
//                        domain: 'product.category').': '.$restore['msg']
//                    );
//
//                    return $this->redirect($request->headers->get('referer'));
//                }
            }
    
            $this->addFlash('danger', 'admin.category.update.danger', 'products.category');
            return $this->redirectToRoute('ProductCategory:admin.category.index');
        }
    
        return $this->render
        (
          [
            'form' => $form->createView(),
            'name' => $Event->getNameByLocale($this->getLocale()) /*  название согласно локали  */
          ]
        );
    }
    
}