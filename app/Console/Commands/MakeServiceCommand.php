<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name : The name of the service (e.g. UserService)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class in the app/Services directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
    $name = $this->argument('name');
    $path = app_path('Services/' . $name . '.php');

    $directory = dirname($path);

    if (!File::exists($directory)) {
        File::makeDirectory($directory, 0755, true);
    }

    if (File::exists($path)) {
        $this->error("Service {$name} already exists!");
        return Command::FAILURE;
    }

    // Generate namespace based on subdirectory
    $namespace = 'App\\Services' . '\\' . str_replace('/', '\\', dirname($name));
    $namespace = rtrim($namespace, '\\');

    $className = Str::studly(basename($name));

    $stub = <<<PHP
<?php

namespace {$namespace};

class {$className}
{
    //
}

PHP;

    File::put($path, $stub);

    $this->info("✅ Service created: {$namespace}\\{$className}");

    return Command::SUCCESS;
    }
}
