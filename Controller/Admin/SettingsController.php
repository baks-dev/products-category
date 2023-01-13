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

namespace App\Module\Products\Category\Controller\Admin;


use App\System\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression('"ROLE_ADMIN" in role_names or "ROLE_PRODUCT_CATEGORY_SETTINGS" in role_names'))]
final class SettingsController extends AbstractController
{
    #[Route('/admin/product/category/recyclebin/settings', name: 'admin.category.recyclebin.settings', methods: [
      'GET',
      'POST'
    ])]
    public function index(
      Request $request,
//      SettingsCategory $getSettings,
//      Handler $handler,
//      TranslatorInterface $translator,
    ) : Response
    {
        
        dd();
        
        $settings = $getSettings();
        
        /* Генерируем форму */
        $form = $this->createForm(
          SettingsForm::class,
          $settings,
          ['action' => $this->generateUrl('Product:admin.category.recyclebin.settings')]
        );
        $form->handleRequest($request);
        
        /* сохраняем изменения */
        if($form->isSubmitted() && $form->isValid())
        {
            
            if($form->has('categorySettings'))
            {
                /* Обновляем настройки */
                $handler->handle($settings);
                
                $this->addFlash(
                  'success',
                  $translator->trans('admin.category.settings.success', domain: 'product.category'));
            }
            
            return $this->redirect($request->headers->get('referer'));
        }
        
        return $this->render(['form' => $form->createView()]);
    }
    
}