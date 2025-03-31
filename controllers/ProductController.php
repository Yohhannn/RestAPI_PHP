<?php


namespace controllers;

use repositories\interface\IProductRepository;
use repositories\ProductRepository;

require_once "repositories/ProductRepository.php";
require_once "repositories/interface/IProductRepository.php";

class ProductController
{
    private IProductRepository $productRepository;

    public function __construct()
    {
        $this->productRepository = new ProductRepository();
    }

    public function GetAllProduct()
    {
        echo json_encode($this->productRepository->GetAllProduct());
    }

    public function GetLatestPriceOfTheProduct()
    {
        echo json_encode($this->productRepository->GetLatestPriceOfTheProduct());
    }

    public function GetProductById($productId)
    {
        echo json_encode($this->productRepository->GetProductById($productId));
    }
}
