<?php
add_action("send_headers", function () {
    $v = getenv("PAGE_VERSION") ?: "UNKNOWN";
    header("X-Page-Version: " . $v);
});