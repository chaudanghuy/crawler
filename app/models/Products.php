<?php

class Products extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(column="id", type="integer", length=11, nullable=false)
     */
    public $id;

    /**
     *
     * @var integer
     * @Column(column="category_id", type="integer", length=11, nullable=true)
     */
    public $category_id;

    /**
     *
     * @var integer
     * @Column(column="sub_category_id", type="integer", length=11, nullable=true)
     */
    public $sub_category_id;

    /**
     *
     * @var integer
     * @Column(column="trademark_id", type="integer", length=11, nullable=false)
     */
    public $trademark_id;

    /**
     *
     * @var string
     * @Column(column="sku", type="string", length=32, nullable=true)
     */
    public $sku;

    /**
     *
     * @var string
     * @Column(column="name", type="string", length=255, nullable=true)
     */
    public $name;

    /**
     *
     * @var string
     * @Column(column="slug", type="string", length=255, nullable=false)
     */
    public $slug;

    /**
     *
     * @var integer
     * @Column(column="total_bought", type="integer", length=10, nullable=false)
     */
    public $total_bought;

    /**
     *
     * @var string
     * @Column(column="model", type="string", length=64, nullable=false)
     */
    public $model;

    /**
     *
     * @var string
     * @Column(column="description", type="string", nullable=true)
     */
    public $description;

    /**
     *
     * @var string
     * @Column(column="shipping", type="string", length=55, nullable=false)
     */
    public $shipping;

    /**
     *
     * @var string
     * @Column(column="buy_price", type="string", nullable=true)
     */
    public $buy_price;

    /**
     *
     * @var string
     * @Column(column="sell_price", type="string", nullable=true)
     */
    public $sell_price;

    /**
     *
     * @var string
     * @Column(column="calc_price", type="string", length=20, nullable=false)
     */
    public $calc_price;

    /**
     *
     * @var string
     * @Column(column="discount_endtime", type="string", length=55, nullable=false)
     */
    public $discount_endtime;

    /**
     *
     * @var string
     * @Column(column="discount_percent", type="string", length=10, nullable=false)
     */
    public $discount_percent;

    /**
     *
     * @var integer
     * @Column(column="units_in_stock", type="integer", length=11, nullable=false)
     */
    public $units_in_stock;

    /**
     *
     * @var string
     * @Column(column="size", type="string", length=64, nullable=true)
     */
    public $size;

    /**
     *
     * @var string
     * @Column(column="color", type="string", length=64, nullable=true)
     */
    public $color;

    /**
     *
     * @var string
     * @Column(column="weight", type="string", nullable=true)
     */
    public $weight;

    /**
     *
     * @var integer
     * @Column(column="rating", type="integer", length=11, nullable=true)
     */
    public $rating;

    /**
     *
     * @var string
     * @Column(column="thumb", type="string", length=255, nullable=true)
     */
    public $thumb;

    /**
     *
     * @var string
     * @Column(column="created", type="string", nullable=false)
     */
    public $created;

    /**
     *
     * @var string
     * @Column(column="modified", type="string", nullable=false)
     */
    public $modified;

    /**
     *
     * @var string
     * @Column(column="crawl_url", type="string", length=255, nullable=false)
     */
    public $crawl_url;

    /**
     *
     * @var string
     * @Column(column="crawl_info_url", type="string", length=255, nullable=false)
     */
    public $crawl_info_url;

    /**
     *
     * @var string
     * @Column(column="crawl_detail_url", type="string", length=255, nullable=false)
     */
    public $crawl_detail_url;

    /**
     *
     * @var string
     * @Column(column="crawl_status", type="string", nullable=false)
     */
    public $crawl_status;

    /**
     *
     * @var integer
     * @Column(column="status", type="integer", length=1, nullable=false)
     */
    public $status;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("wowmua");
        $this->setSource("products");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'products';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Products[]|Products|\Phalcon\Mvc\Model\ResultSetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Products|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
