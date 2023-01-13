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

use App\Module\Product\Repository\Category\Info\InfoRepository;
use App\System\Controller\AbstractController;
use App\Module\User\Repository\User\UserProfile;
use App\Module\Product\Entity\Category\Event;
use App\Module\Product\Handler\Admin\Category\Detail\Handler;
use App\Module\Product\Handler\Admin\Category\NewEdit\CategoryForm;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted(new Expression('"ROLE_ADMIN" in role_names or "ROLE_PRODUCT_CATEGORY_HISTORY" in role_names'))]
final class DetailController extends AbstractController
{
    #[Route('/admin/product/category/history/detail/{id}', name: 'admin.category.history.detail', methods: [
      'GET',
      'POST'
    ])]
    public function detail(
      Request $request,
//      Event $eventCategory,
//      Handler $handler,
//      TranslatorInterface $translator,
//      UserProfile $user,
//      InfoRepository $getInfo,
    ) : Response
    {
        dd();
        
        $info = $getInfo->get($eventCategory->getCategory());
        if($info === null) { throw new RouteNotFoundException(); }
        
        /* Форма восстановления */
        $form = $this->createForm(CategoryForm::class, $eventCategory);
        $form->get('info')->setData($info);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            //if($form->has('activate'))
            //{
            $handler->handle($eventCategory);
            
            $this->addFlash(
              'success',
              $translator->trans('admin.category.activate.success', domain: 'product.category')
            );
            //}
            
            return $this->redirectToRoute(
              'Product:admin.category.history.index',
              ['id' => $eventCategory->getCategory()]);
        }
        
        return $this->render(
          [
            'form' => $form->createView(),
            'data' => $eventCategory,
            'user' => $user($eventCategory->getModify()->getUser()->getId())
          ]);
    }

}