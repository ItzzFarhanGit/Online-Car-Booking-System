<?php
// Admin pages share the exact same database connection & schema as the
// main site, so we simply reuse the root db.php (it creates every table,
// including `admin`, and seeds the default Farhan admin account if needed).
require_once __DIR__ . '/../db.php';
?>
