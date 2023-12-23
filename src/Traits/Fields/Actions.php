<?php
 namespace FASTSQL\Traits\Fields;

 trait Actions {

     /**
     * Get the name of the field.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }


    public function getLength() {
        return $this->length;
    }

    public function getComment() {
        return $this->comment;
    }
 }