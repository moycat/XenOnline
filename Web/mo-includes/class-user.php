<?php
/*
 * mo-includes/class-user.php @ MoyOJ
 *
 * This file provides the classes ralated to the user system.
 *
 * Licensed under GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 */

class User
{
    private $uid;

    private $info;
    private $extra_info;
    private $preference;
    private $stat;

    private $loaded;

    public function __construct($id = null)
    {
        $this->uid = $id;
        if ($id) {
            $this->load();
        }
    }

    public function setUID($id)
    {
        $this->uid = $id;

        return $this->load();
    }

    public function getUID()
    {
        return $this->uid;
    }

    public function get($category, $key)
    {
        if (!$this->loaded) {
            return;
        }
        switch ($category) {
            case 'info':
            return $this->info[$key];
            case 'extra_info':
            return $this->extra_info[$key];
            case 'preference':
            return $this->preference[$key];
            case 'stat':
            return $this->stat[$key];
            default:
            return;
        }
    }

    public function set($category, $key, $value)
    {
        if (!$this->loaded) {
            return;
        }
        switch ($category) {
            case 'info':
            return getset($this->info[$key], $value);
            case 'extra_info':
            return getset($this->extra_info[$key], $value);
            case 'preference':
            return getset($this->preference[$key], $value);
            case 'stat':
            return getset($this->stat[$key], $value);
            default:
            return;
        }
    }

    public function save($category = 'all')
    {
        if (!$this->loaded) {
            return;
        }
        global $db;
        switch ($category) {
            case 'all':
            $this->save('info');
            $this->save('extra_info');
            $this->save('preference');
            $this->save('stat');

            return;
            break;
            case 'info': // Update info
            mo_write_cache_array('mo:user:'.$this->uid.':info', $this->info);
            $sql .= '`mo_user` SET `id` = '.$this->uid;
            $bind = array();
            $bind[0] = '';
            foreach ($this->info as $key => $value) {
                $sql .= ", `$key` = ?";
                $bind[] = $value;
                $bind[0] .= 's';
            }
            $sql .= ' WHERE `id` = '.$this->uid;
            $db->prepare($sql);
            call_user_func_array(array($db, 'bind'), $bind);
            break;
            case 'extra_info': // Update extra info
            mo_write_cache_array('mo:user:'.$this->uid.':extra_info', $this->extra_info);
            $sql .= '`mo_user_extra` SET `info` = ? WHERE `uid` = '.$this->uid;
            $db->prepare($sql);
            $db->bind('s', serialize($this->extra_info));
            break;
            case 'preference':
            mo_write_cache_array('mo:user:'.$this->uid.':preference', $this->preference);
            $sql .= '`mo_user_extra` SET `preference` = ? WHERE `uid` = '.$this->uid;
            $db->prepare($sql);
            $db->bind('s', serialize($this->preference));
            break;
            case 'stat':
            mo_write_cache_array('mo:user:'.$this->uid.':stat', $this->stat);
            $sql .= '`mo_stat_user` SET `uid` = '.$this->uid;
            $bind = array();
            $bind[0] = '';
            foreach ($this->stat as $key => $value) {
                $sql .= ", `$key` = ?";
                $bind[] = $value;
                $bind[0] .= 's';
            }
            $sql .= ' WHERE `uid` = '.$this->uid;
            $db->prepare($sql);
            call_user_func_array(array($db, 'bind'), $bind);
            break;
            default:
            return;
        }
        $db->execute();
        mo_log_user('Information of the user (ID = '.$this->uid.') has been updated.');
    }

    public function login($type, $username = '', $password = '', $cookie_timeout = 0)
    {
        switch ($type) {
            case 'auto':
            if (isset($_SESSION['uid']) && is_numeric($_SESSION['uid'])) { // within a session
                $this->setUID($_SESSION['uid']);
                mo_write_note('Logged in within the session.');
            } elseif (isset($_COOKIE['mo_auth'])) { // session timeout but with cookies
                $this->memAuth($_COOKIE['mo_auth']);
            }
            break;
            case 'check':
            $id = $this->passAuth($username, $password, $cookie_timeout);
            break;
        }
        if ($this->check()) { // check safety
            do_action('login');

            return true;
        } else {
            return false;
        }
    }

    private function memAuth($cookie)
    {
        $input = explode('&', $cookie);
        if (count($input) != 3 || !is_numeric($input[0]) || !is_numeric($input[1])) {
            return false;
        }
        $uid = $input[0];
        $random = $input[1];
        $pass = $input[2];
        if (!$this->setUID($uid)) {
            return false;
        }
        $check = md5($this->info['password'].$random);
        if ($check == $pass) {
            mo_write_note('Logged in with cookies.');
            $_SESSION['uid'] = $this->uid;
            $_SESSION['mask'] = $this->info['mask'];
            $_SESSION['password'] = $this->info['password'];
            mo_log_login($uid, 1);

            return $uid;
        }
        mo_log_login($uid, 1, false);

        return false;
    }

