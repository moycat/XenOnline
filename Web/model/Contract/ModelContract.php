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

use ArrayAccess;
use MongoDB\BSON\Persistable as Persistable;
use Facade\Site;
use Facade\DB;

abstract class ModelContract implements Persistable, ArrayAccess {

    protected $_data = [];          // Data wrapper
    protected $_id = null;          // ObjectID
    protected $_modified = [];      // Items modified
    protected $_loaded = false;     // Flag if loaded
    protected $_json_item = [];     // Items to json
    protected $_collection = '';    // *Collection name

    /* Clear the cache */
    abstract public function refreshCache();

    /* Callbacks */
    protected function onZip(&$doc) {}
    protected function onExtract(&$data) {}

    public function getID()
    {
        return (string)$this->_id;
    }

    public function setID($id)
    {
        if (!$id) {
            return false;
        }
        $this->_id = Site::ObjectID($id);
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

        if (!$this->_loaded) {  // New model
            DB::insertOne($this);
            $this->_modified = [];
            return true;
        } elseif ($this->_loaded && $this->_modified) {   // Existing model
            $replace ? $this->_replace() : $this->_update();
            $this->_modified = [];
            return true;
        } else {    // Unmodified
            return false;
        }
    }

    protected function _update()
    {
        $update = []; // Constuct a query
        foreach ($this->_modified as $item => $_) {
            if (isset($this->_data[$item])) {
                $update['$set'][$item] = $this->_data[$item];
            } else {
                $update['$unset'][$item] = '';
            }
        }
        if (isset($update['$set'])) {
            $this->onZip($update['$set']);
        }
        return DB::updateOne(['_id' => $this->_id], $update);
    }

    protected function _replace()
    {
        return DB::findOneAndReplace(['_id' => $this->_id], $this);
    }

    /* Convert this to json format */
    public function toJson()
    {
        $data = array();
        foreach ($this->_json_item as $item) {
            $data[$item] = isset($this->_data[$item]) ?
                                        $this->_data[$item] : null;
        }
        return json_encode($data);
    }

    /* Forward to operation on this model directly */
    public function __call($name, $arg)
    {
        if (!$this->_loaded) {
            return false;
        }
        $query = array_merge([['_id' => $this->_id]], $arg);
        DB::select($this->_collection);
        return call_user_func_array('\Facade\DB::'.$name, $query);
    }

    function bsonSerialize()    // From the interface
    {
        $bson_doc = array();
        foreach ($this->_data as $key => $value) {
            $bson_doc[$key] = $value;
        }
        if (!$this->_id) {
            $this->_id = Site::ObjectID();
        }
        $bson_doc['_id'] = $this->_id;
        $this->onZip($bson_doc);
        return $bson_doc;
    }

    function bsonUnserialize(array $data)   // From the interface
    {
        $this->onExtract($data);
        foreach ($data as $key => $value)
        {
            $this->_data[$key] = $value;
        }
        $this->_id = $data['_id'];
        $this->_loaded = true;
    }

    public function offsetExists($offset)
    {
        return (isset($this->_data[$offset]));
    }

    public function offsetSet($name, $value)
    {
        $this->_data[$name] = $value;
        $this->_modified[$name] = true;
    }

    public function offsetGet($name)
    {
        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        }
        return null;
    }

    public function offsetUnset($name)
    {
        unset($this->_data[$name]);
        $this->_modified[$name] = true;
    }

    public function __toString()
    {
        return $this->toJson();
    }
}