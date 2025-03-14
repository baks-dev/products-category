<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Products\Category\Listeners\Event;

use BaksDev\Products\Category\Repository\MenuPublicCategory\MenuPublicCategoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

#[AsEventListener(event: ControllerEvent::class)]
final readonly class PublicCatalogMenuListener
{
    public function __construct(
        private Environment $twig,
        private MenuPublicCategoryInterface $category
    ) {}

    /** Создает в меню раздел с категориями продукции */
    public function onKernelController(ControllerEvent $event): void
    {
        if($event->getRequest()->isXmlHttpRequest())
        {
            return;
        }

        if(str_contains($event->getRequest()->getRequestUri(), 'admin'))
        {
            return;
        }

        $this->twig->addGlobal('baks_public_menu', $this->category->findAll());
        //$this->twig->addGlobal('baks_public_menu', []);
    }

}
