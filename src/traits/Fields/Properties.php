<?php

namespace FASTSQL\Traits\Fields;

trait Properties {
    private $name;
    private $type;
    private $options = array();

     /**
     * Get the name of the field.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Check if the field has a primary key.
     *
     * @return bool
     */
    public function hasPrimaryKey() {
        return in_array('PRIMARY KEY', $this->options);
    }

    /**
     * Check if the field is unique.
     *
     * @return bool
     */
    public function isUnique() {
        return in_array('UNIQUE', $this->options);
    }

    /**
     * Set the field as NOT NULL.
     *
     * @return $this
     */
    public function notNull() {
        $this->options[] = "NOT NULL";
        return $this;
    }

    /**
     * Set the field as PRIMARY KEY.
     *
     * @return $this
     */
    public function primaryKey() {
        $this->options[] = "PRIMARY KEY";
        return $this;
    }

    /**
     * Set the field as AUTO_INCREMENT.
     *
     * @return $this
     */
    public function autoIncrement() {
        $this->options[] = "AUTO_INCREMENT";
        return $this;
    }

    /**
     * Set the field as UNIQUE.
     *
     * @return $this
     */
    public function unique() {
        $this->options[] = "UNIQUE";
        return $this;
    }

    /**
     * Set the default value for the field.
     *
     * @param mixed $value The default value.
     *
     * @return $this
     */
    public function default($value) {
        $this->options[] = "DEFAULT '$value'";
        return $this;
    }

    /**
     * Add a CHECK constraint to the field.
     *
     * @param string $condition The condition for the CHECK constraint.
     *
     * @return $this
     */
    public function check($condition) {
        $this->options[] = "CHECK ($condition)";
        return $this;
    }

    /**
     * Add a comment to the field.
     *
     * @param string $comment The comment for the field.
     *
     * @return $this
     */
    public function comment($comment) {
        $this->options[] = "COMMENT '$comment'";
        return $this;
    }

    /**
     * Set the length for the field.
     *
     * @param int $length The length of the field.
     *
     * @return $this
     */
    public function length($length) {
        $this->options[] = "($length)";
        return $this;
    }

    /**
     * Set precision and scale for a numeric field.
     *
     * @param int $precision The precision of the numeric field.
     * @param int $scale     The scale of the numeric field.
     *
     * @return $this
     */
    public function precision($precision, $scale) {
        $this->options[] = "($precision, $scale)";
        return $this;
    }

    /**
     * Set the collation for the field.
     *
     * @param string $collation The collation for the field.
     *
     * @return $this
     */
    public function collate($collation) {
        $this->options[] = "COLLATE $collation";
        return $this;
    }

    /**
     * Set the field as an index.
     *
     * @return $this
     */
    public function index() {
        $this->options[] = "INDEX";
        return $this;
    }

    /**
     * Set the field as a unique key.
     *
     * @return $this
     */
    public function uniqueKey() {
        $this->options[] = "UNIQUE KEY";
        return $this;
    }

    /**
     * Set the field as a spatial index.
     *
     * @return $this
     */
    public function spatialIndex() {
        $this->options[] = "SPATIAL INDEX";
        return $this;
    }

    /**
     * Set the field as unsigned.
     *
     * @return $this
     */
    public function unsigned() {
        $this->options[] = "UNSIGNED";
        return $this;
    }

    /**
     * Set the field as ZEROFILL.
     *
     * @return $this
     */
    public function zerofill() {
        $this->options[] = "ZEROFILL";
        return $this;
    }

    /**
     * Set the default value for the field to the current timestamp.
     *
     * @return $this
     */
    public function currentTimestamp() {
        $this->options[] = "DEFAULT CURRENT_TIMESTAMP";
        return $this;
    }

    /**
     * Set the ON DELETE CASCADE behavior for a foreign key.
     *
     * @return $this
     */
    public function onDeleteCascade() {
        $this->options[] = "ON DELETE CASCADE";
        return $this;
    }

    /**
     * Set the ON UPDATE CASCADE behavior for a foreign key.
     *
     * @return $this
     */
    public function onUpdateCascade() {
        $this->options[] = "ON UPDATE CASCADE";
        return $this;
    }

    /**
     * Set the field as a standard integer.
     *
     * @return $this
     */
    public function integer() {
        $this->setType("INT");
        return $this;
    }
    
    /**
     * Set the field as binary.
     *
     * @return $this
     */
    public function binary() {
        $this->options[] = "BINARY";
        return $this;
    }

    /**
     * Set the character set for the field.
     *
     * @param string $charset The character set for the field.
     *
     * @return $this
     */
    public function charset($charset) {
        $this->options[] = "CHARACTER SET $charset";
        return $this;
    }

