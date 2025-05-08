<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    protected $item = 'Menu Management';
    protected $itemActive = 'Menus';

    /**
     * Constructor to apply permissions middleware
     */
    public function __construct()
    {
        $this->middleware('permission:Menus List', ['only' => ['index', 'data']]);
        $this->middleware('permission:Menus Create', ['only' => ['create', 'store']]);
        $this->middleware('permission:Menus Update', ['only' => ['edit', 'update']]);
        $this->middleware('permission:Menus Delete', ['only' => ['destroy']]);
        $this->middleware('permission:Menus View', ['only' => ['show']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Log activity
        addActivity('menu', 'view', 'User viewed menus list', null);

        $menus = Menu::all();
        return view('backend.menus.index', compact('menus'))
            ->with('item', $this->item)
            ->with('itemActive', $this->itemActive);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Log activity
        addActivity('menu', 'view_create_form', 'User viewed create menu form', null);

        return view('backend.menus.create')
            ->with('item', $this->item)
            ->with('itemActive', $this->itemActive);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $menu = Menu::create($request->all());

            // Log activity
            addActivity('menu', 'create', 'User created menu: ' . $menu->name, $menu->id);

            return response()->json(['status' => 'success', 'message' => 'Menu created successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $menu = Menu::findOrFail($id);

            // Log activity
            addActivity('menu', 'view', 'User viewed menu: ' . $menu->name, $menu->id);

            return response()->json([
                'status' => 'success',
                'data' => $menu
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $menu = Menu::findOrFail($id);

            // Log activity
            addActivity('menu', 'edit', 'User viewed edit form for menu: ' . $menu->name, $menu->id);

            return response()->json([
                'status' => 'success',
                'data' => $menu
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $menu = Menu::findOrFail($id);
            $oldName = $menu->name;

            $menu->update($request->all());

            // Log activity
            addActivity('menu', 'update', 'User updated menu from "' . $oldName . '" to "' . $menu->name . '"', $menu->id);

            return response()->json(['status' => 'success', 'message' => 'Menu updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $menu = Menu::findOrFail($id);
            $menuName = $menu->name;
            $menuId = $menu->id;

            $menu->delete();

            // Log activity
            addActivity('menu', 'delete', 'User deleted menu: ' . $menuName, $menuId);

            return response()->json([
                'status' => 'success',
                'message' => 'Menu deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get menus data for DataTables.
     */
    public function data(Request $request)
    {
        try {
            $menus = Menu::all();
            $data = [];

            foreach ($menus as $menu) {
                $data[] = [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'url' => $menu->url,
                    'icon' => $menu->icon,
                    'created_at' => $menu->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $menu->updated_at->format('Y-m-d H:i:s')
                ];
            }

            return response()->json([
                'draw' => $request->input('draw', 1),
                'recordsTotal' => count($data),
                'recordsFiltered' => count($data),
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error fetching menus: ' . $e->getMessage()
            ], 500);
        }
    }
}