    private function passAuth($username, $password, $cookie_timeout = 0)
    {
        global $db;
        $sql = 'SELECT `id` FROM `mo_user` WHERE `username` = ? LIMIT 1';
        $db->prepare($sql);
        $db->bind('s', $username);
        $result = $db->execute();
        if (!$result || !$this->setUID($result[0]['id']) || !password_verify($password, $this->info['password'])) {
            mo_log_login($this->uid, 0, false);

            return false;
        }
        $_SESSION['uid'] = $this->uid;
        $_SESSION['mask'] = $this->info['mask'];
        $_SESSION['password'] = $this->info['password'];
        if ($cookie_timeout) {
            $random = (string) rand(10000, 99999);
            $cookie_to_write = $this->uid.'&'.$random.'&'.md5($this->info['password'].$random);
            setcookie('mo_auth', $cookie_to_write, time() + $cookie_timeout);
        }
        mo_log_login($this->uid, 0);
        mo_write_note('Logged in with a password.');

        return $this->uid;
    }

    public function check()
    {
        if (!isset($_SESSION['uid'], $_SESSION['mask'], $_SESSION['password'])) {
            return false;
        }
        if (($_SESSION['uid'] != $this->uid) || ($_SESSION['mask'] != $this->info['mask']) ||
            ($_SESSION['password'] != $this->info['password'])) {
            mo_log_user('The user (ID = '.$_SESSION['uid'].') has logged out by force.');
            $this->logout(true);

            return false;
        }

        return true;
    }

    public function logout($forced = false)
    {
        setcookie('mo_auth', '', time() - 3600);
        $uid = $_SESSION['uid'];
        unset($_SESSION['uid']);
        unset($_SESSION['password']);
        unset($_SESSION['mask']);
        if (!$forced) {
            mo_log_user('The user (ID = '.$uid.')has logged out manually.');
            do_action('logout');
        }
    }

    private function load()
    {
        global $db, $mo_user_failed, $mo_now_user;
        if ($this->loaded) {
            $mo_now_user = $this->uid;

            return true;
        }
        if (isset($mo_user_failed[$this->uid]) || $this->uid < 1) {
            $mo_now_user = null;

            return false;
        }
        if (!mo_exist_cache('mo:user:'.$this->uid.':info')) {
            $sql = 'SELECT * FROM `mo_user` WHERE `id` = ? LIMIT 1';
            $db->prepare($sql);
            $db->bind('i', $this->uid);
            $result = $db->execute();
            if (!$result) {
                $mo_user_failed[$this->uid] = true;
                $mo_now_user = null;

                return false;
            }
            $this->info = $result[0];
            if (!$this->info['nickname']) {
                $this->info['nickname'] = $this->info['username'];
            }
            mo_write_cache_array('mo:user:'.$this->uid.':info', $this->info);
        } else {
            $this->info = mo_read_cache_array('mo:user:'.$this->uid.':info');
        }
        if (!mo_exist_cache('mo:user:'.$this->uid.':extra_info') || !mo_exist_cache('mo:user:'.$this->uid.':preference')) {
            $sql = 'SELECT * FROM `mo_user_extra` WHERE `uid` = ? LIMIT 1';
            $db->prepare($sql);
            $db->bind('i', $this->uid);
            $result = $db->execute();
            if (!$result) {
                $mo_user_failed[$this->uid] = true;
                $mo_now_user = null;

                return false;
            }
            $this->extra_info = unserialize($result[0]['info']);
            $this->preference = unserialize($result[0]['preference']);
            mo_write_cache_array('mo:user:'.$this->uid.':extra_info', $this->extra_info);
            mo_write_cache_array('mo:user:'.$this->uid.':preference', $this->preference);
        } else {
            $this->extra_info = mo_read_cache_array('mo:user:'.$this->uid.':extra_info');
            $this->preference = mo_read_cache_array('mo:user:'.$this->uid.':preference');
        }
        if (!mo_exist_cache('mo:user:'.$this->uid.':stat')) {
            $sql = 'SELECT * FROM `mo_stat_user` WHERE `uid` = ? LIMIT 1';
            $db->prepare($sql);
            $db->bind('i', $this->uid);
            $result = $db->execute();
            $this->stat = $result[0];
            mo_write_cache_array('mo:user:'.$this->uid.':stat', $this->stat);
        } else {
            $this->stat = mo_read_cache_array('mo:user:'.$this->uid.':stat');
        }
        $mo_now_user = $this->uid;
        $this->loaded = true;

        return true;
    }

    public function refresh_login()
    {
        global $db;
        $this->info['mask'] = (int) $this->info['mask'] + 1;
        $this->info['mask'] = (string) $this->info['mask'];
        mo_write_cache_array_item('mo:user:'.$this->uid.':info', 'mask', $this->info['mask']);
        $sql = 'UPDATE `mo_user` SET mask = mask+1 WHERE `id` = ?';
        $db->prepare($sql);
        $db->bind('i', $this->uid);
        $db->execute();
        $_SESSION['mask'] = $this->info['mask'];
        mo_log_user('The user (ID = '.$_SESSION['uid'].') has refreshed the saved password.');
    }

    public function is_loaded()
    {
        return $this->loaded;
    }
}
