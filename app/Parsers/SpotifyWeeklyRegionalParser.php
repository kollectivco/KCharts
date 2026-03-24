<?php
namespace Kontentainment\Charts\Parsers;

class SpotifyWeeklyRegionalParser implements ParserInterface {

    public function get_key(): string {
        return 'spotify_regional_csv';
    }

    public function detect( array $headers ): bool {
        $required = [ 'rank', 'uri', 'artist_names', 'track_name', 'streams' ];
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
            $issues[] = "Missing required Spotify columns (rank, uri, artist_names, track_name, streams).";
        }
        return $issues;
    }

    public function normalizeRow( array $row ): array {
        $normalized = [
            'raw_rank' => isset($row['rank']) ? (int) $row['rank'] : null,
            'raw_previous_rank' => isset($row['previous_rank']) ? (int) $row['previous_rank'] : null,
            'raw_peak_rank' => isset($row['peak_rank']) ? (int) $row['peak_rank'] : null,
            'raw_weeks_on_chart' => isset($row['weeks_on_chart']) ? (int) $row['weeks_on_chart'] : null,
            'raw_artist_names' => $row['artist_names'] ?? '',
            'raw_track_name' => $row['track_name'] ?? '',
            'raw_album_name' => '', // not usually in this CSV
            'metric_primary_type' => 'streams',
            'metric_primary_value' => isset($row['streams']) ? (float) $row['streams'] : null,
            'raw_growth_value' => null,
            'external_url' => '',
            'external_uri' => $row['uri'] ?? '',
            'raw_metadata_json' => json_encode([
                'source' => $row['source'] ?? ''
            ])
        ];

        return $normalized;
    }

    public function getMappingSummary(): array {
        return [
            'rank' => 'raw_rank',
            'previous_rank' => 'raw_previous_rank',
            'peak_rank' => 'raw_peak_rank',
            'weeks_on_chart' => 'raw_weeks_on_chart',
            'artist_names' => 'raw_artist_names',
            'track_name' => 'raw_track_name',
            'streams' => 'metric_primary_value',
            'uri' => 'external_uri'
        ];
    }
}
