<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Psr\Http\Message\ServerRequestInterface;

class ProductController extends SearchableController
{
    #[\Override]
    function getQuery(): Builder
    {
        return Product::orderBy('code');
    }

    #[\Override]
    function getFilterOptions(): array
    {
        return [
            ...parent::getFilterOptions(),
            'term' => [
                ...parent::getFilterOptions()['term'],
                'category' => static fn (Builder|Relation $query, string $word)
                    => $query->whereHas(
                        'category',
                        static fn (Builder $categoryQuery) => $categoryQuery->where('name', 'LIKE', "%{$word}%"),
                    ),
            ],
        ];
    }

    #[\Override]
    function prepareCriteria(array $criteria): array
    {
        return [
            ...parent::prepareCriteria($criteria),
            'minPrice' => (($criteria['minPrice'] ?? null) === null || $criteria['minPrice'] === '')
                ? null
                : (float) $criteria['minPrice'],
            'maxPrice' => (($criteria['maxPrice'] ?? null) === null || $criteria['maxPrice'] === '')
                ? null
                : (float) $criteria['maxPrice'],
        ];
    }

    function filterByMinPrice(Builder|Relation $query, float $minPrice): void
    {
        $query->where('price', '>=', $minPrice);
    }

    function filterByMaxPrice(Builder|Relation $query, float $maxPrice): void
    {
        $query->where('price', '<=', $maxPrice);
    }

    #[\Override]
    function filter(
        Builder|Relation $query,
        array $criteria,
        array $filterOptions,
    ): void {
        parent::filter($query, $criteria, $filterOptions);

        if ($criteria['minPrice'] !== null) {
            $this->filterByMinPrice($query, $criteria['minPrice']);
        }

        if ($criteria['maxPrice'] !== null) {
            $this->filterByMaxPrice($query, $criteria['maxPrice']);
        }
    }

    
    function filterOutShopByProduct(
        Builder|Relation $shopQuery,
        Product $product,
    ): void {
        $shopQuery->whereDoesntHave(
            'products',
            static function (Builder $productQuery) use ($product): void {
                $productQuery->where('code', $product->code);
            },
        );
    }

    function list(ServerRequestInterface $request): View
    {
        Gate::authorize('list', Product::class);

        $criteria = $this->prepareCriteria($request->getQueryParams());
        $query = $this->search($criteria)->with('category')->withCount('shops');

        return view('products.list', [
            'title' => 'List',
            'criteria' => $criteria,
            'products' => $query->paginate(static::MAX_ITEMS),
        ]);
    }

    function view(string $product): View
    {
        $product = $this->find($product)->load('category');
        Gate::authorize('view', $product);

        return view('products.view', [
            'title' => $product->name,
            'product' => $product,
        ]);
    }

    function viewShops(string $product, ServerRequestInterface $request): View
    {
        $product = $this->find($product);
        Gate::authorize('view', $product);

        $shopController = \resolve(ShopController::class);
        $criteria = $shopController->prepareCriteria($request->getQueryParams());
        $filterOptions = $shopController->getFilterOptions();

        $shopQuery = $product->shops()->withCount('products');
        $shopController->filter($shopQuery, $criteria, $filterOptions);

        return view('products.view-shops', [
            'criteria' => $criteria,
            'product' => $product,
            'shops' => $shopQuery->paginate($shopController::MAX_ITEMS),
        ]);
    }

    function showAddShopsForm(
        string $product,
        ServerRequestInterface $request,
    ): View {
        $product = $this->find($product);
        Gate::authorize('update', $product);

        $shopController = \resolve(ShopController::class);
        $criteria = $shopController->prepareCriteria($request->getQueryParams());

        $shopQuery = $shopController->getQuery()->withCount('products');
        $this->filterOutShopByProduct($shopQuery, $product);
        $shopController->filter($shopQuery, $criteria, $shopController->getFilterOptions());

        return view('products.add-shops-form', [
            'criteria' => $criteria,
            'product' => $product,
            'shops' => $shopQuery->paginate($shopController::MAX_ITEMS),
        ]);
    }

