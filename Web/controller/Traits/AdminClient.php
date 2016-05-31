<?php
/**
 * controller/Traits/AdminClient.php @ XenOnline
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

namespace Controller\Traits;

use \Model\Client;
use \Facade\Request;
use \Facade\View;
use \Facade\Site;

trait AdminClient
{
    public function clientList()
    {
        $clients = Client::findMany([])->toArray();

        View::assign('clients', $clients);
        $this->show('admin/client');
    }

    public function clientDelete($id)
    {
        $rs = Client::db()->deleteOne(
            [
                'id' => (int)$id
            ]
        );
        if (!$rs->getDeletedCount()) {
            $this->info('danger', '尝试删除不存在的评测机！', 'session');
        } else {
            $this->info('success', '评测机#'.$id.'删除成功！', 'session');
        }
        Site::go('/admin/client');
    }

    public function clientPost()
    {
        $name = Request::post('name');
        $intro = Request::post('introduction');
        if (!$name || !$intro) {
            $this->info('danger', '请填写评测机名称与简介！', 'session');
            Site::go('/admin/client');
        }
        $new_client = Client::one();
        $new_client['name'] = $name;
        $new_client['intro'] = $intro;
        $new_client->save();
        $this->info('success', '添加评测机成功！请将生成的密钥设置到评测端上。', 'session');
        Site::go('/admin/client');
    }
}