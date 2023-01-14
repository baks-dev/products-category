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

use App\Module\Product\Repository\Category\Info\InfoRepository;
use App\Module\Users\Repository\User\UserProfile;
use App\Module\Product\Entity\Category\Event;
use App\Module\Product\Handler\Admin\Category\NewEdit\CategoryForm;
use App\Module\Product\Handler\Admin\Category\Restore\Handler;
use App\System\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted(new Expression('"ROLE_ADMIN" in role_names or "ROLE_PRODUCT_CATEGORY_RECYCLEBIN" in role_names'))]
final class DetailController extends AbstractController
{
    #[Route('/admin/product/category/recyclebin/detail/{id}', name: 'admin.category.recyclebin.detail', methods: [
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
            
            /* Восстанавливаем сущность */
            $restore = $handler->handle($eventCategory);

            if($restore['status'] !== 200)
            {
                $this->addFlash(
                  'danger',
                  $translator->trans('admin.category.restore.danger', domain: 'product.category').': '.$restore['msg']
                );
                
                return $this->redirect($request->headers->get('referer'));
            }
            
            $this->addFlash(
              'success',
              $translator->trans('admin.category.restore.success', domain: 'product.category'));
            
            return $this->redirectToRoute('Product:admin.category.recyclebin.index');
        }
        
        return $this->render(
          [
            'form' => $form->createView(),
            'data' => $eventCategory,
            //'user' => $user($eventCategory->getModify()->getUser()->getId())
          ]);
    }
    
//    #[Route('/624b349091b54/style', name: 'admin.category.recyclebin.detail.css', methods: ['GET'], format: "css")]
//    public function css() : Response
//    {
//        return $this->assets(
//          [
//            '/css/select2.min.css', // Select2
//          ]);
//    }
//
//    #[Route('/624b349091b54/app', name: 'admin.category.recyclebin.detail.js', methods: ['GET'], format: "js")]
//    public function js() : Response
//    {
//        return $this->assets
//        (
//          [
//            '/plugins/semantic/semantic.min.js',
//            '/product/category.min.js'
//          ]);
//    }
    
}