    function addShop(
        string $product,
        ServerRequestInterface $request,
    ): RedirectResponse {
        $product = $this->find($product);
        Gate::authorize('update', $product);

        $shopController = \resolve(ShopController::class);
        $data = $request->getParsedBody();

       
        $shopQuery = $shopController->getQuery();
        $this->filterOutShopByProduct($shopQuery, $product);
        $shop = $shopQuery
            ->where('code', $data['shop'])
            ->firstOrFail();

        try {
            $product->shops()->attach($shop);

            return redirect()
                ->back()
                ->with('status', "Shop {$shop->code} was added to Product {$product->code}.");
        } catch (QueryException $excp) {
            
            return redirect()->back()->withErrors([
                'alert' => $excp->errorInfo[2] ?? $excp->getMessage(),
            ]);
        } catch (\Exception $excp) {
            return redirect()->back()->withErrors([
                'alert' => $excp->getMessage(),
            ]);
        }
    }

    function removeShop(
        string $product,
        ServerRequestInterface $request,
    ): RedirectResponse {
        $product = $this->find($product);
        Gate::authorize('update', $product);

        $data = $request->getParsedBody();

        $shop = $product->shops()->where('code', $data['shop'])->firstOrFail();
        try {
            $product->shops()->detach($shop);

            return redirect()
                ->back()
                ->with('status', "Shop {$shop->code} was removed from Product {$product->code}.");
        } catch (QueryException $excp) {
            
            return redirect()->back()->withErrors([
                'alert' => $excp->errorInfo[2] ?? $excp->getMessage(),
            ]);
        } catch (\Exception $excp) {
            return redirect()->back()->withErrors([
                'alert' => $excp->getMessage(),
            ]);
        }
    }

    function showCreateForm(): View
    {
        Gate::authorize('create', Product::class);

        return view('products.create-form', [
            'title' => 'Create',
            'categories' => Category::orderBy('code')->get(),
        ]);
    }

    function create(ServerRequestInterface $request): RedirectResponse
    {
        Gate::authorize('create', Product::class);

        $data = $request->getParsedBody();
        $category = Category::where('code', $data['category'])->firstOrFail();

        try {
            $product = new Product();
            $product->fill($data);
            $product->category()->associate($category);
            $product->save();

            if (session()->has('bookmarks.products.create')) {
                session()->put(
                    'bookmarks.products.view',
                    session()->get('bookmarks.products.create'),
                );
            }
            session()->forget('bookmarks.products.create');

            return redirect()
                ->route('products.view', [
                    'product' => $product->code,
                ])
                ->with('status', "Product {$product->code} was created.");
        } catch (QueryException $excp) {
            return redirect()->back()->withInput()->withErrors([
                'alert' => $excp->errorInfo[2] ?? $excp->getMessage(),
            ]);
        } catch (\Exception $excp) {
            return redirect()->back()->withInput()->withErrors([
                'alert' => $excp->getMessage(),
            ]);
        }
    }

    function showUpdateForm(string $product): View
    {
        $product = $this->find($product);
        Gate::authorize('update', $product);

        return view('products.update-form', [
            'title' => $product->code,
            'product' => $product,
            'categories' => Category::orderBy('code')->get(),
        ]);
    }

    function update(
        string $product,
        ServerRequestInterface $request,
    ): RedirectResponse {
        $product = $this->find($product);
        Gate::authorize('update', $product);

        $data = $request->getParsedBody();
        $category = Category::where('code', $data['category'])->firstOrFail();

        try {
            $product->fill($data);
            $product->category()->associate($category);
            $product->save();

            if (session()->has('bookmarks.products.update')) {
                session()->put(
                    'bookmarks.products.view',
                    session()->get('bookmarks.products.update'),
                );
            }
            session()->forget('bookmarks.products.update');

            return redirect()
                ->route('products.view', [
                    'product' => $product->code,
                ])
                ->with('status', "Product {$product->code} was updated.");
        } catch (QueryException $excp) {
            return redirect()->back()->withInput()->withErrors([
                'alert' => $excp->errorInfo[2] ?? $excp->getMessage(),
            ]);
        } catch (\Exception $excp) {
            return redirect()->back()->withInput()->withErrors([
                'alert' => $excp->getMessage(),
            ]);
        }
    }

    function delete(string $product): RedirectResponse
    {
        $product = $this->find($product);
        Gate::authorize('delete', $product);

        try {
            $product->delete();

            return redirect(
                session()->get('bookmarks.products.delete') ?? route('products.index'),
            )->with('status', "Product {$product->code} was deleted.");
        } catch (QueryException $excp) {
            
            return redirect()->back()->withErrors([
                'alert' => $excp->errorInfo[2] ?? $excp->getMessage(),
            ]);
        } catch (\Exception $excp) {
            return redirect()->back()->withErrors([
                'alert' => $excp->getMessage(),
            ]);
        }
    }
}
