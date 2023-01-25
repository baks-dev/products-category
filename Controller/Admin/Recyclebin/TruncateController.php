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

use BaksDev\Core\Controller\AbstractController;

use BaksDev\Core\Services\Security\RoleSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[RoleSecurity(['ROLE_ADMIN', 'ROLE_PRODUCT_CATEGORY_REMOVE'])]
final class TruncateController extends AbstractController
{
    #[Route('/admin/product/category/recyclebin/truncate', name: 'admin.category.recyclebin.truncate', methods: ['GET', 'POST'])]
    public function truncate(
      Request             $request,
//      TranslatorInterface $translator,
//      MessageBusInterface $bus,
//      Handler $handler
    ): Response
    {
        dd();
        
        /* Генерируем форму */
        $form = $this->createForm(TruncateForm::class, null, [
            'action' => $this->generateUrl('Product:admin.category.recyclebin.truncate'),
        ]);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid())
        {
            if($form->has('truncate'))
            {
                /* Очищаем корзину */
                $cmd = new Command();
                //$handler->handle($cmd); /* Очищаем через обработчик */
                $bus->dispatch($cmd); /* Очищаем в очереди */
                
                $this->addFlash(
                'success',
                $translator->trans('truncate.success'));
                
                return $this->redirectToRoute('Product:admin.category.index');
            }

            return $this->redirectToRoute('Product:admin.category.recyclebin.index');
        }

        return $this->render(['form' => $form->createView()]);
    }


}