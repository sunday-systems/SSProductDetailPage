<?php

namespace Plugin\SSProductDetailPage\Entity;

use Eccube\Entity\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation as Eccube;

/**
 * PageLayout
 *
 * @ORM\Table(name="dtb_product_layout")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Plugin\SSProductDetailPage\Repository\ProductLayoutRepository")
 */
class ProductLayout extends AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="product_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $product_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="layout_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $layout_id;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_no", type="smallint", options={"unsigned":true})
     */
    private $sort_no;

    /**
     * @var \Eccube\Entity\Product
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Product", inversedBy="ProductLayouts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     * })
     */
    private $Product;

    /**
     * @var \Eccube\Entity\Layout
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Layout", inversedBy="ProductLayouts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="layout_id", referencedColumnName="id")
     * })
     */
    private $Layout;

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * @param int $product_id
     * @return ProductLayout
     */
    public function setProductId(int $product_id)
    {
        $this->product_id = $product_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getLayoutId()
    {
        return $this->layout_id;
    }

    /**
     * @param int $layout_id
     * @return ProductLayout
     */
    public function setLayoutId(int $layout_id)
    {
        $this->layout_id = $layout_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getSortNo()
    {
        return $this->sort_no;
    }

    /**
     * @param int $sort_no
     * @return ProductLayout
     */
    public function setSortNo(int $sort_no)
    {
        $this->sort_no = $sort_no;
        return $this;
    }

    /**
     * @return \Eccube\Entity\Product
     */
    public function getProduct()
    {
        return $this->Product;
    }

    /**
     * @param \Eccube\Entity\Product $Product
     * @return ProductLayout
     */
    public function setProduct(\Eccube\Entity\Product $Product)
    {
        $this->Product = $Product;
        return $this;
    }

    /**
     * @return \Eccube\Entity\Layout
     */
    public function getLayout()
    {
        return $this->Layout;
    }

    /**
     * @param \Eccube\Entity\Layout $Layout
     * @return ProductLayout
     */
    public function setLayout(\Eccube\Entity\Layout $Layout)
    {
        $this->Layout = $Layout;
        return $this;
    }

    /**
     * DeviceTypeがあればDeviceTypeIdを返す
     * DeviceTypeがなければnullを返す
     *
     * @return int|null
     */
    public function getDeviceTypeId()
    {
        if ($this->Layout->getDeviceType()) {
            return $this->Layout->getDeviceType()->getId();
        }

        return null;
    }
}