    /**
     * Set the field as a virtual generated column.
     *
     * @param string $expression The expression for the virtual column.
     *
     * @return $this
     */
    public function virtualAs($expression) {
        $this->options[] = "VIRTUAL GENERATED ALWAYS AS ($expression)";
        return $this;
    }

    /**
     * Set the field as a stored generated column.
     *
     * @param string $expression The expression for the stored column.
     *
     * @return $this
     */
    public function storedAs($expression) {
        $this->options[] = "STORED GENERATED ALWAYS AS ($expression)";
        return $this;
    }

    /**
     * Set a foreign key reference for the field.
     *
     * @param string $foreignTable The referenced table for the foreign key.
     * @param string $foreignField The referenced field for the foreign key.
     *
     * @return $this
     */
    public function references($foreignTable, $foreignField) {
        $this->options[] = "REFERENCES $foreignTable($foreignField)";
        return $this;
    }

    /**
     * Set the field as a primary key with auto-increment.
     *
     * @return $this
     */
    public function primaryKeyAutoIncrement() {
        $this->primaryKey();
        $this->autoIncrement();
        return $this;
    }

    /**
     * Set the field as a medium integer.
     *
     * @return $this
     */
    public function mediumInt() {
        $this->setType("MEDIUMINT");
        return $this;
    }

    /**
     * Set the field as a big integer.
     *
     * @return $this
     */
    public function bigInt() {
        $this->setType("BIGINT");
        return $this;
    }

    /**
     * Set the field as a tiny integer.
     *
     * @return $this
     */
    public function tinyInt() {
        $this->setType("TINYINT");
        return $this;
    }

    /**
     * Set the field as a date.
     *
     * @return $this
     */
    public function date() {
        $this->setType("DATE");
        return $this;
    }

    /**
     * Set the field as a time.
     *
     * @return $this
     */
    public function time() {
        $this->setType("TIME");
        return $this;
    }

    /**
     * Set the field as a year.
     *
     * @return $this
     */
    public function year() {
        $this->setType("YEAR");
        return $this;
    }

    /**
     * Set the field as a binary large object (BLOB).
     *
     * @return $this
     */
    public function blob() {
        $this->setType("BLOB");
        return $this;
    }

    /**
     * Set the field as a text.
     *
     * @return $this
     */
    public function tinyText() {
        $this->setType("TINYTEXT");
        return $this;
    }

    /**
     * Set the field as a text.
     *
     * @return $this
     */
    public function text() {
        $this->setType("TEXT");
        return $this;
    }

    /**
     * Set the field as a text.
     *
     * @return $this
     */
    public function mediumText() {
        $this->setType("MEDIUMTEXT");
        return $this;
    }

    /**
     * Set the field as a text.
     *
     * @return $this
     */
    public function largeText() {
        $this->setType("LONGTEXT");
        return $this;
    }

    /**
     * Set the field as a decimal with precision and scale.
     *
     * @param int $precision The precision of the decimal.
     * @param int $scale     The scale of the decimal.
     *
     * @return $this
     */
    public function decimal($precision, $scale) {
        $this->setType("DECIMAL", null, $precision, $scale);
        return $this;
    }

    /**
     * Set the field as a float.
     *
     * @return $this
     */
    public function float() {
        $this->setType("FLOAT");
        return $this;
    }

    /**
     * Set the field as a double.
     *
     * @return $this
     */
    public function double() {
        $this->setType("DOUBLE");
        return $this;
    }

    /**
     * Set the field as a boolean (tinyint with 1 or 0).
     *
     * @return $this
     */
    public function boolean() {
        $this->tinyInt();
        return $this;
    }

    /**
     * Set the field as an enum with given values.
     *
     * @param array $values The allowed values for the enum.
     *
     * @return $this
     */
    public function enum(array $values) {
        $this->setType("ENUM('" . implode("','", $values) . "')");
        return $this;
    }

    /**
     * Set the field as a JSON.
     *
     * @return $this
     */
    public function json() {
        $this->setType("JSON");
        return $this;
    }

    /**
     * Set the field as a point for spatial data.
     *
     * @return $this
     */
    public function point() {
        $this->setType("POINT");
        return $this;
    }

    /**
     * Set the field as a line string for spatial data.
     *
     * @return $this
     */
    public function lineString() {
        $this->setType("LINESTRING");
        return $this;
    }

    /**
     * Set the field as a polygon for spatial data.
     *
     * @return $this
     */
    public function polygon() {
        $this->setType("POLYGON");
        return $this;
    }

