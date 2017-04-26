<?php

class Store_Cart_Item
{
    private $_product = null;

	private $_quantity = 0;

    private $_subTotal = null;
    private $_subTotalV = null;

    public function __construct(Store_Product $product = null, $qty = null)
    {
        $this->_product = $product;
        $this->_quantity = (int)$qty;
        $this->_calculateImporte();
    }

	/**
	 *
	 * @return Store_Product
	 */
    public function getProduct()
    {
        return $this->_product;
    }

	public function setProduct(Store_Product $product)
    {
        $this->_product = $product;
        $this->_calculateImporte();
    }

    public function getId()
    {
        return $this->_product->getId();
    }

    public function setId($value)
    {
        $this->_product->setId((int)$value);
    }

    public function getName()
    {
        return $this->_product->getName();
    }

    public function setName($value)
    {
        $this->_product->setName($value);
    }

    public function getPrice()
    {
        return $this->_product->getPrice();
    }
    public function getPriceV()
    {
        return $this->_product->getPrecioVista();
    }
    public function getPrecioVista()
    {
        return $this->_product->getPrecioVista();
    }
    public function getSimbolo()
    {
        return $this->_product->getSimbolo();
    }

    public function setPrice($value)
    {
        $this->_product->setPrice((double)$value);
    }

    public function getQuantity()
    {
        return $this->_quantity;
    }

    public function setQuantity($value)
    {
        $this->_quantity = (int)$value;
        $this->_calculateImporte();
    }

    public function getWeight()
    {
        return $this->_product->getWeight();
    }

    public function setWeight($value)
    {
        $this->_product->setWeight((double)$value);
    }

    private function _calculateImporte()
    {
        $this->getSubTotal();
    }

    public function getImporte()
    {
        return $this->_subTotal;
    }
    public function getImporteV()
    {
        return $this->_subTotalV;
    }

    public function getSubTotal()
    {
        if ($this->getPrice() != 0 && null !== $this->getPrice()) {
            $this->setSubTotal($this->getQuantity() * $this->getPrice());
            return $this->_subTotal;
        }
        return 0;
    }
    public function getSubTotalV()
    {
        if ($this->getPriceV() != 0 && null !== $this->getPriceV()) {
            $this->setSubTotalV($this->getQuantity() * $this->getPriceV());
            return $this->_subTotalV;
        }
        return 0;
    }

    public function setSubTotal($value)
    {
        $this->_subTotal = (double)$value;
    }
    public function setSubTotalV($value)
    {
        $this->_subTotalV = (double)$value;
    }
            
    public function getOrigen()
    {
        return $this->_product->getOrigen();
    }
}
