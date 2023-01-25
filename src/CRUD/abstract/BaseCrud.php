<?php

namespace Azizjonaliev\Laravelcrud\CRUD\abstract;

use Azizjonaliev\Laravelcrud\CRUD\interfaces\BaseCrud as InterfacesBaseCrud;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

abstract class BaseCrud implements InterfacesBaseCrud
{
    public $name;
    public $version;
    public $request;

    public function model() {
        $file = app_path("Models/{$this->getClassName()}.php");
        return $this->put($file, '{{ className }}', $this->getClassName(), 'Model');
    }

    public function migration()
    {
        $table = Str::plural($this->getLowerName());
        $file = base_path("database/migrations/".date('Y_m_d_His')."_create_{$table}_tables.php");
        return $this->put($file, '{{ table }}', $table, 'Migration');
    }

    public function request()
    {
        $classNames = ['Store', 'Update'];

        $namespace = $this->add_version('App\Http\Requests');
        $namespace = $this->add_str($namespace, $this->getClassName());

        $path = $this->add_version(app_path("Http/Requests"));
        $path = $this->add_str($path, $this->getClassName());

        $this->falseOrCreateDirectory($path);
        $resalt = [];

        foreach ($classNames as $clsName) {
            $file = $this->add_str($path, $clsName.'Request.php');
            $resalt[] = $this->put($file, ['{{ className }}', '{{ namespace }}'], [$clsName, $namespace], 'Request');
        }

        return $resalt;
    }

    abstract public function service();
    abstract public function controller();
    abstract public function route();

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    protected function getStub($path)
    {
        return File::get(__DIR__."\stubs\\{$path}.stub");
    }

    protected function put($file, $templates, $values, $stub)
    {
        if (!File::exists($file)) {
            $template = Str::replace($templates, $values, $this->getStub($stub));
            File::put($file, $template);
            return $file;
        }
        return false;
    }

    protected function getFolder($path)
    {
        return Str::replace(File::basename($path), "", $path);
    }

    protected function falseOrCreateDirectory($directory, $permissions = 0777, $recursive = true)
    {
        if (!File::exists($directory))
            return File::makeDirectory($directory, $permissions, $recursive);
        else
            return false;
    }

    protected function add_version($str)
    {
        if ($this->version) {
            return $this->add_str($str, "V".$this->version);
        }
        return $str;
    }

    protected function add_str($str, $text, $separator="\\")
    {
        return $str . "{$separator}{$text}";
    }

    protected function getClassName()
    {
        return Str::ucfirst($this->name);
    }

    protected function getLowerName()
    {
        return Str::lower($this->name);
    }

    protected function getComponentValidate()
    {
        if ($this->request)
            $path = $this->add_str("components\\validate", 1);
        else
            $path = $this->add_str("components\\validate", 0);

        return $this->getStub($path);
    }

}
