<?php

namespace Tests\Feature\Support;

use App\Support\VectorMath;
use Tests\TestCase;

class VectorMathTest extends TestCase
{
    public function test_identical_vectors_have_similarity_of_one(): void
    {
        $this->assertEqualsWithDelta(1.0, VectorMath::cosineSimilarity([1.0, 2.0, 3.0], [1.0, 2.0, 3.0]), 1e-9);
    }

    public function test_orthogonal_vectors_have_zero_similarity(): void
    {
        $this->assertEqualsWithDelta(0.0, VectorMath::cosineSimilarity([1.0, 0.0], [0.0, 1.0]), 1e-9);
    }

    public function test_opposite_vectors_have_similarity_of_minus_one(): void
    {
        $this->assertEqualsWithDelta(-1.0, VectorMath::cosineSimilarity([1.0, 1.0], [-1.0, -1.0]), 1e-9);
    }

    public function test_mismatched_lengths_return_zero(): void
    {
        $this->assertSame(0.0, VectorMath::cosineSimilarity([1.0, 2.0], [1.0]));
    }

    public function test_zero_magnitude_vector_returns_zero(): void
    {
        $this->assertSame(0.0, VectorMath::cosineSimilarity([0.0, 0.0], [1.0, 1.0]));
    }

    public function test_empty_vectors_return_zero(): void
    {
        $this->assertSame(0.0, VectorMath::cosineSimilarity([], []));
    }
}