    /**
     * Set the field as a geometry for spatial data.
     *
     * @return $this
     */
    public function geometry() {
        $this->setType("GEOMETRY");
        return $this;
    }
/**
     * Set the field as an auto-updating timestamp on insert.
     *
     * @return $this
     */
    public function onInsertCurrentTimestamp() {
        $this->options[] = "DEFAULT CURRENT_TIMESTAMP";
        return $this;
    }

    /**
     * Set the field as a unique identifier (UUID).
     *
     * @return $this
     */
    public function uuid() {
        $this->setType("UUID");
        return $this;
    }

    /**
     * Set the field as an IP address.
     *
     * @return $this
     */
    public function ipAddress() {
        $this->setType("VARCHAR", 45) // Assuming IPv6 compatibility
            ->collate("ascii_general_ci");
        return $this;
    }

    /**
     * Set the field as an email address.
     *
     * @return $this
     */
    public function email() {
        $this->setType("VARCHAR", 255)
            ->collate("utf8mb4_general_ci");
        return $this;
    }

    /**
     * Set the field as a password hash.
     *
     * @return $this
     */
    public function passwordHash() {
        $this->setType("VARCHAR", 255);
        return $this;
    }

    /**
     * Set the field as a soft delete flag.
     *
     * @return $this
     */
    public function softDelete() {
        $this->boolean()
            ->default(0)
            ->comment("Soft delete flag: 1 for deleted, 0 for active");
        return $this;
    }

    /**
     * Set the field as a foreign key that references the same table.
     *
     * @param string $field The referenced field.
     *
     * @return $this
     */
    public function selfReference($field = 'id') {
        $this->options[] = "FOREIGN KEY REFERENCES {$this->name}($field)";
        return $this;
    }

    /**
     * Set the field as an auto-incremental version number.
     *
     * @return $this
     */
    public function version() {
        $this->integer ()
            ->unsigned()
            ->comment("Auto-incremental version number");
        return $this;
    }

    /**
     * Set the field as a file path or URL.
     *
     * @return $this
     */
    public function filePath() {
        $this->setType("VARCHAR", 255);
        return $this;
    }

    /**
     * Set the field as a color in hexadecimal format.
     *
     * @return $this
     */
    public function color() {
        $this->setType("CHAR", 7); // e.g., #RRGGBB
        return $this;
    }

    /**
     * Set the field as a URL.
     *
     * @return $this
     */
    public function url() {
        $this->setType("VARCHAR", 255);
        return $this;
    }

    /**
     * Set the field as a JSONB data type.
     *
     * @return $this
     */
    public function jsonb() {
        $this->setType("JSONB");
        return $this;
    }

    /**
     * Set the field as a money amount.
     *
     * @return $this
     */
    public function money() {
        $this->decimal(10, 2);
        return $this;
    }

    /**
     * Set the field as a timestamp with time zone.
     *
     * @return $this
     */
    public function timestampTz() {
        $this->setType("TIMESTAMP WITH TIME ZONE");
        return $this;
    }

    /**
     * Set the field as a duration or interval.
     *
     * @return $this
     */
    public function interval() {
        $this->setType("INTERVAL");
        return $this;
    }

    /**
     * Set the field as a URL-friendly slug.
     *
     * @return $this
     */
    public function slug() {
        $this->setType("VARCHAR", 255)
            ->collate("utf8mb4_general_ci");
        return $this;
    }

    /**
     * Set the field as a phone number.
     *
     * @return $this
     */
    public function phoneNumber() {
        $this->setType("VARCHAR", 20);
        return $this;
    }

    /**
     * Set the field as a country code.
     *
     * @return $this
     */
    public function countryCode() {
        $this->setType("CHAR", 2);
        return $this;
    }

    /**
     * Set the field as an IPv4 address.
     *
     * @return $this
     */
    public function ipv4Address() {
        $this->setType("VARCHAR", 15);
        return $this;
    }

    /**
     * Set the field as a MAC address.
     *
     * @return $this
     */
    public function macAddress() {
        $this->setType("VARCHAR", 17);
        return $this;
    }

    /**
     * Set the field as a UTC datetime.
     *
     * @return $this
     */
    public function utcDatetime() {
        $this->setType("DATETIME")
            ->collate("utf8mb4_general_ci");
        return $this;
    }

    /**
     * Set the field as a URL-friendly path.
     *
     * @return $this
     */
    public function urlPath() {
        $this->setType("VARCHAR", 255)
            ->collate("utf8mb4_general_ci");
        return $this;
    }

    /**
     * Set the field as a tag (alphanumeric with spaces).
     *
     * @return $this
     */
    public function tag() {
        $this->setType("VARCHAR", 50)
            ->collate("utf8mb4_general_ci");
        return $this;
    }
}