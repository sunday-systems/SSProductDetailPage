<?php

namespace Plugin\SSProductDetailPage;

use Eccube\Event\TemplateEvent;
use Eccube\Event\EventArgs;

class SSProductDetailPage {

    /**
     * @var \Eccube\Application
     */
    private $app;

    public function __construct(\Eccube\Application $app) 
    {
        $this->app = $app;
    }
    
    public function onRenderTplAdminProductDetail(TemplateEvent $event)
    {
        
        $data = $event->getParameters();
        
        $data['IsExPageLayout'] = false;
        if ($data['Product'] && $data['Product']->getId()) {
            /* @var $em \Doctrine\ORM\EntityManager */
            $em = $this->app['orm.em'];
            $pageId = $em->getConnection()->fetchAll("SELECT page_id FROM plg_ss_product_detail_layout WHERE page_id = ? AND device_type_id = 10", array($data['Product']->getId()));
            if (count($pageId)) {
                $data['IsExPageLayout'] = true;
            }
        }
        
        $oldMethod = '{% if Product.id %}';
        $source = str_replace($oldMethod, 
            $oldMethod . '{% if Product.Id %}
                <a class="btn btn-default btn-sm" href="{{ url(\'ss_admin_product_detail_layout_edit\', {id: Product.id}) }}">独自レイアウト</a>
                {% if IsExPageLayout %}<a class="btn btn-default btn-sm" href="{{ url(\'ss_admin_product_detail_layout_delete\', {id: Product.id}) }}" {{ csrf_token_for_anchor() }} data-method="delete" data-message="このレイアウトを削除してもよろしいですか？">レイアウト削除</a>{% endif %}
            {% endif %}', 
            $event->getSource());
        
        $event->setParameters($data);
        $event->setSource($source);
    }
    
    public function onFrontProductDetailInit(EventArgs $event)
    {
        /* @var $product \Eccube\Entity\Product */
        $product = $event->getArgument('Product');
        
        if ($product) {
            $pid = $product->getId();
            
            try {
                $DeviceType = $this->app['eccube.repository.master.device_type']
                    ->find(\Eccube\Entity\Master\DeviceType::DEVICE_TYPE_PC);
                
                /* @var $oldPageLayout \Eccube\Entity\PageLayout */
                /* @var $PageLayout \Plugin\SSProductDetailPage\Entity\ProductDetailLayout */
                $oldPageLayout = null;
                    
                $data = $this->app['twig']->getGlobals();
                if (array_key_exists('PageLayout', $data)) {
                    $oldPageLayout = $data['PageLayout'];
                }

                if (array_key_exists('preview', $_REQUEST)) {
                    $pid = 0;
                }
                
                $PageLayout = $this->app['plugin.ss_product_detail.repository.page_layout']->get($DeviceType, $pid);
                
                if ($PageLayout) {
                    if ($oldPageLayout) {
                        $PageLayout->setAuthor($oldPageLayout->getAuthor());
                        $PageLayout->setDescription($oldPageLayout->getDescription());
                        $PageLayout->setKeyword($oldPageLayout->getKeyword());
                        $PageLayout->setMetaRobots($oldPageLayout->getMetaRobots());
                        if (method_exists($oldPageLayout, 'getMetaTags')) {
                            $PageLayout->setMetaTags($oldPageLayout->getMetaTags());
                        }   
                    }
                    
                    $this->app['twig']->addGlobal('PageLayout', $PageLayout);
                }
            } catch (\Doctrine\ORM\NoResultException $e) {
                
            }
        }
    }
}

