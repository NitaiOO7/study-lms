<?php

namespace App\Services\AI;

use App\Models\AiKnowledgeChunk;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * CodeIndexerService
 *
 * Scans the Laravel project source files and indexes them into
 * the ai_knowledge_chunks table for RAG-based code search.
 *
 * Uses SHA-256 checksums to detect changed files and avoid
 * re-indexing unchanged content.
 */
class CodeIndexerService
{
    protected KnowledgeSearchService $searcher;
    protected int $chunkSize;
    protected int $chunkOverlap;

    public function __construct(KnowledgeSearchService $searcher)
    {
        $this->searcher     = $searcher;
        $this->chunkSize    = config('ai.chunk_size', 1500);
        $this->chunkOverlap = config('ai.chunk_overlap', 200);
    }

    /**
     * Index all configured paths.
     *
     * @param  bool  $force  Re-index even if file hasn't changed
     * @return array{files: int, chunks: int, skipped: int}
     */
    public function indexAll(bool $force = false, ?callable $progress = null): array
    {
        $stats = ['files' => 0, 'chunks' => 0, 'skipped' => 0];
        $paths = config('ai.index_paths', []);

        foreach ($paths as $relativePath) {
            $fullPath = base_path($relativePath);

            if (!File::exists($fullPath)) {
                continue;
            }

            $files = File::isDirectory($fullPath)
                ? File::allFiles($fullPath)
                : [new \SplFileInfo($fullPath)];

            foreach ($files as $file) {
                // Only index PHP and Blade files
                $ext = $file->getExtension();
                if (!in_array($ext, ['php'])) {
                    continue;
                }

                $result = $this->indexFile($file->getRealPath(), $force);
                $stats['files']++;
                $stats['chunks']  += $result['chunks'];
                $stats['skipped'] += $result['skipped'] ? 1 : 0;

                if ($progress) {
                    $progress($file->getRelativePathname(), $result);
                }
            }
        }

        return $stats;
    }

    /**
     * Index a single file.
     *
     * @return array{chunks: int, skipped: bool}
     */
    public function indexFile(string $filePath, bool $force = false): array
    {
        if (!File::exists($filePath)) {
            return ['chunks' => 0, 'skipped' => false];
        }

        $content  = File::get($filePath);
        $checksum = hash('sha256', $content);

        // Determine relative path for storage
        $relativePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $filePath);
        $relativePath = str_replace('\\', '/', $relativePath);

        // Determine source type
        $sourceType = $this->detectSourceType($relativePath);

        // Check if already indexed and unchanged
        if (!$force) {
            $existing = AiKnowledgeChunk::where('source_path', $relativePath)
                ->where('checksum', $checksum)
                ->exists();

            if ($existing) {
                return ['chunks' => 0, 'skipped' => true];
            }
        }

        // Remove old chunks for this file
        AiKnowledgeChunk::where('source_path', $relativePath)->delete();

        // Split content into chunks
        $chunks     = $this->splitIntoChunks($content, $relativePath);
        $chunkCount = 0;

        foreach ($chunks as $index => $chunkContent) {
            if (trim($chunkContent) === '') {
                continue;
            }

            $keywords = $this->searcher->tokenize($chunkContent);
            // Keep only unique top keywords (limit to 50)
            $keywords = array_slice(array_unique($keywords), 0, 50);

            AiKnowledgeChunk::create([
                'source_type'  => $sourceType,
                'source_path'  => $relativePath,
                'chunk_index'  => $index,
                'content'      => $chunkContent,
                'keywords'     => $keywords,
                'checksum'     => $checksum,
                'indexed_at'   => now(),
            ]);

            $chunkCount++;
        }

        return ['chunks' => $chunkCount, 'skipped' => false];
    }

    /**
     * Split file content into overlapping chunks.
     */
    protected function splitIntoChunks(string $content, string $filePath): array
    {
        // Add file path as a header comment to each chunk for context
        $header = "// File: {$filePath}\n";
        $lines  = explode("\n", $content);
        $chunks = [];
        $current = $header;
        $currentLen = strlen($header);

        foreach ($lines as $line) {
            $lineLen = strlen($line) + 1; // +1 for newline

            if ($currentLen + $lineLen > $this->chunkSize && $currentLen > strlen($header)) {
                $chunks[] = $current;

                // Start new chunk with overlap (last N chars of current chunk)
                $overlap = substr($current, -$this->chunkOverlap);
                $current = $header . "// ... (continued)\n" . $overlap . "\n" . $line . "\n";
                $currentLen = strlen($current);
            } else {
                $current    .= $line . "\n";
                $currentLen += $lineLen;
            }
        }

        if (strlen(trim($current)) > strlen($header)) {
            $chunks[] = $current;
        }

        return $chunks;
    }

    /**
     * Detect the source type from the file path.
     */
    protected function detectSourceType(string $path): string
    {
        if (str_contains($path, '/routes/')) {
            return 'route';
        }
        if (str_contains($path, '/migrations/')) {
            return 'schema';
        }
        if (str_contains($path, '/Models/')) {
            return 'code';
        }
        if (str_contains($path, '/Controllers/')) {
            return 'code';
        }
        if (str_contains($path, '/Services/')) {
            return 'code';
        }
        if (str_contains($path, '/views/') || str_contains($path, 'blade')) {
            return 'docs';
        }
        return 'code';
    }

    /**
     * Get indexing statistics.
     */
    public function getStats(): array
    {
        return [
            'total_chunks' => AiKnowledgeChunk::count(),
            'by_type'      => AiKnowledgeChunk::selectRaw('source_type, count(*) as count')
                ->groupBy('source_type')
                ->pluck('count', 'source_type')
                ->toArray(),
            'last_indexed' => AiKnowledgeChunk::latest('indexed_at')->value('indexed_at'),
            'total_files'  => AiKnowledgeChunk::distinct('source_path')->count('source_path'),
        ];
    }
}
