<?php

namespace Azizjonaliev\Laravelcrud\CRUD;

class CrudGenerate
{
    public $api;
    protected $web;

    public function __construct(ApiCrudGenerate $api, WebCrudGenerate $web)
    {
        $this->api = $api;
        $this->web = $web;
    }

    /*
    protected $name;
    protected $version;

    public function service()
    {
        $namespaceResource = 'App\Http\Resources';
        $namespace = 'App\Services';
        $file = app_path("Services");


        if ($this->api) {
            $namespace .= "\Api";
            $file .= "\Api";
        }

        if ($this->version) {
            $file .= "\\V{$this->version}";
            $namespaceResource .= "\\V{$this->version}";
            $namespace .= "\\V{$this->version}";
        }

        $this->mkdir($file);

        $file .= "\\{$this->name}Service.php";

        $namespaceResource .= "\\{$this->name}Resource;";

        return $this->put_or_error(
            $file,
            ['{{ className }}', '{{ namespaceResource }}', '{{ namespace }}'],
            [$this->name, $namespaceResource, $namespace],
            "Service"
        );
    }

    public function controller()
    {
        $file = app_path("Http/Controllers");
        $namespace = 'App\Http\Controllers';
        $namespaceRequest = 'App\Http\Requests';
        $namespaceService = 'App\Services';

        if ($this->api) {
            $a = "\\Api";
            $file .= $a;
            $namespace .= $a;
            $namespaceService .= $a;
        }

        if ($this->version) {
            $ver = "\V{$this->version}";
            $file .= $ver;
            $namespace .= $ver;
            $namespaceRequest .= $ver;
            $namespaceService .= $ver;
        }

        $this->mkdir($file);

        $file .= "\\{$this->name}Controller.php";

        $namespaceRequest .= "\\{$this->name}";
        $namespaceService .= "\\{$this->name}Service";

        return $this->put_or_error(
            $file,
            [
                '{{ namespace }}',
                '{{ namespaceStoreRequest }}',
                '{{ namespaceUpdateRequest }}',
                '{{ namespaceService }}',
                '{{ className }}'
            ],
            [
                $namespace,
                $namespaceRequest."\\StoreRequest",
                $namespaceRequest."\\UpdateRequest",
                $namespaceService,
                $this->name
            ],
            'Controller'
        );
    }

    public function route()
    {
        $file = base_path('routes');
        $controller = 'App\Http\Controllers';
        $name = Str::plural(strtolower($this->name));

        if ($this->api)
            $controller .= "\\Api";

        if ($this->version) {
            // $file .= "\\v{$this->version}";
            $controller .= "\\V{$this->version}";
        }

        if ($this->api) {
            $file .= "\\api.php";
            $content = "Route::apiResource('{$name}', {$controller}\\{$this->name}Controller::class);";
        } else {
            $file .= "\\web.php";
            $content = "Route::resource('{$name}', {$controller}\\{$this->name}Controller::class);";
        }

        if ($this->version) {
            $old_file = file($file);
            $seacrh = "Route::prefix('v{$this->version}')->group(function() {";
            $content = "\n\t" . $content;
            $replace = $seacrh . $content;

            if (array_search($seacrh."\n", $old_file))
            {
                $template = str_replace($seacrh, $replace, $old_file);
                file_put_contents($file, $template);
                return $file;
            } else {
                $replace = "\n\n{$replace}\n});";
                File::append($file, $replace);
                return $file;
            }
        } else {
            File::append($file, "\n".$content);
            return $file;
        }


        // $this->components->info("Route [{$content}] add to [{$file}] success.");
    }
    */
}
