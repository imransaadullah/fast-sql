<?php

namespace FASTSQL;
use FASTSQL\Traits\Tables\Actions;
use FASTSQL\Traits\Tables\Contraints;
use FASTSQL\Traits\Tables\Properties;

class TableManipulator {
    use Properties, Actions, Contraints;

    public function __construct($tableName) {
        $this->tableName = $tableName;
    }
    
}
