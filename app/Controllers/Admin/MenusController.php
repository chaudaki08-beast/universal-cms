<?php
namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Session;
use App\Core\Database;
use App\Models\Menu;
use App\Models\Page;

class MenusController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        Auth::requireAbility('menus.manage');
    }

    public function index(): void
    {
        $menus = Menu::all('id');
        $current = Request::str('menu', 'primary');
        $menu  = Menu::bySlug($current) ?: ($menus[0] ?? null);
        $items = $menu ? Menu::items((int) $menu['id']) : [];
        $pages = Page::published();
        $this->adminView('admin/menus/index', compact('menus', 'menu', 'items', 'pages'), 'Menus');
    }

    public function addItem(): void
    {
        $menuId = Request::int('menu_id');
        $max = (int) Database::scalar("SELECT COALESCE(MAX(sort_order),-1) FROM menu_items WHERE menu_id=?", [$menuId]);
        Database::insert('menu_items', [
            'menu_id'   => $menuId,
            'parent_id' => Request::int('parent_id') ?: null,
            'label'     => Request::str('label', 'Link'),
            'url'       => Request::str('url', '#'),
            'target'    => Request::str('target', '_self'),
            'icon'      => Request::str('icon'),
            'sort_order'=> $max + 1,
        ]);
        Session::flash('success', 'Menu item added.');
        $this->back();
    }

    public function deleteItem(string $id): void
    {
        Database::delete('menu_items', ['id' => (int) $id]);
        Session::flash('success', 'Menu item removed.');
        $this->back();
    }

    public function reorder(): void
    {
        foreach (Request::array('order') as $i => $itemId) {
            Database::run("UPDATE menu_items SET sort_order=? WHERE id=?", [(int) $i, (int) $itemId]);
        }
        $this->json(['ok' => true]);
    }
}
