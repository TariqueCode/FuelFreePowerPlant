# CMS editor source audit

The Site Content CMS editor is rendered by `resources/views/admin/site-content/create.blade.php` through `SiteContentController::create()` and `edit()`. This file exists to trigger the verified source-level correction after the implementation audit.
