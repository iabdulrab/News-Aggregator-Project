<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeRepositoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name : The name of the repository (e.g. UserRepository)}';

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
        // normalize separators so user can pass either Auth\UserRepository or Auth/UserRepository
        $rawName = $this->argument('name');
        $name = str_replace('\\', '/', $rawName);

        // Build filesystem path using normalized slashes
        $path = app_path('Repositories/' . $name . '.php');
        $directory = dirname($path);

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        if (File::exists($path)) {
            $this->error("Repository {$rawName} already exists!");
            return Command::FAILURE;
        }

        // Determine namespace — only append sub-namespace when dirname($name) is not '.'
        $sub = dirname($name); // returns '.' when there's no folder part
        if ($sub === '.' || $sub === '') {
            $namespace = 'App\\Repositories';
        } else {
            $namespace = 'App\\Repositories\\' . str_replace('/', '\\', $sub);
        }

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

        $this->info("✅ Repository created: {$namespace}\\{$className}");

        return Command::SUCCESS;
    }
}
