<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Review\CreateReviewRequest;
use App\Http\Requests\Review\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Http\Controllers\Controller;
use App\Services\ReviewsService;
use App\Models\Reviews;
use Illuminate\Support\Facades\Auth;

class ReviewsController extends Controller
{
  public function __construct(
    private ReviewsService $reviewsService
  ) {
  }

  public function index()
  {
    $items = $this->reviewsService->findAll(userId: Auth::id());
    return ReviewResource::collection($items);
  }

  public function store(CreateReviewRequest $request)
  {
    $review = $this->reviewsService->create(
      $request->validated(),
      Auth::id()
    );
    return new ReviewResource($review);
  }

  public function update(UpdateReviewRequest $request, Reviews $review)
  {
    if ($review->user_id !== Auth::id()) {
      abort(403, 'Unauthorized');
    }

    $updated = $this->reviewsService->update($review, $request->validated());

    return new ReviewResource($updated);
  }

  public function destroy(Reviews $review)
  {
    if ($review->user_id !== Auth::id()) {
      abort(403, 'Unauthorized');
    }

    $this->reviewsService->delete($review);

    return response()->json(['message' => 'Review deleted']);
  }
}