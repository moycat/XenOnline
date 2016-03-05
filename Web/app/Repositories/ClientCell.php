<?php

namespace App\Repositories;

use \App\Client;
use Cell;

class ClientCell extends Cell
{
    public function add($name, $intro)
    {
        $client = new Client;
        $client->name = $name;
        $client->intro = $intro;
        $client->load_1 = 0;
        $client->load_5 = 0;
        $client->load_15 = 0;
        $client->memory = 0;
        $client->last_ping = 0;
        $hash = '';
        for($i = 0; $i < 256; ++$i) {
            $hash .= rand(0, 9);
        }
        $hash = md5($hash);
        $client->hash = $hash;
        $client->save();

        return ['id'=>$client->id, 'hash'=>$client->hash];
    }

    public function count()
    {
        return Client::count();
    }

    public function getOn()
    {
        return [];
    }

}