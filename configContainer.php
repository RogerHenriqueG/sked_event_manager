<?php
use DI\Container;
use App\Service\Sql\Sql;
use App\Service\User\User;
use GuzzleHttp\Client;
use voku\helper\AntiXSS;
use App\Repository\EventsRepository;
use App\Controller\EventsController;

return function(Container $container){

    $container->set('Sql', function (Container $container) {
        return new Sql();
    });

    $container->set('EventsRepository', function (Container $container){
        $sql = $container->get('Sql');
        return new EventsRepository($sql);
    });

    $container->set('User', function (Container $container) {
        $sql = $container->get('Sql');
        return new User($sql);
    });

    $container->set('AntiXSS', function (Container $container) {
        return new AntiXSS();
    });

    $container->set('Client', function (Container $container) {
        return new Client();
    });

    $container->set('EventsController', function (Container $container) {
        $EventsRepository = $container->get('EventsRepository');
        $antiXss = $container->get('AntiXSS');
        return new EventsController($EventsRepository, $antiXss);
    });
};
