<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Config\TwigConfig;

return static function (TwigConfig $config, ContainerConfigurator $configurator)
{
    $config->path('%kernel.project_dir%/src/Module/Products/Category/Resources/view', 'ProductCategory');
    
    
    /** Абсолютный Путь для загрузки обложек разделов */
    $configurator->parameters()->set('category_cover_dir', '%kernel.project_dir%/public/assets/images/products/category/cover/');
    
    /** Относительный путь обложек разделов */
    $config->global('category_cover_dir')->value('/images/products/category/cover/');
    
    
    
    
};




