<?php
/**
 * Root-level entry point.
 *
 * Ideally your web server's document root points directly at /public
 * (see public/index.php + the sample .htaccess there). On shared hosting
 * where you can't change the document root, this file forwards every
 * request through to the real front controller instead.
 */

chdir(__DIR__ . '/public');
require __DIR__ . '/public/index.php';
