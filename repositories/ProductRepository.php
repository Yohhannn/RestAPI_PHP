<?php

namespace repositories;

use config\Database;
use repositories\interface\IProductRepository;

require_once "config/Database.php";
require_once "repositories/interface/IProductRepository.php";

class ProductRepository implements IProductRepository
{
    private $databaseConnection;
    private Database $database;

    public function __construct()
    {
        $this->database = Database::getInstance();
        $this->databaseConnection = $this->database->getConnection();
    }

    public function GetAllProduct()
    {
        $query = "SELECT 
                    Product.ProductId,
                    Product.ProductName,
                    ProductDetails.ProductPrice,
                    ProductDetails.ProductDate
                  FROM Product 
                  INNER JOIN ProductDetails ON ProductDetails.ProductId = Product.ProductId
                  ORDER BY Product.ProductId, ProductDetails.ProductDate DESC";

        return $this->ExecuteSqlQuery($query, []);
    }

    public function GetLatestPriceOfTheProduct()
    {
        $query = "SELECT 
                    p.ProductId,
                    p.ProductName,
                    pd.ProductPrice,
                    pd.ProductDate
                  FROM Product p
                  INNER JOIN (
                      SELECT 
                          ProductId,
                          MAX(ProductDate) as LatestDate
                      FROM ProductDetails
                      GROUP BY ProductId
                  ) latest ON p.ProductId = latest.ProductId
                  INNER JOIN ProductDetails pd ON pd.ProductId = latest.ProductId AND pd.ProductDate = latest.LatestDate";

        return $this->ExecuteSqlQuery($query, []);
    }

    public function GetProductById($productId)
    {
        $query = "SELECT 
                    Product.ProductId,
                    Product.ProductName,
                    ProductDetails.ProductPrice,
                    ProductDetails.ProductDate
                  FROM Product 
                  INNER JOIN ProductDetails ON ProductDetails.ProductId = Product.ProductId
                  WHERE Product.ProductId = :productId";

        $params = [
            ':productId' => $productId
        ];

        return $this->ExecuteSqlQuery($query, $params);
    }

    private function ExecuteSqlQuery(string $query, array $params)
    {
        $statementObject = $this->databaseConnection->prepare($query);
        $statementObject->execute($params);

        if (stripos($query, "SELECT") === 0) {
            return $statementObject->fetchAll(PDO::FETCH_ASSOC);
        }

        return null;
    }
}

