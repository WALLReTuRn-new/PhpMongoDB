<?php

/**
 * DB
 */

namespace WebSiteToYou\System\Library;

class DB {

    private object $adaptor;

    /**
     * Constructor
     *
     * @param	string	$adaptor
     * @param	string	$hostname
     * @param	string	$username
     * @param	string	$password
     * @param	string	$database
     * @param	int		$port
     *
     */
    public function __construct(string $adaptor, string $hostname, string $database, string $port = '') {
        $class = 'WebSiteToYou\System\Library\DB\\' . $adaptor;

        if (class_exists($class)) {
            $this->adaptor = new $class($hostname, $database, $port);
        } else {
            throw new \Exception('Error: Could not load database adaptor ' . $adaptor . '!');
        }
    }

    public function query(string $collection, $collectionQuery = []): bool|object|array {

        return $this->adaptor->query($collection, $collectionQuery);
    }

    public function insert(string $collection, $collectionQuery = []): mixed {

        return $this->adaptor->insert($collection, $collectionQuery);
    }

    public function insertMany(string $collection, $collectionQuery = []): mixed {

        return $this->adaptor->insert($collection, $collectionQuery);
    }

    public function update(string $collection, $where, $collectionQuery = []): mixed {

        return $this->adaptor->update($collection, $where, $collectionQuery);
    }
    
    public function updateMany(string $collection, $where,$collectionQuery = []): mixed {

        return $this->adaptor->updateMany($collection, $where, $collectionQuery);
    }
    
    public function delete(string $collection, $where = []): mixed {

        return $this->adaptor->delete($collection, $where);
    }
    public function deleteMany(string $collection, $where = []): mixed {

        return $this->adaptor->deleteMany($collection, $where);
    }
    

}
