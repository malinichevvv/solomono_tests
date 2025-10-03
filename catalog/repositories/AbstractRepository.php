<?php

abstract class AbstractRepository
{
    /**
     * @var PDO
     */
    protected $conn;

    /**
     * @param $db
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * @param $data
     * @param $object
     * @param array $map
     * @return mixed
     */
    public function hydrate($data, $object, array $map = []): mixed
    {
        foreach ($data as $key => $value) {
            if (property_exists($object, $key)) {
                $object->$key = $value;
            }

            if (isset($map[$key])) {
                $object->{$map[$key]} = $value;
            }
        }

        return $object;
    }

    /**
     * @param $data
     * @param $object
     * @param array $map
     * @return array
     */
    public function hydrateAll($data, $object, array $map = []): array
    {
        return array_map(function ($item) use ($object, $map) {
            return $this->hydrate($item, clone $object, $map);
        }, $data);
    }
}