<?php

namespace Belt\Elastic\Commands;

use Belt\Core\Commands\PublishCommand as Command;

/**
 * Class PublishCommand
 * @package Belt\Content\Commands
 */
class PublishCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'belt-content:publish {action=publish} {--force} {--include=} {--exclude=} {--config}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'publish assets for belt content';

    /**
     * @var array
     */
    protected $dirs = [
        'vendor/larabelt/content/config' => 'config/belt',
        //'vendor/larabelt/content/resources/js' => 'resources/belt/content/js',
        //'vendor/larabelt/content/resources/sass' => 'resources/belt/content/sass',
        'vendor/larabelt/content/database/factories' => 'database/factories',
        'vendor/larabelt/content/database/migrations' => 'database/migrations',
        'vendor/larabelt/content/database/seeds' => 'database/seeds',
    ];

}