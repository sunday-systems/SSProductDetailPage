<?php

namespace Plugin\SSProductDetailPage\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductDetailBlockPosition
 */
class ProductDetailBlockPosition extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $page_id;

    /**
     * @var integer
     */
    private $target_id;

    /**
     * @var integer
     */
    private $block_id;

    /**
     * @var integer
     */
    private $block_row;

    /**
     * @var integer
     */
    private $anywhere = '0';

    /**
     * @var \Plugin\SSProductDetailPage\Entity\Block
     */
    private $Block;

    /**
     * @var \Plugin\SSProductDetailPage\Entity\ProductDetailLayout
     */
    private $ProductDetailLayout;


    /**
     * Set page_id
     *
     * @param integer $pageId
     * @return ProductDetailBlockPosition
     */
    public function setPageId($pageId)
    {
        $this->page_id = $pageId;

        return $this;
    }

    /**
     * Get page_id
     *
     * @return integer 
     */
    public function getPageId()
    {
        return $this->page_id;
    }

    /**
     * Set target_id
     *
     * @param integer $targetId
     * @return ProductDetailBlockPosition
     */
    public function setTargetId($targetId)
    {
        $this->target_id = $targetId;

        return $this;
    }

    /**
     * Get target_id
     *
     * @return integer 
     */
    public function getTargetId()
    {
        return $this->target_id;
    }

    /**
     * Set block_id
     *
     * @param integer $blockId
     * @return ProductDetailBlockPosition
     */
    public function setBlockId($blockId)
    {
        $this->block_id = $blockId;

        return $this;
    }

    /**
     * Get block_id
     *
     * @return integer 
     */
    public function getBlockId()
    {
        return $this->block_id;
    }

    /**
     * Set block_row
     *
     * @param integer $blockRow
     * @return ProductDetailBlockPosition
     */
    public function setBlockRow($blockRow)
    {
        $this->block_row = $blockRow;

        return $this;
    }

    /**
     * Get block_row
     *
     * @return integer 
     */
    public function getBlockRow()
    {
        return $this->block_row;
    }

    /**
     * Set anywhere
     *
     * @param integer $anywhere
     * @return ProductDetailBlockPosition
     */
    public function setAnywhere($anywhere)
    {
        $this->anywhere = $anywhere;

        return $this;
    }

    /**
     * Get anywhere
     *
     * @return integer 
     */
    public function getAnywhere()
    {
        return $this->anywhere;
    }

    /**
     * Set Block
     *
     * @param \Plugin\SSProductDetailPage\Entity\Block $block
     * @return ProductDetailBlockPosition
     */
    public function setBlock(\Plugin\SSProductDetailPage\Entity\Block $block)
    {
        $this->Block = $block;

        return $this;
    }

    /**
     * Get Block
     *
     * @return \Plugin\SSProductDetailPage\Entity\Block 
     */
    public function getBlock()
    {
        return $this->Block;
    }

    /**
     * Set ProductDetailLayout
     *
     * @param \Plugin\SSProductDetailPage\Entity\ProductDetailLayout $productDetailLayout
     * @return ProductDetailBlockPosition
     */
    public function setProductDetailLayout(\Plugin\SSProductDetailPage\Entity\ProductDetailLayout $productDetailLayout)
    {
        $this->ProductDetailLayout = $productDetailLayout;

        return $this;
    }

    /**
     * Get ProductDetailLayout
     *
     * @return \Plugin\SSProductDetailPage\Entity\ProductDetailLayout 
     */
    public function getProductDetailLayout()
    {
        return $this->ProductDetailLayout;
    }
}
