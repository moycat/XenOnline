<?php
/**
 * model/Contract/ModelContract.php @ XenOnline
 *
 * The contract of models.
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

namespace Model\Contract;

use MongoDB\BSON\Persistable as Persistable;
use MongoDB\BSON\ObjectID as ObjectID;
use Facade\DB;

abstract class ModelContract implements Persistable {
    protected $_data = [];
    protected $_id = null;
    protected $_modified = [];
    protected $_loaded = false;
    protected $_json_item = [];

    protected $_collection = '';
    protected $_bson_map = [];      // mongodb_name => php_name

    public function getID()
    {
        return $this->_id;
    }

    public function setID($id)
    {
        if (!$id) {
            return false;
        }
        $this->_id = $id;
        return true;
    }

    public function setJsonItem($item)
    {
        if (!is_array($item)) {
            return false;
        }
        $this->_json_item = $item;
        return true;
    }

    /* Save the data to the db */
    public function save($replace = false)
    {
        if (!$this->_data) {
            return false;
        }
        DB::select($this->_collection);

        if (!$this->load || !$this->_id) {  // New model
            DB::insertOne($this);
            $this->_modified = [];
            return true;
        } elseif ($this->_id && $this->_loaded && $this->_modified) {   // Update
            $rs = null;
            if ($replace) {
                $rs = DB::findOneAndReplace(['_id' => $this->_id], $this);
            } else {
                $update = [];
                foreach ($this->_modified as $item) {
                    $update['$set'][] = [$item => $this->_data[$item]];
                }
                $rs = DB::updateOne(['_id' => $this->_id, $update]);
            }
            $this->_modified = [];
            return $rs;
        } else {    // Existing and unmodified
            return false;
        }
    }

    /* Convert this to json format */
    public function toJson()
    {
        $data = array();
        foreach ($this->_json_item as $item) {
            if (!isset($this->_data[$item])) {
                return null;
            }
            $data[$item] = $this->_data[$item];
        }
        return json_encode($data);
    }

    /* Clear the cache */
    abstract public function refreshCache();

    function bsonSerialize()    // From the interface
    {
        $bson_doc = array();
        foreach ($this->_bson_map as $des => $src) {
            $bson_doc[$des] = $this->_data[$src];
        }
        if (!$this->_id) {
            $this->_id = new ObjectID();
        }
        $bson_doc['_id'] = $this->_id;
        return $bson_doc;
    }

    function bsonUnserialize(array $data)   // From the interface
    {
        foreach ($data as $key => $value)
        {
            if (isset($this->_bson_map[$key])) {
                $this->_data[$this->_bson_map[$key]] = $value;
            } else {
                $this->_data[$key] = $value;
            }
        }
        $this->_id = $data['_id'];
    }

    public function __set($name, $value)
    {
        $rt = isset($this->_data[$name]) ? $this->_data[$name] : $value;
        $this->_data[$name] = $value;
        $this->_modified[$name] = true;
        return $rt;
    }

    public function __get($name)
    {
        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        }
        return null;
    }

    public function __toString()
    {
        return $this->toJson();
    }
}