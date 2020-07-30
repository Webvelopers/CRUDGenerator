<?php

namespace Webvelopers\CRUDGenerator\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CRUDGeneratorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:generator {name : model name for example Post} {--api : create an api controller and route}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model, migration, request and controller file with CRUD for operations';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');

        $this->model($name);
        $this->migration($name);
        $this->request($name);

        if ($this->option('api')) {
            $this->apiController($name);
            File::append(
                base_path('routes/api.php'),
                "\nRoute::resource('" . Str::plural(strtolower($name)) . "', 'Api\\{$name}Controller')->except(['create', 'edit']);\n"
            );
        } else {
            $this->controller($name);
            File::append(
                base_path('routes/web.php'),
                "\nRoute::resource('" . Str::plural(strtolower($name)) . "', '{$name}Controller');\n"
            );
        }

        $this->info("CRUD Generator created $name model, migration, request and controller successfully.");
        $this->comment('Please edit migration file before to run "php artisan migrate" command.');
    }

    /**
     * Get content of the stub files
     *
     * @param string $type
     * @return data
     */
    protected function getStub($type)
    {
        return file_get_contents(__DIR__ . "/../../stubs/$type.stub");
    }

    /**
     * Create the model file
     *
     * @param string $name
     * @return void
     */
    protected function model($name)
    {
        $modelTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
            ],
            [
                $name,
                Str::plural(strtolower($name)),
            ],
            $this->getStub('Model')
        );

        file_put_contents(app_path("/{$name}.php"), $modelTemplate);
    }

    /**
     * Create the migration file
     *
     * @param string $name
     * @return void
     */
    protected function migration($name)
    {
        $this->callSilent('make:migration', [
            'name' => 'Create' . Str::plural(ucfirst($name)) . 'Table',
        ]);
    }

    /**
     * Create the request file
     *
     * @param string $name
     * @return void
     */
    protected function request($name)
    {
        $requestTemplate = str_replace(
            ['{{modelName}}'],
            [$name],
            $this->getStub('Request')
        );

        if (!file_exists($path = app_path('/Http/Requests'))) {
            mkdir($path, 0777, true);
        }

        file_put_contents(app_path("/Http/Requests/{$name}Request.php"), $requestTemplate);
    }

    /**
     * Create the controller file
     *
     * @param string $name
     * @return void
     */
    protected function controller($name)
    {
        $controllerTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}',
            ],
            [
                $name,
                strtolower(Str::plural($name)),
                strtolower($name),
            ],
            $this->getStub('Controller')
        );

        file_put_contents(app_path("/Http/Controllers/{$name}Controller.php"), $controllerTemplate);
    }

    /**
     * Create the controller file
     *
     * @param string $name
     * @return void
     */
    protected function apiController($name)
    {
        $controllerTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}',
            ],
            [
                $name,
                strtolower(Str::plural($name)),
                strtolower($name),
            ],
            $this->getStub('ApiController')
        );

        if (!file_exists($path = app_path('/Http/Controllers/Api'))) {
            mkdir($path, 0777, true);
        }

        file_put_contents(app_path("/Http/Controllers/Api/{$name}Controller.php"), $controllerTemplate);
    }
}