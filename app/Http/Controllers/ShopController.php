<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Psr\Http\Message\ServerRequestInterface;

class ShopController extends SearchableController
{
    #[\Override]
    function getQuery(): Builder
    {
        return Shop::orderBy('code');
    }

    #[\Override]
    function getFilterOptions(): array
    {
        return [
            'term' => [
                'code' => static fn (Builder $query, string $word)
                    => $query->where('code', 'LIKE', "%{$word}%"),
                'name' => static fn (Builder $query, string $word)
                    => $query->where('name', 'LIKE', "%{$word}%"),
                'owner' => static fn (Builder $query, string $word)
                    => $query->where('owner', 'LIKE', "%{$word}%"),
            ],
        ];
    }

    
    function filterOutProductByShop(
        Builder|Relation $productQuery,
        Shop $shop,
    ): void {
        $productQuery->whereDoesntHave(
            'shops',
            static function (Builder $shopQuery) use ($shop): void {
                $shopQuery->where('code', $shop->code);
            },
        );
    }

    function list(ServerRequestInterface $request): View
    {
        Gate::authorize('list', Shop::class);

        $criteria = $this->prepareCriteria($request->getQueryParams());
        $query = $this->search($criteria)->withCount('products');

        return view('shops.list', [
            'title' => 'List',
            'criteria' => $criteria,
            'shops' => $query->paginate(static::MAX_ITEMS),
        ]);
    }

    function view(string $shop): View
    {
        $shop = $this->find($shop);
        Gate::authorize('view', $shop);

        return view('shops.view', [
            'title' => $shop->name,
            'shop' => $shop,
        ]);
    }

    function viewProducts(string $shop, ServerRequestInterface $request): View
    {
        $shop = $this->find($shop);
        Gate::authorize('view', $shop);

        $productController = \resolve(ProductController::class);
        $criteria = $productController->prepareCriteria($request->getQueryParams());
        $filterOptions = $productController->getFilterOptions();

        $productQuery = $shop->products()->with('category')->withCount('shops');
        $productController->filter($productQuery, $criteria, $filterOptions);

        return view('shops.view-products', [
            'criteria' => $criteria,
            'shop' => $shop,
            'products' => $productQuery->paginate($productController::MAX_ITEMS),
        ]);
    }

    function showAddProductsForm(
        string $shop,
        ServerRequestInterface $request,
    ): View {
        $shop = $this->find($shop);
        Gate::authorize('update', $shop);

        $productController = \resolve(ProductController::class);
        $criteria = $productController->prepareCriteria($request->getQueryParams());

        $productQuery = $productController->getQuery()->with('category')->withCount('shops');
        $this->filterOutProductByShop($productQuery, $shop);
        $productController->filter($productQuery, $criteria, $productController->getFilterOptions());

        return view('shops.add-products-form', [
            'criteria' => $criteria,
            'shop' => $shop,
            'products' => $productQuery->paginate($productController::MAX_ITEMS),
        ]);
    }

    function addProduct(
        string $shop,
        ServerRequestInterface $request,
    ): RedirectResponse {
        $shop = $this->find($shop);
        Gate::authorize('update', $shop);

        $productController = \resolve(ProductController::class);
        $data = $request->getParsedBody();

   
        $productQuery = $productController->getQuery();
        $this->filterOutProductByShop($productQuery, $shop);
        $product = $productQuery
            ->where('code', $data['product'])
            ->firstOrFail();

        try {
            $shop->products()->attach($product);

            return redirect()
                ->back()
                ->with('status', "Product {$product->code} was added to Shop {$shop->code}.");
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

    function removeProduct(
        string $shop,
        ServerRequestInterface $request,
    ): RedirectResponse {
        $shop = $this->find($shop);
        Gate::authorize('update', $shop);

        $data = $request->getParsedBody();

        $product = $shop->products()->where('code', $data['product'])->firstOrFail();
        try {
            $shop->products()->detach($product);

            return redirect()
                ->back()
                ->with('status', "Product {$product->code} was removed from Shop {$shop->code}.");
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
        Gate::authorize('create', Shop::class);

        return view('shops.create-form', [
            'title' => 'Create',
        ]);
    }

    function create(ServerRequestInterface $request): RedirectResponse
    {
        Gate::authorize('create', Shop::class);

        try {
            $shop = Shop::create($request->getParsedBody());

            if (session()->has('bookmarks.shops.create')) {
                session()->put(
                    'bookmarks.shops.view',
                    session()->get('bookmarks.shops.create'),
                );
            }
            session()->forget('bookmarks.shops.create');

            return redirect()
                ->route('shops.view', [
                    'shop' => $shop->code,
                ])
                ->with('status', "Shop {$shop->code} was created.");
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

    function showUpdateForm(string $shop): View
    {
        $shop = $this->find($shop);
        Gate::authorize('update', $shop);

        return view('shops.update-form', [
            'title' => $shop->code,
            'shop' => $shop,
        ]);
    }

    function update(
        string $shop,
        ServerRequestInterface $request,
    ): RedirectResponse {
        $shop = $this->find($shop);
        Gate::authorize('update', $shop);

        try {
            $shop->fill($request->getParsedBody());
            $shop->save();

            if (session()->has('bookmarks.shops.update')) {
                session()->put(
                    'bookmarks.shops.view',
                    session()->get('bookmarks.shops.update'),
                );
            }
            session()->forget('bookmarks.shops.update');

            return redirect()
                ->route('shops.view', [
                    'shop' => $shop->code,
                ])
                ->with('status', "Shop {$shop->code} was updated.");
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

    function delete(string $shop): RedirectResponse
    {
        $shop = $this->find($shop);
        Gate::authorize('delete', $shop);

        try {
            $shop->delete();

            return redirect(
                session()->get('bookmarks.shops.delete') ?? route('shops.index'),
            )->with('status', "Shop {$shop->code} was deleted.");
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
