<?php
foreach(glob('library/*/*.php') as $file) {
    require_once($file);
}

foreach(glob('library/*/*/*.php') as $file) {
    require_once($file);
}