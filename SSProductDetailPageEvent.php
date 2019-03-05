<?php

namespace Plugin\SSProductDetailPage;

use Doctrine\ORM\EntityManager;
use Eccube\Entity\Layout;
use Eccube\Entity\Product;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Event\TemplateEvent;
use Plugin\SSProductDetailPage\Entity\ProductLayout;
use Plugin\SSProductDetailPage\Repository\ProductLayoutRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SSProductDetailPageEvent implements EventSubscriberInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ProductLayoutRepository
     */
    protected $productLayoutRepository;

    public function __construct(EntityManager $entityManager, ProductLayoutRepository $productLayoutRepository)
    {
        $this->entityManager = $entityManager;
        $this->productLayoutRepository = $productLayoutRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            '@admin/Product/product.twig' => ['onTemplateAdminProduct', 10],
            EccubeEvents::ADMIN_PRODUCT_EDIT_COMPLETE => ['onAdminProductEditComplete', 10],
            EccubeEvents::ADMIN_PRODUCT_DELETE_COMPLETE => ['onAdminProductDeleteComplete', 10],
            EccubeEvents::ADMIN_PRODUCT_COPY_COMPLETE => ['onAdminProductCopyComplete', 10],
        ];
    }

    public function onTemplateAdminProduct(TemplateEvent $templateEvent)
    {
        $templateEvent->addSnippet('@SSProductDetailPage/admin/Product/product.twig');
    }

    /**
     * @param EventArgs $eventArgs
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function onAdminProductEditComplete(EventArgs $eventArgs)
    {
        /** @var \Symfony\Component\Form\Form $form */
        /** @var \Eccube\Entity\Product $Product */
        $form = $eventArgs->getArgument('form');
        $Product = $eventArgs->getArgument('Product');

        /** @var ProductLayout $productLayout */
        foreach ($Product->getProductLayouts() as $productLayout) {
            $Product->removeProductLayout($productLayout);
            $this->entityManager->remove($productLayout);
            $this->entityManager->flush($productLayout);
        }

        /** @var Layout $Layout */
        $Layout = $form['PcLayout']->getData();
        $LastPageLayout = $this->productLayoutRepository->findOneBy([], ['sort_no' => 'DESC']);
        if ($LastPageLayout == null) {
            $sortNo = 0;
        } else {
            $sortNo = $LastPageLayout->getSortNo();
        }

        if ($Layout) {
            $PageLayout = new ProductLayout();
            $PageLayout->setLayoutId($Layout->getId());
            $PageLayout->setLayout($Layout);
            $PageLayout->setProductId($Product->getId());
            $PageLayout->setSortNo($sortNo++);
            $PageLayout->setProduct($Product);

            $this->entityManager->persist($PageLayout);
            $this->entityManager->flush($PageLayout);

            $Product->addProductLayout($PageLayout);
        }

        $Layout = $form['SpLayout']->getData();
        if ($Layout) {
            $PageLayout = new ProductLayout();
            $PageLayout->setLayoutId($Layout->getId());
            $PageLayout->setLayout($Layout);
            $PageLayout->setProductId($Product->getId());
            $PageLayout->setSortNo($sortNo++);
            $PageLayout->setProduct($Product);

            $this->entityManager->persist($PageLayout);
            $this->entityManager->flush($PageLayout);

            $Product->addProductLayout($PageLayout);
        }

        $this->entityManager->persist($Product);
        $this->entityManager->flush($Product);
    }

    /**
     * @param EventArgs $eventArgs
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function onAdminProductDeleteComplete(EventArgs $eventArgs)
    {

    }

    /**
     * @param EventArgs $eventArgs
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function onAdminProductCopyComplete(EventArgs $eventArgs)
    {
        /** @var Product $Product */
        $Product = $eventArgs->getArgument('Product');
        /** @var Product $CopyProduct */
        $CopyProduct = $eventArgs->getArgument('CopyProduct');

        /** @var ProductLayout $productLayout */
        foreach ($Product->getProductLayouts() as $productLayout) {
            $CopyProductLayout = clone $productLayout;
            $CopyProductLayout->setProductId($CopyProduct->getId());
            $CopyProductLayout->setProduct($CopyProduct);
            $CopyProduct->addProductLayout($CopyProductLayout);

            $this->entityManager->persist($CopyProductLayout);
        }
        $this->entityManager->persist($CopyProduct);
        $this->entityManager->flush();
    }
}