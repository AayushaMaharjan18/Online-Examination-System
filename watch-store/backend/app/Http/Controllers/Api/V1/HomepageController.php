<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Blog;
use App\Models\HeroSlider;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class HomepageController extends Controller
{
    public function index(): JsonResponse
    {
        $heroSliders = HeroSlider::where('is_active', true)->orderBy('sort_order')->get();
        
        $featuredProducts = Product::with('brand', 'categories')
            ->where('status', 'active')->where('is_featured', true)
            ->orderBy('created_at', 'desc')->take(8)->get();
        
        $newArrivals = Product::with('brand', 'categories')
            ->where('status', 'active')->where('is_new', true)
            ->orderBy('created_at', 'desc')->take(8)->get();
        
        $bestSellers = Product::with('brand', 'categories')
            ->where('status', 'active')->where('is_best_seller', true)
            ->orderBy('reviews_count', 'desc')->take(8)->get();
        
        $limitedEdition = Product::with('brand', 'categories')
            ->where('status', 'active')->where('is_limited_edition', true)
            ->orderBy('created_at', 'desc')->take(8)->get();
        
        $brands = Brand::withCount('products')
            ->where('is_featured', true)->orderBy('name')->get();
        
        $latestBlogs = Blog::where('status', 'published')
            ->orderBy('published_at', 'desc')->take(3)->get();

        return response()->json([
            'hero_sliders' => $heroSliders->map(fn($s) => [
                'id' => $s->id,
                'title' => $s->title,
                'subtitle' => $s->subtitle,
                'description' => $s->description,
                'button_text' => $s->button_text,
                'button_url' => $s->button_url,
                'image' => $s->image,
            ]),
            'featured_products' => ProductResource::collection($featuredProducts),
            'new_arrivals' => ProductResource::collection($newArrivals),
            'best_sellers' => ProductResource::collection($bestSellers),
            'limited_edition' => ProductResource::collection($limitedEdition),
            'brands' => BrandResource::collection($brands),
            'latest_blogs' => $latestBlogs->map(fn($b) => [
                'id' => $b->id,
                'title' => $b->title,
                'slug' => $b->slug,
                'excerpt' => $b->excerpt,
                'featured_image' => $b->featured_image,
                'author' => $b->author,
                'published_at' => $b->published_at?->format('Y-m-d'),
            ]),
            'offers' => [],
        ]);
    }
}