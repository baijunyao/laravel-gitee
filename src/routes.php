<?php

Route::match(['get', 'post'], 'gitee/hook', '\Baijunyao\LaravelGitee\Controller\GiteeController@pull');