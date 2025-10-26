<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
class MakeInterfaceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:interface {name : The name of the interface (e.g. UserRepositoryInterface)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new interface file in the App/Interfaces directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
    
        $name = $this->argument('name');
        $path = app_path('Interfaces/' . $name . '.php');

        // Create directory if it doesn't exist
        if (!File::exists(app_path('Interfaces'))) {
            File::makeDirectory(app_path('Interfaces'), 0755, true);
        }

        // Prevent overwriting existing files
        if (File::exists($path)) {
            $this->error("Interface {$name} already exists!");
            return Command::FAILURE;
        }

        // Generate namespace and class name
        $namespace = 'App\\Interfaces';
        $className = Str::studly($name);

        // Interface stub content
        $stub = <<<PHP
<?php

namespace {$namespace};

interface {$className}
{
    //
}

PHP;

        File::put($path, $stub);

        $this->info("✅ Interface created: App\\Interfaces\\{$className}");

        return Command::SUCCESS;
    }
    
}
