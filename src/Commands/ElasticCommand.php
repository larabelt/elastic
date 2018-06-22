<?php

namespace Belt\Elastic\Commands;

use Riimu;
use Belt\Elastic\Service as ElasticService;
use Illuminate\Console\Command;

/**
 * Class CompileCommand
 * @package Belt\Elastic\Commands
 */
class ElasticCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'belt-elastic:search {action} {--type=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * @var ElasticService
     */
    public $service;

    /**
     * @return ElasticService
     */
    public function service()
    {
        return $this->service = $this->service ?: new ElasticService(['console' => $this]);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $action = $this->argument('action');

        if ($action == 'get-settings') {
            $this->service()->getSettings();
        }

        if ($action == 'get-mappings') {
            $this->service()->getMappings();
        }

        if ($action == 'put-mappings') {
            $this->service()->putMappings();
        }

        if ($action == 'delete-index') {
            $this->service()->deleteIndex();
        }

        if ($action == 'create-index') {
            $this->service()->createIndex();
        }

        if ($action == 'replace-index') {
            $this->service()->deleteIndex();
            $this->service()->createIndex();
        }

        if ($action == 'upsert') {
            $this->service()->upsert($this->option('type'));
        }

    }


}