<?php

namespace Plugin\SSProductDetailPage\Form\Extension;

use Doctrine\ORM\EntityRepository;
use Eccube\Entity\Layout;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\Product;
use Eccube\Form\Type\Admin\ProductType;
use Eccube\Repository\Master\DeviceTypeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ProductTypeExtension extends AbstractTypeExtension
{
    /**
     * @var DeviceTypeRepository
     */
    protected $deviceTypeRepository;

    /**
     * CategoryTypeExtension constructor.
     * @param DeviceTypeRepository $deviceTypeRepository
     */
    public function __construct(DeviceTypeRepository $deviceTypeRepository)
    {
        $this->deviceTypeRepository = $deviceTypeRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('PcLayout', EntityType::class, [
                'mapped' => false,
                'placeholder' => '---',
                'required' => false,
                'class' => Layout::class,
                'query_builder' => function (EntityRepository $er) {
                    $DeviceType = $this->deviceTypeRepository->find(DeviceType::DEVICE_TYPE_PC);

                    return $er->createQueryBuilder('l')
                        ->where('l.id != :DefaultLayoutPreviewPage')
                        ->andWhere('l.DeviceType = :DeviceType')
                        ->setParameter('DeviceType', $DeviceType)
                        ->setParameter('DefaultLayoutPreviewPage', Layout::DEFAULT_LAYOUT_PREVIEW_PAGE)
                        ->orderBy('l.id', 'DESC');
                },
            ])->add('SpLayout', EntityType::class, [
                'mapped' => false,
                'placeholder' => '---',
                'required' => false,
                'class' => Layout::class,
                'query_builder' => function (EntityRepository $er) {
                    $DeviceType = $this->deviceTypeRepository->find(DeviceType::DEVICE_TYPE_MB);

                    return $er->createQueryBuilder('l')
                        ->where('l.id != :DefaultLayoutPreviewPage')
                        ->andWhere('l.DeviceType = :DeviceType')
                        ->setParameter('DeviceType', $DeviceType)
                        ->setParameter('DefaultLayoutPreviewPage', Layout::DEFAULT_LAYOUT_PREVIEW_PAGE)
                        ->orderBy('l.id', 'DESC');
                },
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                /** @var Product $Product */
                $Product = $event->getData();
                if (is_null($Product->getId())) {
                    return;
                }
                $form = $event->getForm();
                $Layouts = $Product->getLayouts();
                foreach ($Layouts as $Layout) {
                    if ($Layout->getDeviceType()->getId() == DeviceType::DEVICE_TYPE_PC) {
                        $form['PcLayout']->setData($Layout);
                    }
                    if ($Layout->getDeviceType()->getId() == DeviceType::DEVICE_TYPE_MB) {
                        $form['SpLayout']->setData($Layout);
                    }
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return ProductType::class;
    }

    /**
     * product admin form name.
     *
     * @return string[]
     */
    public static function getExtendedTypes(): iterable
    {
        yield ProductType::class;
    }
}