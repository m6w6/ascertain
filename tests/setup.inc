<?php

spl_autoload_register(function ($c) {
	if (substr($c, 0, 10) === "ascertain\\") require_once sprintf("%s/../lib/%s.php", __DIR__, strtr($c, "\\", "/"));
});
