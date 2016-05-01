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

abstract class ModelContract {
    protected $_data = [];
    protected $_id = null;
    protected $_modified = [];
    protected $_loaded = false;
    protected $_json_item = [];

    public function __construct($id = null)
    {
        $this->id = $id;
        $this->load($id);
    }

    public function getID()
    {
        return $this->id;
    }

    public function setID($id)
    {
        if (!$id) {
            return false;
        }
        $this->id = $id;
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
    abstract public function save();

    /* Convert this to json format */
    abstract public function toJson();

    /* Clear the cache */
    abstract public function refreshCache();

    /* Load this from the db or cache */
    abstract protected function load($id);

    public function __set($name, $value)
    {
        if (isset($this->_data[$name])) {
            $old = $this->_data[$name];
            $this->_data[$name] = $value;
            return $old;
        }
        $this->_data[$name] = $value;
        return $value;
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