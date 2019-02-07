<?php

namespace Belt\Elastic\Commands;

use Belt\Core\Commands\PublishCommand as Command;

/**
 * Class PublishCommand
 * @package Belt\Elastic\Commands
 */
class PublishCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'belt-elastic:publish {action=publish} {--force} {--include=} {--exclude=} {--config}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'publish assets for belt elastic';

    /**
     * @var array
     */
    protected $dirs = [
        'vendor/larabelt/elastic/config' => 'config/belt',
        'vendor/larabelt/elastic/database/factories' => 'database/factories',
        'vendor/larabelt/elastic/database/migrations' => 'database/migrations',
        'vendor/larabelt/elastic/database/seeds' => 'database/seeds',
        'vendor/larabelt/elastic/docs' => 'resources/docs',
    ];

}