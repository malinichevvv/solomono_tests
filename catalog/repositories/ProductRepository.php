<?php

require_once __DIR__ . '/AbstractRepository.php';
require_once __DIR__ . '/../models/Product.php';

class ProductRepository extends AbstractRepository
{
    private string $table_name = 'products';

    /**
     * @param int|null $category_id
     * @param string $order_by
     * @return array
     */
    public function getAll(?int $category_id = null, string $order_by = 'price_asc'): array
    {
        $where = "";
        if ($category_id) {
            $where = "WHERE p.category_id = :category_id";
        }

        $order = match ($order_by) {
            'price_desc' => "ORDER BY p.price DESC",
            'name_asc' => "ORDER BY p.name ASC",
            'name_desc' => "ORDER BY p.name DESC",
            'date_asc' => "ORDER BY p.created_at ASC",
            'date_desc' => "ORDER BY p.created_at DESC",
            default => "ORDER BY p.price ASC",
        };

        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  " . $where . " 
                  " . $order;

        $stmt = $this->conn->prepare($query);

        if ($category_id) {
            $stmt->bindParam(':category_id', $category_id);
        }

        $result = $stmt->execute();

        if ($result) {
            return $this->hydrateAll($stmt->fetchAll(PDO::FETCH_ASSOC), new Product());
        }

        return [];
    }

    /**
     * @param int $id
     * @return Product
     * @throws Exception
     */
    public function getOne(int $id): Product
    {
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.id = ? LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $this->hydrate($row, new Product());
        }

        throw new Exception("Product not found");
    }
}