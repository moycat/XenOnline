<?php

function mo_load_settings()
{
    global $mo_settings;
    $mo_settings = mo_read_cache('mo_cache_settings');
    if (!$mo_settings) {
        global $db;
        $sql = 'SELECT * FROM `mo_site_options`';
        $db->prepare($sql);
        $result = $db->execute();
        foreach ($result as $value) {
            $mo_settings[$value['item']] = $value['value'];
        }
        mo_write_cache('mo_cache_settings', $mo_settings);
    }
    mo_write_note('Site settings have been loaded.');

    return $mo_settings;
}

function mo_get_option($option)
{
    global $mo_settings;
    if (isset($mo_settings[$option])) {
        if (is_serialized($mo_settings[$option])) {
            $mo_settings[$option] = unserialize($mo_settings[$option]);
        }

        return $mo_settings[$option];
    } else {
        return;
    }
}

function mo_set_option($option, $data)
{
    global $mo_settings, $db;
    if (is_array($data) || is_object($data)) {
        $to_write = serialize($data);
    } else {
        $to_write = $data;
    }
    if (isset($mo_settings[$option])) {
        $rt = $mo_settings[$option];
        $mo_settings[$option] = $data;
        $sql = 'UPDATE `mo_site_options` SET `value` = ? WHERE `item` = ?';
        $db->prepare($sql);
        $db->bind('ss', $to_write, $option);
    } else {
        $rt = true;
        $mo_settings[$option] = $data;
        $sql = 'INSERT INTO `mo_site_options` (`item`, `value`) VALUES (?, ?)';
        $db->prepare($sql);
        $db->bind('ss', $option, $to_write);
    }
    $db->execute();
    mo_write_cache('mo_cache_settings', $mo_settings);
    mo_write_note("Site option: '$option' has been update.");

    return $rt;
}
