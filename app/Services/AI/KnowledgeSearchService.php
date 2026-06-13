<?php

namespace App\Services\AI;

use App\Models\AiKnowledgeChunk;
use App\Models\AiFaq;
use Illuminate\Support\Collection;

/**
 * KnowledgeSearchService
 *
 * Implements TF-IDF keyword-based search over the knowledge base.
 * No external embedding API needed — 100% free, works offline.
 *
 * Algorithm:
 *   1. Tokenize the query into keywords
 *   2. Score each chunk by keyword frequency + position weight
 *   3. Return top-K most relevant chunks
 */
class KnowledgeSearchService
{
    // Common English stop words to ignore during tokenization
    protected array $stopWords = [
        'a','an','the','is','it','in','on','at','to','for','of','and','or','but',
        'be','was','were','are','have','has','had','do','does','did','will','would',
        'can','could','should','may','might','shall','need','must','with','by','from',
        'this','that','these','those','i','you','he','she','we','they','my','your',
        'his','her','our','their','me','him','us','them','what','how','where','when',
        'who','which','if','then','than','so','as','up','out','no','not','just',
    ];

    /**
     * Search knowledge chunks (code, docs, schema, routes) by query.
     *
     * @param  string  $query
     * @param  string|null  $sourceType  Filter by 'code', 'docs', 'faq', 'schema', 'route'
     * @param  int  $topK
     * @return Collection
     */
    public function searchChunks(string $query, ?string $sourceType = null, int $topK = 5): Collection
    {
        $keywords = $this->tokenize($query);

        if (empty($keywords)) {
            return collect();
        }

        $builder = AiKnowledgeChunk::query();

        if ($sourceType) {
            $builder->where('source_type', $sourceType);
        }

        $chunks = $builder->get();

        return $this->scoreAndRank($chunks, $keywords, $topK);
    }

    /**
     * Search FAQs by query and user role.
     *
     * @param  string  $query
     * @param  string  $role   student|teacher|admin|all
     * @param  int     $topK
     * @return Collection
     */
    public function searchFaqs(string $query, string $role = 'all', int $topK = 3): Collection
    {
        $keywords = $this->tokenize($query);

        $faqs = AiFaq::active()->forRole($role)->get();

        if (empty($keywords) || $faqs->isEmpty()) {
            return $faqs->take($topK);
        }

        return $this->scoreAndRank($faqs, $keywords, $topK, 'question', 'answer');
    }

    /**
     * Score documents by keyword relevance and return top-K.
     */
    protected function scoreAndRank(
        Collection $items,
        array $keywords,
        int $topK,
        string $primaryField  = 'content',
        string $secondaryField = null
    ): Collection {
        $scored = $items->map(function ($item) use ($keywords, $primaryField, $secondaryField) {
            $text  = strtolower($item->{$primaryField} ?? '');
            $text .= ' ' . strtolower($item->keywords ? implode(' ', $item->keywords ?? []) : '');

            if ($secondaryField) {
                $text .= ' ' . strtolower($item->{$secondaryField} ?? '');
            }

            $score = 0;
            foreach ($keywords as $kw) {
                $count  = substr_count($text, $kw);
                $score += $count;

                // Bonus if keyword appears in the beginning (title/heading)
                if (str_contains(substr($text, 0, 200), $kw)) {
                    $score += 2;
                }
            }

            return ['item' => $item, 'score' => $score];
        });

        return $scored
            ->filter(fn($s) => $s['score'] > 0)
            ->sortByDesc('score')
            ->take($topK)
            ->pluck('item')
            ->values();
    }

    /**
     * Tokenize a query string into meaningful keywords.
     */
    public function tokenize(string $text): array
    {
        // Lowercase, split on non-alphanumeric
        $words = preg_split('/[^a-z0-9_]+/', strtolower($text), -1, PREG_SPLIT_NO_EMPTY);

        // Remove stop words and very short words
        $keywords = array_filter($words, fn($w) =>
            strlen($w) > 2 && !in_array($w, $this->stopWords)
        );

        // Also add original query phrases (bigrams) for better matching
        $words    = array_values($keywords);
        $bigrams  = [];
        for ($i = 0; $i < count($words) - 1; $i++) {
            $bigrams[] = $words[$i] . ' ' . $words[$i + 1];
        }

        return array_merge(array_values($keywords), $bigrams);
    }

    /**
     * Format retrieved chunks into a context string for the LLM prompt.
     */
    public function formatChunksAsContext(Collection $chunks): string
    {
        if ($chunks->isEmpty()) {
            return '';
        }

        $context = "=== RELEVANT CODEBASE CONTEXT ===\n\n";

        foreach ($chunks as $chunk) {
            $context .= "📁 File: {$chunk->source_path} (chunk #{$chunk->chunk_index})\n";
            $context .= "```\n{$chunk->content}\n```\n\n";
        }

        return $context;
    }

    /**
     * Format retrieved FAQs into a context string.
     */
    public function formatFaqsAsContext(Collection $faqs): string
    {
        if ($faqs->isEmpty()) {
            return '';
        }

        $context = "=== RELEVANT FAQ KNOWLEDGE ===\n\n";

        foreach ($faqs as $faq) {
            $context .= "Q: {$faq->question}\nA: {$faq->answer}\n\n";
        }

        return $context;
    }
}
