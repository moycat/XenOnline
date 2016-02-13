<?php
    /*
     * mo-api/client/db.php @ MoyOJ
     * 
     * This file provides the classes ralated to database operations.
     * For client api only.
     * 
     */

    class DB
    {
        private $host = 'localhost';
        private $name = 'moyoj';
        private $user = 'moyoj';
        private $pass = 'moyoj';

        private $mysqli = null;
        private $query = array();
        private $insID;

        private $count = 0;

        public function init($db_host, $db_user, $db_pass, $db_name)
        {
            $this->host = $db_host;
            $this->name = $db_name;
            $this->user = $db_user;
            $this->pass = $db_pass;
        }
        public function connect()
        {
            $this->mysqli = new mysqli($this->host, $this->user, $this->pass, $this->name);
            if (mysqli_connect_errno()) {
                $error = mysqli_connect_errno();
                p("Error Connecting to the Database #$error");

                return false;
            }
            $this->mysqli->set_charset('utf8');
            p('Connected to the database successfully.');

            return true;
        }
        public function prepare($sql)
        {
            $mark = rand(10000, 99999);
            while (isset($this->query[$mark])) {
                $mark = rand(10000, 99999);
            }
            $this->query[$mark] = $this->mysqli->prepare($sql);

            return $this->query[$mark] ? $mark : false;
        }
        public function bind()
        {
            $input = func_get_args();
            $cnt = count($input);
            if ($cnt < 3) {
                throw new Exception('Bad Binding!');

                return;
            }
            $mark = (int) $input[0];
            for ($i = 0; $i < $cnt - 1; ++$i) {
                $input[$i] = &$input[$i + 1];
            }
            unset($input[$cnt - 1]);
            call_user_func_array(array($this->query[$mark], 'bind_param'), $input);
        }
        public function execute($mark)
        {
            $this->query[$mark]->execute();
            ++$this->count;
            $this->insID = $this->query[$mark]->insert_id;
            if (!$this->query[$mark]->field_count) {
                $this->query[$mark]->close();

                return 0;
            }
            $result = array();
            $meta = $this->query[$mark]->result_metadata();
            while ($field = $meta->fetch_field()) {
                $params[] = &$row[$field->name];
            }
            $this->query[$mark]->store_result();
            call_user_func_array(array($this->query[$mark], 'bind_result'), $params);
            while ($this->query[$mark]->fetch()) {
                foreach ($row as $key => $val) {
                    $c[$key] = $val;
                }
                $result[] = $c;
            }
            $this->query[$mark]->free_result();
            $this->query[$mark]->close();
            unset($this->query[$mark]);

            return $result;
        }

        public function getCount()
        {
            return $this->count;
        }
        public function getInsID()
        {
            return $this->insID;
        }
    }
