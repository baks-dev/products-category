<?php

// namespace Symfony\Component\DependencyInjection\Loader\Configurator;


use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $config)
{
    /* Очистка корзины */
    //$config->messenger()->routing(Handler\Admin\Profile\Truncate\Command::class)->senders(['async_priority_low']);
};

