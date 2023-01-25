<?php

namespace Azizjonaliev\Laravelcrud\CRUD;

use Azizjonaliev\Laravelcrud\CRUD\abstract\BaseCrud;
use Azizjonaliev\Laravelcrud\CRUD\interfaces\ApiCrud as InterfacesApiCrud;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ApiCrudGenerate extends BaseCrud implements InterfacesApiCrud {
    public $resource;
    public $service;

    public function generate()
    {
        $result = [
            'Request' => null,
            'Service' => null,
            'Resource' => null,
        ];

        $result['Model'] = $this->model();
        $result['Migration'] = $this->migration();
        $result['Controller'] = $this->controller();
        $result['Route'] = $this->route();

        if ($this->request)
            $result['Request'] = $this->request();

        if ($this->service)
            $result['Service'] = $this->service();

        if ($this->resource)
            $result['Resource'] = $this->resource();

        return $result;
    }

    public function resource()
    {
        $file = $this->add_version(app_path("Http/Resources"));
        $namespace = $this->add_version('App\Http\Resources');

        $this->falseOrCreateDirectory($file);
        $file = $this->add_str($file, "{$this->name}Resource.php");

        $this->put(
            $file,
            ['{{ className }}', '{{ namespace }}'],
            [$this->getClassName(), $namespace],
            'Resource'
        );
    }

    public function service()
    {
        $namespaceResource = $this->add_version('use App\Http\Resources');
        $namespace = $this->add_version('App\Services\Api');
        $file = $this->add_version(app_path("Services\Api"));


        $this->falseOrCreateDirectory($file);
        $file = $this->add_str($file, "{$this->getClassName()}Service.php");
        $namespaceResource = $this->resource ? $this->add_str($namespaceResource, "{$this->getClassName()}Resource;") : "";

        $prossesing = ['index', 'show', 'store', 'update', 'destroy'];
        $templates = ['{{ className }}', '{{ namespaceResource }}', '{{ namespace }}'];
        $values = [$this->name, $namespaceResource, $namespace];

        foreach ($prossesing as $item) {
            $templates[] = '{{ response_'.$item.' }}';

            if ($this->resource) {
                $values[] = File::get(__DIR__."\abstract\stubs\components\\resource\\1\\{$item}.stub");
            } else {
                if ($item == 'destroy')
                    $values[] = 'return null;';
                elseif ($item == 'index')
                    $values[] = 'return $objects;';
                else
                    $values[] = 'return $object;';
            }
        }

        $file = $this->put($file, $templates, $values, "Service");

        if ($file) {
            $template = Str::replace('{{ className }}', $this->getClassName(), File::get($file));
            File::put($file, $template);
        }
        return $file;
    }

    public function controller()
    {
        $file = $this->add_version(app_path("Http/Controllers/Api"));
        $namespace = $this->add_version('App\Http\Controllers\Api');

        $this->falseOrCreateDirectory($file);


        $file = $this->add_str($file, "{$this->getClassName()}Controller.php");

        $namespaceRequest = $this->add_version('App\Http\Requests');
        $namespaceRequest = $this->add_str($namespaceRequest, $this->getClassName());

        if ($this->request) {
            $storeRequestClass = 'StoreRequest';
            $updateRequestClass = 'UpdateRequest';

            $namespaceStoreRequest = $this->add_str($namespaceRequest, 'StoreRequest');
            $namespaceUpdateRequest = $this->add_str($namespaceRequest, 'UpdateRequest');

            $namespaceRequest = "{$namespaceStoreRequest};\nuse {$namespaceUpdateRequest};";
        } else {
            $storeRequestClass = 'Request';
            $updateRequestClass = 'Request';

            $namespaceStoreRequest = 'Illuminate\Http\Request';
            $namespaceUpdateRequest = 'Illuminate\Http\Request';

            $namespaceRequest = "{$namespaceStoreRequest};";
        }

        $namespaceAll = $namespaceRequest;

        if ($this->resource) {
            $namespaceResource = $this->add_version('App\Http\Resources');
            $namespaceResource = $this->add_str($namespaceResource, "{$this->getClassName()}Resource");
            $namespaceAll .= "\nuse {$namespaceResource};";
        }

        if ($this->service) {
            $namespaceService = $this->add_version("App\Services\Api");
            $namespaceService = $this->add_str($namespaceService, "{$this->getClassName()}Service");

            $namespaceAll .= "\nuse {$namespaceService};";

            // return $this->put(
            //     $file,
            //     ['{{ namespace }}', '{{ namespaceService }}', '{{ className }}'],
            //     [$namespace, $namespaceService, $this->getClassName()],
            //     'Api/ControllerWithService'
            // );
        }
        $prossesing = ['index', 'show', 'store', 'update', 'destroy'];

        $templates = [
                '{{ namespace }}', '{{ className }}', '{{ classLowerName }}',
                '{{ validate }}',

                '{{ StoreRequestClass }}',
                '{{ UpdateRequestClass }}',
                '{{ namespaceStoreRequest }}',
                '{{ namespaceUpdateRequest }}',
                '{{ namespaceAll }}',
                '{{ prossesing_construct }}'
            ];
        $values = [
            $namespace, $this->getClassName(), $this->getLowerName(),
            $this->getComponentValidate(),

            $storeRequestClass,
            $updateRequestClass,
            $namespaceStoreRequest,
            $namespaceUpdateRequest,
            $namespaceAll,
            $this->getComponentService('construct'),
        ];

        foreach ($prossesing as $item) {
            $templates[] = '{{ prossesing_'.$item.' }}';
            $templates[] = '{{ response_'.$item.' }}';
            $values[] = $this->getComponentService($item);
            $values[] = $this->getComponentResource($item);
        }

        $file = $this->put($file, $templates, $values, 'Api/Controller');

        if ($file) {
            $template = Str::replace('{{ className }}', $this->getClassName(), File::get($file));
            File::put($file, $template);
        }
        return $file;
    }

    public function route()
    {
        $file = $this->add_str(base_path('routes'), 'api.php');
        $controller = $this->add_version('App\Http\Controllers\Api');
        $name = Str::plural($this->getLowerName());

        $content = "Route::apiResource('{$name}', {$controller}\\{$this->getClassName()}Controller::class);";

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
    }

    protected function getComponentService($method)
    {
        if ($this->service)
            $path = $this->add_str(__DIR__."\abstract\stubs\components\service", 1);
        else
            $path = $this->add_str(__DIR__."\abstract\stubs\components\service", 0);

        return File::get($this->add_str($path, $method.".stub"));
    }

    protected function getComponentResource($method)
    {
        if ($this->resource)
            $path = $this->add_str(__DIR__."\abstract\stubs\components\\resource", 1);
        else
            $path = $this->add_str(__DIR__."\abstract\stubs\components\\resource", 0);

        return File::get($this->add_str($path, $method.".stub"));
    }
}
