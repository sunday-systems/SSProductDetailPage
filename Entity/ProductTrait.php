<?php

namespace Plugin\SSProductDetailPage\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation as Eccube;
use Plugin\SSProductDetailPage\Entity\ProductLayout;

/**
 * @Eccube\EntityExtension("Eccube\Entity\Product")
 */
trait ProductTrait
{
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Plugin\SSProductDetailPage\Entity\ProductLayout", mappedBy="Product", cascade={"persist","remove"})
     */
    private $ProductLayouts;

    /**
     * @return array
     */
    public function getLayouts()
    {
        if ($this->ProductLayouts == null) {
            $this->ProductLayouts = new \Doctrine\Common\Collections\ArrayCollection();
        }

        $Layouts = [];
        foreach ($this->ProductLayouts as $productLayout) {
            $Layouts[] = $productLayout->getLayout();
        }

        return $Layouts;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    public function getProductLayouts()
    {
        if ($this->ProductLayouts == null) {
            $this->ProductLayouts = new \Doctrine\Common\Collections\ArrayCollection();
        }
        return $this->ProductLayouts;
    }

    /**
     * @param ProductLayout $productLayout
     * @return $this
     */
    public function addProductLayout(ProductLayout $productLayout)
    {
        if ($this->ProductLayouts == null) {
            $this->ProductLayouts = new \Doctrine\Common\Collections\ArrayCollection();
        }
        $this->ProductLayouts[] = $productLayout;

        return $this;
    }

    /**
     * @param ProductLayout $productLayout
     * @return $this
     */
    public function removeProductLayout(ProductLayout $productLayout)
    {
        if ($this->ProductLayouts == null) {
            $this->ProductLayouts = new \Doctrine\Common\Collections\ArrayCollection();
        }
        $this->ProductLayouts->removeElement($productLayout);

        return $this;
    }

    /**
     * @param $layoutId
     *
     * @return null|int
     */
    public function getSortNo($layoutId)
    {
        $ProductLayouts = $this->getProductLayouts();

        /** @var ProductLayout $productLayout */
        foreach ($ProductLayouts as $productLayout) {
            if ($productLayout->getLayoutId() == $layoutId) {
                return $productLayout->getSortNo();
            }
        }

        return null;
    }
}
