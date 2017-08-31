<?php

namespace Plugin\SSProductDetailPage\Controller;

use Eccube\Application;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Symfony\Component\HttpFoundation\Request;
use Plugin\SSProductDetailPage\Form\Type\Admin\PageLayoutType;
use Eccube\Controller\AbstractController;

class LayoutController extends AbstractController
{
    private $isPreview = false;
    
    public function index(Application $app, Request $request, $id = 1, $origId = 1)
    {
        $DeviceType = $app['eccube.repository.master.device_type']
            ->find(\Eccube\Entity\Master\DeviceType::DEVICE_TYPE_PC);
        
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $app['orm.em'];
        $em->getConnection()->exec("DELETE FROM plg_ss_product_detail_block_position WHERE page_id = 0");
        
        // 編集対象ページ
        /* @var $TargetPageLayout \Plugin\SSProductDetailPage\Entity\ProductDetailLayout */
        try {
            $TargetPageLayout = $app['plugin.ss_product_detail.repository.page_layout']->get($DeviceType, $id);
        } catch (\Exception $e) {
            $TargetPageLayout = $app['plugin.ss_product_detail.repository.page_layout']->newPageLayout($DeviceType, $id);
        }
        
        $Blocks = $app['orm.em']->getRepository('Eccube\Entity\Block')
            ->findBy(array(
                'DeviceType' => $DeviceType,
            ));
        $BlockPositions = $TargetPageLayout->getBlockPositions();
    
        $pageForm = new PageLayoutType();
        $builderLayout = $app['form.factory']->createBuilder($pageForm);
    
        // 未使用ブロックの取得
        $unusedBlocks = $app['plugin.ss_product_detail.repository.page_layout']->findUnusedBlocks($DeviceType, $id);
        
        foreach ($unusedBlocks as $unusedBlock) {
            $UnusedBlockPosition = new \Plugin\SSProductDetailPage\Entity\ProductDetailBlockPosition();
            $UnusedBlockPosition
                ->setPageId($id)
                ->setTargetId(\Eccube\Entity\PageLayout::TARGET_ID_UNUSED)
                ->setAnywhere(0)
                ->setBlockRow(0)
                ->setBlockId($unusedBlock->getId())
                ->setBlock($unusedBlock)
                ->setProductDetailLayout($TargetPageLayout);
            $TargetPageLayout->addProductDetailBlockPosition($UnusedBlockPosition);
        }
    
        $builder = $app['form.factory']->createBuilder();
    
        $listForm = $builderLayout->getForm();
        
        $listForm->get('layout')->setData($id);
    
        $form = $builder->getForm();
    
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
    
            if ($form->isValid()) {
                // 消す
                foreach ($BlockPositions as $BlockPosition) {
                    if ($BlockPosition->getPageId() == $id || $BlockPosition->getAnywhere() == 0) {
                        $TargetPageLayout->removeProductDetailBlockPosition($BlockPosition);
                        $app['orm.em']->remove($BlockPosition);
                    }
                }
                $app['orm.em']->flush();
    
                // TODO: collection を利用
    
                $data = $request->request->all();
                $max = count($Blocks);
                for ($i = 0; $i < $max; $i++) {
                    // block_id が取得できない場合は INSERT しない
                    if (!isset($data['id_' . $i])) {
                        continue;
                    }
                    // 未使用は INSERT しない
                    if ($data['target_id_' . $i] == \Eccube\Entity\PageLayout::TARGET_ID_UNUSED) {
                        continue;
                    }
                    // 他のページに anywhere が存在する場合は INSERT しない
                    $anywhere = (isset($data['anywhere_' . $i]) && $data['anywhere_' . $i] == 1) ? 1 : 0;
    
                    if (isset($data['anywhere_' . $i]) && $data['anywhere_' . $i] == 1) {
                        $Other = $app['orm.em']->getRepository('\Plugin\SSProductDetailPage\Entity\ProductDetailBlockPosition')
                        ->findBy(array(
                            'anywhere' => 1,
                            'block_id' => $data['id_' . $i],
                        ));
                        //exist and not preview model
                        if (( count($Other) > 0)&&($id)) {
                            continue;
                        }
                    }
    
                    $BlockPosition = new \Plugin\SSProductDetailPage\Entity\ProductDetailBlockPosition();
                    $Block = $app['orm.em']->getRepository('\Plugin\SSProductDetailPage\Entity\Block')
                    ->findOneBy(array(
                        'id' => $data['id_' . $i],
                        'DeviceType' => $DeviceType,
                    ));
                    $BlockPosition
                        ->setPageId($id)
                        ->setBlockId($data['id_' . $i])
                        ->setBlockRow($data['top_' . $i])
                        ->setTargetId($data['target_id_' . $i])
                        ->setBlock($Block)
                        ->setProductDetailLayout($TargetPageLayout)
                        ->setAnywhere($anywhere);
    
                    $TargetPageLayout->addProductDetailBlockPosition($BlockPosition);
                    $app['orm.em']->persist($BlockPosition);
                }
    
                $app['orm.em']->persist($TargetPageLayout);
                $app['orm.em']->flush();
    
    
                if ($this->isPreview) {
                    return $app->redirect($app->url('product_detail') . '?preview=1&category_id=' . $origId);
                } else {
                    $app->addSuccess('admin.register.complete', 'admin');
                    return $app->redirect($app->url('ss_admin_product_detail_layout_edit', array('id' => $id)));
                }
    
            }
    
        }
    
        return $app->render('SSProductDetailPage/Resource/template/admin/layout.twig', array(
            'form' => $form->createView(),
            'list_form' => $listForm->createView(),
            'TargetPageLayout' => $TargetPageLayout,
        ));
    }
    
    public function delete(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);
        
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $app['orm.em'];
        $em->getConnection()->beginTransaction();
        
        $em->getConnection()->executeQuery("DELETE FROM plg_ss_product_detail_block_position WHERE page_id = ?", array($id));
        $em->getConnection()->executeQuery("DELETE FROM plg_ss_product_detail_layout WHERE page_id = ?", array($id));
        
        $em->getConnection()->commit();
        
        $app->addSuccess('レイアウト情報を削除しました。', 'admin');
        return $app->redirect($app->url('admin_product_category_show', ['parent_id' => $id]));
    }
    
    public function preview(Application $app, Request $request, $id)
    {
        $this->isPreview = true;
        return $this->index($app, $request, 0, $id);
    }
}