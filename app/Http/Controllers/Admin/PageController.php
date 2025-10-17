<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Page\PageIndexRequest;
use App\Http\Requests\Admin\Page\PageCreateRequest;
use App\Http\Requests\Admin\Page\PageUpdateRequest;
use App\Models\Page;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PagesImport;
use App\Exports\PagesExport;
use Illuminate\Http\Request;

class PageController extends Controller
{
    // Define attribute once
    protected string $attribute = 'Page';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.pages.index');
    }

    /**
     * Get paginated items for Alpine.js or API
     */
    public function getItems(PageIndexRequest $request)
    {
        return $this->runSafely(function () use ($request) {
            $perPage = $request->input('per_page', 10);
            $search = $request->input('search');

            $query = Page::query()->orderBy('created_at', 'desc');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            }

            $pages = $query->paginate($perPage);

            return $this->paginateResponse($pages, "{$this->attribute}s fetched successfully.");
        }, messages('server_error', $this->attribute));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PageCreateRequest $request)
    {
        return $this->runSafely(function () use ($request) {
            $data = $request->validated();
            $data['created_by'] = auth()->id();

            $page = Page::create($data);

            return $this->successResponse([
                'page' => $page,
                'redirect' => route('admin.pages.index'),
            ], messages('crud.created', $this->attribute));
        }, messages('errors.failed_create', $this->attribute), [
            'data' => $request->all(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Page $page)
    {
        return view('admin.pages.show', compact('page'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PageUpdateRequest $request, Page $page)
    {
        return $this->runSafely(function () use ($request, $page) {
            $data = $request->validated();
            $data['updated_by'] = auth()->id();

            $page->update($data);

            return $this->successResponse([
                'page' => $page->fresh(),
                'redirect' => route('admin.pages.index'),
            ], messages('crud.updated', $this->attribute));
        }, messages('errors.failed_update', $this->attribute), [
            'page_id' => $page->id,
            'data' => $request->all(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page)
    {
        return $this->runSafely(function () use ($page) {
            $page->delete(); // Soft delete

            return $this->successResponse([
                'redirect' => route('admin.pages.index')
            ], messages('crud.deleted', $this->attribute));
        }, messages('errors.failed_delete', $this->attribute), [
            'page_id' => $page->id
        ]);
    }

    /**
     * Export pages to Excel.
     */
    public function export()
    {
        return $this->runSafely(function () {
            $fileName = 'pages_export_' . now()->format('Ymd_His') . '.xlsx';
            return Excel::download(new PagesExport, $fileName);
        }, messages('errors.failed_export', $this->attribute));
    }

    /**
     * Import pages from Excel/CSV.
     */
    public function import(Request $request)
    {
        return $this->runSafely(function () use ($request) {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,csv',
            ]);

            Excel::import(new PagesImport, $request->file('file'));

            return $this->successResponse([], messages('crud.imported', $this->attribute));
        }, messages('errors.failed_import', $this->attribute));
    }

    /**
     * Perform bulk status update or delete.
     */
    public function bulkAction(Request $request)
    {
        return $this->runSafely(function () use ($request) {
            $request->validate([
                'ids' => 'required|array',
                'action' => 'required|in:active,draft,archived,delete,delete_permanently',
            ]);

            $pages = Page::withTrashed()->whereIn('id', $request->ids);

            if ($request->action === 'delete') {
                $pages->delete(); // Soft delete
            } elseif ($request->action === 'delete_permanently') {
                $pages->forceDelete(); // Permanent delete
            } else {
                $pages->update(['status' => $request->action, 'updated_by' => auth()->id()]);
            }

            return $this->successResponse([], messages('crud.bulk_action', $this->attribute));
        }, messages('errors.failed_bulk_action', $this->attribute));
    }

    /**
     * Restore soft-deleted pages.
     */
    public function restore(Request $request)
    {
        return $this->runSafely(function () use ($request) {
            $request->validate([
                'ids' => 'required|array',
            ]);

            $pages = Page::onlyTrashed()->whereIn('id', $request->ids);
            $pages->restore();

            return $this->successResponse([], messages('crud.restored', $this->attribute));
        }, messages('errors.failed_restore', $this->attribute));
    }
}
