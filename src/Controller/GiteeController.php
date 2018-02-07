<?php

namespace Baijunyao\LaravelGitee\Controller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Artisan;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\PhpExecutableFinder;

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
        if (env('GITEE_HOOK_DEBUG') || ($data['password'] === env('GITEE_HOOK_PASSWORD') && 'merged' === $data['state'])) {
            $basePath = base_path();
            chdir($basePath);
            $phpBinPath = dirname((new PhpExecutableFinder)->find());

            // 回滚
            $reset = <<<EOF
git add .
git reset --hard HEAD^
EOF;
            // 拉取命令
            $pull = <<<EOF
git pull
export PATH=\$PATH:$phpBinPath
composer install --no-dev
EOF;
            // 为了防止有新增的文件 先reset
            $process = new Process($reset);
            $process->run();
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
            dump($process->getOutput());

            // 拉取代码 执行 composer install
            $process = new Process($pull);
            $process->run();
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
            dump($process->getOutput());

            // 执行迁移
            Artisan::call('migrate', [
                '--force' => true,
            ]);
        }
    }
}
