<?php

namespace Plugin\SSProductDetailPage\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation as Eccube;

/**
 * @Eccube\EntityExtension("Eccube\Entity\Layout")
 */
trait LayoutTrait {
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="\Plugin\SSProductDetailPage\Entity\ProductLayout", mappedBy="Layout", cascade={"persist","remove"})
     * @ORM\OrderBy({"sort_no" = "ASC"})
     */
    private $ProductLayouts;

    public function addProductLayout(ProductLayout $ProductLayout)
    {
        if ($this->ProductLayouts == null) {
            $this->ProductLayouts = new \Doctrine\Common\Collections\ArrayCollection();
        }
        $this->ProductLayouts[] = $ProductLayout;

        return $this;
    }

    public function removeProductLayout(ProductLayout $ProductLayout)
    {
        if ($this->ProductLayouts == null) {
            $this->ProductLayouts = new \Doctrine\Common\Collections\ArrayCollection();
        }
        $this->ProductLayouts->removeElement($ProductLayout);
    }

    /**
     * Get ProductLayoutLayouts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductLayouts()
    {
        if ($this->ProductLayouts == null) {
            $this->ProductLayouts = new \Doctrine\Common\Collections\ArrayCollection();
        }
        return $this->ProductLayouts;
    }

    /**
     * Check layout can delete or not
     *
     * @return boolean
     */
    public function isDeletable()
    {
        if (!$this->getPageLayouts()->isEmpty()) {
            return false;
        }

        if (!$this->getProductLayouts()->isEmpty()) {
            return false;
        }

        return true;
    }
}