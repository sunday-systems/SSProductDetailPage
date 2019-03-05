<?php

namespace Plugin\SSProductDetailPage;

use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\Product;
use Eccube\Repository\LayoutRepository;
use Eccube\Request\Context;
use Plugin\SSProductDetailPage\Entity\ProductLayout;
use SunCat\MobileDetectBundle\DeviceDetector\MobileDetector;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Twig\Environment;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RequestContext;

class TemplateLayoutListener implements EventSubscriberInterface
{

    /**
     * @var bool
     */
    protected $initialized = false;

    /**
     * @var RequestContext
     */
    protected $requestContext;

    /**
     * @var Environment
     */
    protected $twig;

    /**
     * @var MobileDetector
     */
    protected $mobileDetector;

    /**
     * @var LayoutRepository
     */
    protected $layoutRepository;

    public function __construct(Environment $twig, Context $requestContext, MobileDetector $mobileDetector, LayoutRepository $layoutRepository)
    {
        $this->requestContext = $requestContext;
        $this->twig = $twig;
        $this->mobileDetector = $mobileDetector;
        $this->layoutRepository = $layoutRepository;
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {

        if ($this->initialized) {
            return;
        }

        if ($this->requestContext->isAdmin()) {
            return;
        }

        $this->initialized = true;

        $request = $event->getRequest();

        /** @var \Symfony\Component\HttpFoundation\ParameterBag $attributes */
        $attributes = $request->attributes;

        if ($attributes->get('_route') == "product_detail") {
            $data = $event->getControllerResult();
            if (array_key_exists('Product', $data) && $data['Product'] != null && $data['Product'] instanceof Product) {
                $type = DeviceType::DEVICE_TYPE_PC;
                if ($this->mobileDetector->isMobile()) {
                    $type = DeviceType::DEVICE_TYPE_MB;
                }

                $Layout = null;
                /** @var ProductLayout $ProductLayout */
                foreach ($data['Product']->getProductLayouts() as $ProductLayout) {
                    if ($ProductLayout->getDeviceTypeId() == $type) {
                        $Layout = $ProductLayout->getLayout();
                        break;
                    }
                }
                if ($Layout) {
                    // lazy loadを制御するため, Layoutを取得しなおす.
                    $Layout = $this->layoutRepository->get($Layout->getId());
                    $this->twig->addGlobal('Layout', $Layout);
                }
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['onKernelView', 1],
            //KernelEvents::RESPONSE => ['onKernelResponse', 1],
        ];
    }
}