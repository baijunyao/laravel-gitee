<?php

namespace Baijunyao\LaravelGitee\Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GiteeController extends Controller
{
    /**
     * 接受 git merge 到 master 后的 hook 事件
     *
     * @param Request $request
     */
    public function pull(Request $request)
    {
        $data = $request->all();
        if ($data['password'] === env('GITEE_HOOK_PASSWORD') && 'merged' === $data['state']) {
            $basePath = base_path();
            // 回滚
            $reset = <<<EOF
cd $basePath
git add .
git reset --hard HEAD^
EOF;
            // 拉取命令
            $pull = <<<EOF
cd $basePath
git pull
composer install --no-dev
php artisan migrate --force
EOF;

            //为了防止有新增的文件 先reset
            exec($reset, $result);
            dump($result);
            // 拉取代码
            exec($pull, $result);
            dump($result);
        }
    }
}
