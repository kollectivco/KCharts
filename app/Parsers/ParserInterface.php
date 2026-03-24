<?php
namespace Kontentainment\Charts\Parsers;

interface ParserInterface {

    /**
     * @return string
     */
    public function get_key(): string;

    /**
     * Detect if this headers align with this parser
     *
     * @param array $headers
     * @return bool
     */
    public function detect( array $headers ): bool;

    /**
     * @param array $headers
     * @return array Array of validation issues, or empty array if valid
     */
    public function validate( array $headers ): array;

    /**
     * @param array $row Raw mapped row (assoc array)
     * @return array Canonical internal schema
     */
    public function normalizeRow( array $row ): array;
    
    /**
     * Return a display summary of column mapping
     */
    public function getMappingSummary(): array;

}
