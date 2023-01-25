<?php

namespace Azizjonaliev\Laravelcrud\CRUD\interfaces;

interface BaseCrud {
    public function model();
    public function migration();
    public function request();
    public function route();
    public function service();
}
