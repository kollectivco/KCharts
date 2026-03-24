<?php
namespace Kontentainment\Charts\Parsers;

class YouTubeTopSongsParser implements ParserInterface {

    public function get_key(): string {
        return 'youtube_topsongs_csv';
    }

    public function detect( array $headers ): bool {
        $required = [ 'Rank', 'Track Name', 'Artist Names', 'Views', 'YouTube URL' ];
        foreach ( $required as $req ) {
            if ( ! in_array( $req, $headers ) ) {
                return false;
            }
        }
        return true;
    }

    public function validate( array $headers ): array {
        $issues = [];
        if ( ! $this->detect( $headers ) ) {
            $issues[] = "Missing required YouTube CSV columns (Rank, Track Name, Artist Names, Views, YouTube URL).";
        }
        return $issues;
    }

    public function normalizeRow( array $row ): array {
        
        $growth = null;
        if ( isset( $row['Growth'] ) && $row['Growth'] !== '' ) {
            $growth_str = str_replace( '%', '', $row['Growth'] );
            $growth = is_numeric( $growth_str ) ? (float) $growth_str : null;
        }

        $normalized = [
            'raw_rank' => isset($row['Rank']) ? (int) $row['Rank'] : null,
            'raw_previous_rank' => isset($row['Previous Rank']) ? (int) $row['Previous Rank'] : null,
            'raw_peak_rank' => null, // YouTube standard file usually doesn't have peak
            'raw_weeks_on_chart' => isset($row['Periods on Chart']) ? (int) $row['Periods on Chart'] : null,
            'raw_artist_names' => $row['Artist Names'] ?? '',
            'raw_track_name' => $row['Track Name'] ?? '',
            'raw_album_name' => '',
            'metric_primary_type' => 'views',
            'metric_primary_value' => isset($row['Views']) ? (float) str_replace(',','', $row['Views']) : null,
            'raw_growth_value' => $growth,
            'external_url' => $row['YouTube URL'] ?? '',
            'external_uri' => '',
            'raw_metadata_json' => json_encode([])
        ];

        return $normalized;
    }

    public function getMappingSummary(): array {
        return [
            'Rank' => 'raw_rank',
            'Previous Rank' => 'raw_previous_rank',
            'Periods on Chart' => 'raw_weeks_on_chart',
            'Artist Names' => 'raw_artist_names',
            'Track Name' => 'raw_track_name',
            'Views' => 'metric_primary_value',
            'Growth' => 'raw_growth_value',
            'YouTube URL' => 'external_url'
        ];
    }
}
