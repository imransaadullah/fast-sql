<?php

namespace FASTSQL;
use FASTSQL\Traits\Fields\Actions;
use FASTSQL\Traits\Fields\Contraints;
use FASTSQL\Traits\Fields\Properties;

class FieldDefinition {
    use Contraints, Properties, Actions;
    
    /**
     * Constructor for FieldDefinition class.
     *
     * @param string $name The name of the field.
     */
    public function __construct($name) {
        $this->name = $name;
    }

    /**
     * Set the data type for the field.
     *
     * @param string $type      The data type (e.g., INT, VARCHAR).
     * @param int    $length    The length for types that support it.
     * @param int    $precision The precision for numeric types.
     * @param int    $scale     The scale for numeric types.
     *
     * @return $this
     */
    public function setType($type, $length = null, $precision = null, $scale = null) {
        $this->type = $type;

        // Add length, precision, and scale if applicable
        if ($length !== null) {
            $this->options[] = "($length)";
        } elseif ($precision !== null && $scale !== null) {
            $this->options[] = "($precision, $scale)";
        }

        return $this;
    }

    
    public function generateFieldStatement() {
        $sql = "`{$this->name}` {$this->type}";

        $options = implode(' ', 
            array_filter(
                $this->options, 
                function($option) {
                    return $option !== 'PRIMARY KEY' && $option !== 'UNIQUE';
                }
            )
        );
        
        if (!empty($options)) {
            $sql .= " $options";
        }

        return $sql;
    }

}
