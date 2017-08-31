<?php

namespace Plugin\SSProductDetailPage\ServiceProvider;

use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Silex\Provider\MonologServiceProvider;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Yaml\Yaml;

class SSProductDetailPageProvider implements ServiceProviderInterface
{

    public function register(BaseApplication $app)
    {
        
        $app->match('/' . $app["config"]["admin_route"] . '/ss/product/detail/layout/{id}/edit',
            '\Plugin\SSProductDetailPage\Controller\LayoutController::index')
            ->assert('id', '\d+')->bind('ss_admin_product_detail_layout_edit');
        
        $app->match('/' . $app["config"]["admin_route"] . '/ss/product/detail/layout/{id}/preview', 
            '\Plugin\SSProductDetailPage\Controller\LayoutController::preview')
            ->assert('id', '\d+')->bind('ss_admin_product_detail_layout_preview');
        
        $app->match('/' . $app["config"]["admin_route"] . '/ss/product/detail/layout/{id}/delete',
            '\Plugin\SSProductDetailPage\Controller\LayoutController::delete')
            ->assert('id', '\d+')->bind('ss_admin_product_detail_layout_delete');
        
        $app['plugin.ss_product_detail.repository.page_layout'] = $app->share(function () use ($app) {
            $pageLayoutRepository = $app['orm.em']->getRepository('Plugin\SSProductDetailPage\Entity\ProductDetailLayout');
            $pageLayoutRepository->setApplication($app);
        
            return $pageLayoutRepository;
        });
        
        $app['plugin.ss_product_detail.repository.block_position'] = $app->share(function () use ($app) {
            $pageLayoutRepository = $app['orm.em']->getRepository('Plugin\SSProductDetailPage\Entity\ProductDetailBlockPosition');
        
            return $pageLayoutRepository;
        });
    }

    public function boot(BaseApplication $app)
    {
    }
}
