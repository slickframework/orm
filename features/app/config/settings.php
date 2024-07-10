<?php

/**
 * This file is part of orm
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Config;

$settings = [];

$settings['environment'] = getenv('APP_ENV');

return $settings;
