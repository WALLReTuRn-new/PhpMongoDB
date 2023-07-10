<?php

/*
 * WebSiteToYou License
 * Each line should be prefixed with  * 
 */

namespace WebSiteToYou\System\Library\DB;

class MongoDb {

    private object $connection;
    private object $connectionClient;
    private $db;
    private $checkDatabase;

    public function __construct(string $hostname, string $database, string $port = '') {
        if (!$port) {
            $port = '27017';
        }

        $this->db = $database;
        $this->connectionClient = new \MongoDB\Client('mongodb://' . $hostname . ':' . $port);

        try {

            foreach ($this->connectionClient->listDatabases() as $database):

                $this->checkDatabase[] = $database['name'];

            endforeach;

            try {
                $this->checkDatabase();
                $this->connection = new \MongoDB\Driver\Manager('mongodb://' . $hostname . ':' . $port);
            } catch (\Exception $exc) {
                echo $exc->getMessage();
            }
        } catch (\MongoDB\Exception\Exception $e) {
            echo "tes:", $e->getMessage(), "\n";
        } catch (\MongoDB\Driver\Exception\AuthenticationException $e) {
            echo "AuthenticationException:", $e->getMessage(), "\n";
        } catch (\MongoDB\Driver\Exception\ConnectionException $e) {
            echo "ConnectionException:", $e->getMessage(), "\n";
        } catch (\MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            echo "ConnectionTimeoutException:", $e->getMessage(), "\n";
        } catch (\MongoDB\Exception\Exception $e) {
            echo "Exception:", $e->getMessage(), "\n";
        }
    }

    private function checkDatabase() {
        if (!in_array($this->db, $this->checkDatabase)):
            throw new \Exception('Exception: Please Check Database (' . $this->db . ') database no exist;');
        endif;
    }

    public function query(string $collection, $collectionQuery = []): bool|object|array {


        try {
            $db = $this->db;
            $query = $this->connectionClient->$db->$collection->find($collectionQuery)->toArray();

            if ($query):
                $data1 = [];
                $data = [];
                foreach ($query as $row):

                    $data1[] = (array) $row;

                endforeach;

                foreach ($data1 as $key => $data2):

                    //Remove MongoDb id
                    unset($data2['_id']);
                    $data[] = $data2;
                endforeach;

                $result = new \stdClass();
                $result->num_rows = count($data);
                $result->row = isset($data[0]) ? $data[0] : [];
                $result->rows = $data;

                unset($data);

                return $result;
            else:
                echo $this->error($collection);
                return false;
            endif;
        } catch (\MongoDB\Exception\Exception $e) {

            throw new \Exception('Error: ' . $e->getMessage() . '<br/>Error No: ' . $e->getMessage() . '<br/>' . $collection);
        }



        return $result;
    }

    public function insert(string $collection, $collectionQuery = []): mixed {

        try {
            $options = ['sort' => ['id' => -1]]; // -1 is for DESC

            $db = $this->db;

            $maxId = $this->connectionClient->$db->$collection->findOne([], $options);

            if (isset($maxId['id'])):
                $id = ['id' => $maxId['id'] + 1];
            else:
                $id = ['id' => 1];
            endif;

            $arrays = array_merge($id, $collectionQuery);

            $this->connectionClient->$db->$collection->insertOne($arrays);

            $query = $this->connectionClient->$db->$collection->findOne([], $options);

            return $query['id'];
        } catch (\MongoDB\Exception\Exception $e) {
            throw new \Exception('Error: ' . $e->getMessage() . '<br/>Error No: ' . $e->getMessage() . '<br/>' . $collection);
        }

        return $query['id'];
    }

    public function insertMany(string $collection, $collectionQuery = []): mixed {

        try {
            $options = ['sort' => ['id' => -1]]; // -1 is for DESC

            $db = $this->db;

            $maxId = $this->connectionClient->$db->$collection->findOne([], $options);

            if (isset($maxId['id'])):
                $id = ['id' => $maxId['id'] + 1];
            else:
                $id = ['id' => 1];
            endif;

            $arrays = array_merge($id, $collectionQuery);

            $this->connectionClient->$db->$collection->insertOne($arrays);

            $query = $this->connectionClient->$db->$collection->findOne([], $options);

            return $query['id'];
        } catch (\MongoDB\Exception\Exception $e) {
            throw new \Exception('Error: ' . $e->getMessage() . '<br/>Error No: ' . $e->getMessage() . '<br/>' . $collection);
        }

        return $query['id'];
    }

    public function deleteMany(string $collection, $where = []): mixed {
        try {
            $db = $this->db;
            $result = $this->connectionClient->$db->$collection->deleteMany($where);
            return 'delete one';
        } catch (\MongoDB\Exception\Exception $e) {
            return 'No delete one';
            throw new \Exception('Error: ' . $e->getMessage() . '<br/>Error No: ' . $e->getMessage() . '<br/>' . $collection);
        }
        return 'delete one';
    }

    public function delete(string $collection, $where = []): mixed {
        try {
            $db = $this->db;
            $result = $this->connectionClient->$db->$collection->deleteOne($where);
            return 'delete one';
        } catch (\MongoDB\Exception\Exception $e) {
            return 'No delete one';
            throw new \Exception('Error: ' . $e->getMessage() . '<br/>Error No: ' . $e->getMessage() . '<br/>' . $collection);
        }
        return 'delete one';
    }

    public function updateMany(string $collection, $where, $collectionQuery = []): mixed {

        $db = $this->db;
        $bulk = new \MongoDB\Driver\BulkWrite;
        $bulk->update(
                $where,
                $collectionQuery,
                ['multi' => true, 'upsert' => false]
        );

        try {
            $result = $this->connection->executeBulkWrite($db . '.' . $collection, $bulk);
            return 'update many';
        } catch (\MongoDB\Exception\Exception $e) {
            return $result = 'No update2';
            throw new \Exception('Error: ' . $e->getMessage() . '<br/>Error No: ' . $e->getMessage() . '<br/>' . $collection);
        }
        return 'update many';
    }

    public function update(string $collection, $where, $collectionQuery = []): mixed {
        try {

            $db = $this->db;
            $update = $this->connectionClient->$db->$collection->updateOne($where, $collectionQuery);

            return $result = 'update';
        } catch (\MongoDB\Exception\Exception $e) {
            return $result = 'No update2';
            throw new \Exception('Error: ' . $e->getMessage() . '<br/>Error No: ' . $e->getMessage() . '<br/>' . $collection);
        }
        return $result = 'update';
    }

    public function error($collection) {
        return 'Error With this Query (' . $collection . ')';
    }

}
