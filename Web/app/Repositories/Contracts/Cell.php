<?php
namespace App\Repositories\Contracts;

interface Protein {
    // Yes, this is a useless class.
    // However, cells are made up of protein, aren't they?
}

abstract class Cell implements Protein {
    public function oidToTimestamp($oid) {
        return hexdec(substr((string) $oid, 0, 8));
    }
}