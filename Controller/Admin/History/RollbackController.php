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

namespace App\Module\Products\Category\Controller\Admin\History;


use App\System\Controller\AbstractController;
use App\System\Type\Locale\Locale;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted(new Expression('"ROLE_ADMIN" in role_names or "ROLE_PRODUCT_CATEGORY_ROLLBACK" in role_names'))]
final class RollbackController extends AbstractController
{
    #[Route('/admin/product/category/history/rollback/{id}', name: 'admin.category.history.rollback', methods: ['POST'])]
    public function rollback(
      Request $request,
//      TranslatorInterface $translator,
//      TransName $getTransName,
//      Handler $handler,
//        Category\Event $eventCategory,
    ) : Response
    {
        dd();
        $form = $this->createForm(RollbackForm::class, $eventCategory, [
          'action' => $this->generateUrl('Product:admin.category.history.rollback', ['id' => $eventCategory->getId()]),
        ]);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            if($form->has('activate'))
            {
                $handler->handle($eventCategory);
    
                $this->addFlash(
                  'success',
                  $translator->trans('admin.category.rollback.success', domain: 'product.category'));
            }
            
            return $this->redirectToRoute('Product:admin.category.index');
        }
        
        /* Получаем название согласно локали */
        $name = $getTransName($eventCategory->getId(), new Locale($request->getLocale()));
        
        return $this->render(['form' => $form->createView(), 'name' => $name]);
    }
    


    
}