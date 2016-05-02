<?php
/**
 * public/index.php @ XenOnline
 *
 * This file is the entrance to the site.
 *
 * Authored by Moycat <moycat@makedie.net>
 * Licensed under GPLv2, see file LICENSE in this source tree.
 */

require_once '../config/config.php';

use \Facade\Site;

// Process the process :)
Site::init();