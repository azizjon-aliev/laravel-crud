<?php

namespace Azizjonaliev\Laravelcrud\CRUD;

use Azizjonaliev\Laravelcrud\CRUD\abstract\BaseCrud;
use Azizjonaliev\Laravelcrud\CRUD\interfaces\WebCrud as InterfacesWebCrud;

class WebCrudGenerate extends BaseCrud implements InterfacesWebCrud {
    public function blade() {}
    public function service() {}
    public function controller() {}
    public function route() {}
}
