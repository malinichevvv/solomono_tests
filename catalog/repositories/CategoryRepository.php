<?php

require_once __DIR__ . '/AbstractRepository.php';
require_once __DIR__ . '/../models/Category.php';

class CategoryRepository extends AbstractRepository
{
    private string $table_name = 'categories';

    /**
     * @return array
     */
    public function getAll(): array
    {
        $query = "SELECT c.*, COUNT(p.id) as count_products 
                  FROM " . $this->table_name . " c 
                  LEFT JOIN products p ON c.id = p.category_id 
                  GROUP BY c.id 
                  ORDER BY c.name";

        $stmt = $this->conn->prepare($query);
        $result = $stmt->execute();

        if ($result) {
            return $this->hydrateAll($stmt->fetchAll(PDO::FETCH_ASSOC), new Category());
        }

        return [];
    }

    /**
     * @param int $id
     * @return Category
     * @throws Exception
     */
    public function getOne(int $id): Category
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $this->hydrate($row, new Category());
        }

        throw new Exception("Category not found");
    }
}