<?php

namespace app\models\interfaces;

//для реализации постраничной навигации
interface IDataPages
{

    public function getAllByPage();

}