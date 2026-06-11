<?php

namespace App\Support;

class VectorMath
{
    /**
     * Compute the cosine similarity between two vectors.
     *
     * Returns a value in [-1, 1] where 1 is identical direction. Returns 0.0 for
     * mismatched lengths or a zero-magnitude vector rather than throwing, so callers
     * can rank defensively over potentially dirty data.
     *
     * @param  list<float>  $a
     * @param  list<float>  $b
     */
    public static function cosineSimilarity(array $a, array $b): float
    {
        if ($a === [] || count($a) !== count($b)) {
            return 0.0;
        }

        $dot = 0.0;
        $magnitudeA = 0.0;
        $magnitudeB = 0.0;

        foreach ($a as $i => $valueA) {
            $valueB = $b[$i];
            $dot += $valueA * $valueB;
            $magnitudeA += $valueA * $valueA;
            $magnitudeB += $valueB * $valueB;
        }

        if ($magnitudeA <= 0.0 || $magnitudeB <= 0.0) {
            return 0.0;
        }

        return $dot / (sqrt($magnitudeA) * sqrt($magnitudeB));
    }
}
