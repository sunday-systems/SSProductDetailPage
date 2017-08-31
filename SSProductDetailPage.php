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
    
    public function onRenderTplAdminProductCategory(TemplateEvent $event)
    {
        
        $data = $event->getParameters();
        
        $data['IsExPageLayout'] = false;
        if ($data['Parent']) {
            /* @var $em \Doctrine\ORM\EntityManager */
            $em = $this->app['orm.em'];
            $pageId = $em->getConnection()->fetchAll("SELECT page_id FROM plg_ss_product_list_layout WHERE page_id = ? AND device_type_id = 10", array($data['Parent']->getId()));
            if (count($pageId)) {
                $data['IsExPageLayout'] = true;
            }
        }
        
        $oldMethod = '<div class="extra-form">';
        $source = str_replace($oldMethod, 
            $oldMethod . '{% if Parent %}
                <a class="btn btn-default btn-sm" href="{{ url(\'ss_admin_product_list_layout_edit\', {id: Parent.id}) }}">独自レイアウト</a>
                {% if IsExPageLayout %}<a class="btn btn-default btn-sm" href="{{ url(\'ss_admin_product_list_layout_delete\', {id: Parent.id}) }}" {{ csrf_token_for_anchor() }} data-method="delete" data-message="このレイアウトを削除してもよろしいですか？">レイアウト削除</a>{% endif %}
            {% endif %}', 
            $event->getSource());
        
        $event->setParameters($data);
        $event->setSource($source);
    }
    
    public function onFrontProductIndexInit(EventArgs $event)
    {
        /* @var $form \Symfony\Component\Form\FormBuilder */
        
        
        if (array_key_exists('category_id', $_REQUEST)) {
            $cid = intval($_REQUEST['category_id']);
            
            try {
                $DeviceType = $this->app['eccube.repository.master.device_type']
                    ->find(\Eccube\Entity\Master\DeviceType::DEVICE_TYPE_PC);
                
                /* @var $oldPageLayout \Eccube\Entity\PageLayout */
                /* @var $PageLayout \Plugin\SSProductListPage\Entity\ProductListLayout */
                $oldPageLayout = null;
                    
                $data = $this->app['twig']->getGlobals();
                if (array_key_exists('PageLayout', $data)) {
                    $oldPageLayout = $data['PageLayout'];
                }
                /*
                $PageLayout = $this->app['eccube.repository.page_layout']->getByUrl($DeviceType, 'product_list', 'product_list');
                */
                
                if (array_key_exists('preview', $_REQUEST)) {
                    $cid = 0;
                }
                
                $PageLayout = $this->app['plugin.ss_product_list.repository.page_layout']->get($DeviceType, $cid);
                if ($PageLayout) {
                    if ($oldPageLayout) {
                        $PageLayout->setAuthor($oldPageLayout->getAuthor());
                        $PageLayout->setDescription($oldPageLayout->getDescription());
                        $PageLayout->setKeyword($oldPageLayout->getKeyword());
                        $PageLayout->setMetaRobots($oldPageLayout->getMetaRobots());
                        if (function_exists($oldPageLayout, 'getMetaTags')) {
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

