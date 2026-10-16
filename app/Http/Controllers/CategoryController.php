<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Psr\Http\Message\ServerRequestInterface;

class CategoryController extends SearchableController
{
    #[\Override]
    function getQuery(): Builder
    {
        return Category::orderBy('code');
    }

    function filterOutProductInCategory(
        Builder|Relation $productQuery,
        Category $category,
    ): void {
        $productQuery->whereDoesntHave(
            'category',
            static function (Builder $categoryQuery) use ($category): void {
                $categoryQuery->where('code', $category->code);
            },
        );
    }

    function list(ServerRequestInterface $request): View
    {
        Gate::authorize('list', Category::class);

        $criteria = $this->prepareCriteria($request->getQueryParams());
        $query = $this->search($criteria)->withCount('products');

        return view('categories.list', [
            'title' => 'List',
            'criteria' => $criteria,
            'categories' => $query->paginate(static::MAX_ITEMS),
        ]);
    }

    function view(string $category): View
    {
        $category = $this->find($category);
        Gate::authorize('view', $category);

        return view('categories.view', [
            'title' => $category->name,
            'category' => $category,
        ]);
    }

    function viewProducts(string $category, ServerRequestInterface $request): View
    {
        $category = $this->find($category);
        Gate::authorize('view', $category);

        $productController = \resolve(ProductController::class);
        $criteria = $productController->prepareCriteria($request->getQueryParams());
        
        $filterOptions = $productController->getFilterOptions();
        unset($filterOptions['term']['category']);

        $productQuery = $category->products()->withCount('shops');
        $productController->filter($productQuery, $criteria, $filterOptions);

        return view('categories.view-products', [
            'criteria' => $criteria,
            'category' => $category,
            'products' => $productQuery->paginate($productController::MAX_ITEMS),
        ]);
    }

    function showAddProductsForm(
        string $category,
        ServerRequestInterface $request,
    ): View {
        $category = $this->find($category);
        Gate::authorize('update', $category);

        $productController = \resolve(ProductController::class);
        $criteria = $productController->prepareCriteria($request->getQueryParams());

        $productQuery = $productController->getQuery()->with('category')->withCount('shops');
        $this->filterOutProductInCategory($productQuery, $category);
        $productController->filter($productQuery, $criteria, $productController->getFilterOptions());

        return view('categories.add-products-form', [
            'criteria' => $criteria,
            'category' => $category,
            'products' => $productQuery->paginate($productController::MAX_ITEMS),
        ]);
    }

    function addProduct(
        string $category,
        ServerRequestInterface $request,
    ): RedirectResponse {
        $category = $this->find($category);
        Gate::authorize('update', $category);

        $productController = \resolve(ProductController::class);
        $data = $request->getParsedBody();

     
        $productQuery = $productController->getQuery();
        $this->filterOutProductInCategory($productQuery, $category);
        $product = $productQuery
            ->where('code', $data['product'])
            ->firstOrFail();

        try {
            $product->category()->associate($category);
            $product->save();

            return redirect()
                ->back()
                ->with('status', "Product {$product->code} was added to Category {$category->code}.");
        } catch (QueryException $excp) {
            // We don't want withInput() here.
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
        Gate::authorize('create', Category::class);

        return view('categories.create-form', [
            'title' => 'Create',
        ]);
    }

    function create(ServerRequestInterface $request): RedirectResponse
    {
        Gate::authorize('create', Category::class);

        try {
            $category = Category::create($request->getParsedBody());

            if (session()->has('bookmarks.categories.create')) {
                session()->put(
                    'bookmarks.categories.view',
                    session()->get('bookmarks.categories.create'),
                );
            }
            session()->forget('bookmarks.categories.create');

            return redirect()
                ->route('categories.view', [
                    'category' => $category->code,
                ])
                ->with('status', "Category {$category->code} was created.");
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

    function showUpdateForm(string $category): View
    {
        $category = $this->find($category);
        Gate::authorize('update', $category);

        return view('categories.update-form', [
            'title' => $category->code,
            'category' => $category,
        ]);
    }

    function update(
        string $category,
        ServerRequestInterface $request,
    ): RedirectResponse {
        $category = $this->find($category);
        Gate::authorize('update', $category);

        try {
            $category->fill($request->getParsedBody());
            $category->save();

            if (session()->has('bookmarks.categories.update')) {
                session()->put(
                    'bookmarks.categories.view',
                    session()->get('bookmarks.categories.update'),
                );
            }
            session()->forget('bookmarks.categories.update');

            return redirect()
                ->route('categories.view', [
                    'category' => $category->code,
                ])
                ->with('status', "Category {$category->code} was updated.");
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

    function delete(string $category): RedirectResponse
    {
        $category = $this->find($category);
        Gate::authorize('delete', $category);

        try {
            $category->delete();

            return redirect(
                session()->get('bookmarks.categories.delete') ?? route('categories.index'),
            )->with('status', "Category {$category->code} was deleted.");
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
