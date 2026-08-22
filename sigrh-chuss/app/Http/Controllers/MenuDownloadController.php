<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MenuDownloadController extends Controller
{
    public function download(Menu $menu)
    {
        $menu->load([
            'menuJours.repas.plat',
            'menuJours.repas.sauce',
            'menuJours.repas.viande',
            'menuJours.repas.dessert',
        ]);

        $pdf = Pdf::loadView('menus.pdf', compact('menu'));
        return $pdf->download("Menu_Semaine_{$menu->numero_semaine}_{$menu->annee}.pdf");
    }
}
