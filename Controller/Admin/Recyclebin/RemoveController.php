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

namespace App\Module\Products\Category\Controller\Admin\Recyclebin;

use App\System\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\System\Type\Locale\Locale;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted(new Expression('"ROLE_ADMIN" in role_names or "ROLE_PRODUCT_CATEGORY_REMOVE" in role_names'))]
final class RemoveController extends AbstractController
{
    #[Route('/admin/product/category/recyclebin/remove/{id}', name: 'admin.category.recyclebin.remove', methods: ['GET', 'POST'])]
    public function remove(
        Request              $request,
//        TranslatorInterface  $translator,
//        MessageBusInterface  $bus,
//        Handler $handler,
//        Event $eventCategory,
//        TransName $getTransName,
    ): Response
    {
        dd();

        $form = $this->createForm(RemoveForm::class, $eventCategory, [
            'action' => $this->generateUrl('Product:admin.category.recyclebin.remove', ['id' => $eventCategory->getId()]),
        ]);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid())
        {
            if($form->has('remove'))
            {
                /* Очищаем все события Category */
                $remove = $handler->handle($eventCategory);
                
                if($remove['status'] !== 200)
                {
                    $this->addFlash(
                        'danger',
                        $translator->trans('admin.level.remove.danger', domain: 'product.category').': '.$remove['msg']
                    );
                
                    return $this->redirect($request->headers->get('referer'));
                }
    
                $this->addFlash(
                'success',
                $translator->trans('admin.category.remove.success', domain: 'product.category'));
            }

            return $this->redirectToRoute('Product:admin.category.recyclebin.index');
        }

        /* Получаем название согласно локали */
        $name = $getTransName($eventCategory->getId(), new Locale($request->getLocale()));
        
        return $this->render(['form' => $form->createView(), 'name' => $name]);
    }


}