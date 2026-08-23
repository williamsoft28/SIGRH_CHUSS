<?php

namespace App\Http\Controllers;

use App\Models\Beneficiaire;
use App\Models\DeclarationJour;
use App\Models\RegimeSpecial;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminBeneficiaireController extends Controller
{
    /**
     * Liste de tous les bénéficiaires, tous services confondus, filtrable par service.
     */
    public function index(Request $request): View
    {
        $beneficiaires = Beneficiaire::query()
            ->with(['service', 'regimeSpecial'])
            ->when($request->filled('service_id'), fn ($q) => $q->where('service_id', $request->integer('service_id')))
            ->orderBy('nom')
            ->paginate(20)
            ->withQueryString();

        $services = Service::orderBy('nom')->get();

        return view('admin.beneficiaires.index', compact('beneficiaires', 'services'));
    }

    /**
     * Bénéficiaires déclarés pour une date donnée, tous services confondus.
     */
    public function duJour(Request $request): View
    {
        $date = $request->date('date') ?? today();

        $declarations = DeclarationJour::query()
            ->where(function ($query) use ($date) {
                $query->where(function ($q) use ($date) {
                    $q->where('type_periode', 'quotidien')
                      ->whereDate('date_repas', $date->toDateString());
                })
                ->orWhere(function ($q) use ($date) {
                    $q->whereIn('type_periode', ['hebdomadaire', 'mensuel'])
                      ->whereHas('bonRepas.droits', function ($q2) use ($date) {
                          $q2->whereDate('date', $date->toDateString());
                      });
                })
                ->orWhere(function ($q) use ($date) {
                    $q->whereIn('type_periode', ['hebdomadaire', 'mensuel'])
                      ->whereDoesntHave('bonRepas')
                      ->whereDate('date_debut', '<=', $date->toDateString())
                      ->whereDate('date_fin', '>=', $date->toDateString());
                });
            })
            ->with(['beneficiaire.service', 'bonRepas.droits' => function($q) use ($date) {
                $q->whereDate('date', $date->toDateString());
            }])
            ->orderBy('statut')
            ->get();

        // Adjust meals for display if rights exist for the day
        foreach ($declarations as $declaration) {
            if ($declaration->bonRepas && $declaration->bonRepas->droits->isNotEmpty()) {
                $declaration->repas_jour = $declaration->bonRepas->droits->pluck('type_repas')->unique()->toArray();
            } else {
                $declaration->repas_jour = $declaration->repas;
            }
        }

        return view('admin.beneficiaires.jour', compact('declarations', 'date'));
    }

    public function create(): View
    {
        $services = Service::orderBy('nom')->get();
        $regimesSpeciaux = RegimeSpecial::orderBy('libelle')->get();

        return view('admin.beneficiaires.create', compact('services', 'regimesSpeciaux'));
    }

    public function store(Request $request): RedirectResponse
    {
        Beneficiaire::create($this->validated($request));

        return redirect()
            ->route('admin.beneficiaires.index')
            ->with('status', 'Bénéficiaire ajouté avec succès.');
    }

    public function edit(Beneficiaire $beneficiaire): View
    {
        $services = Service::orderBy('nom')->get();
        $regimesSpeciaux = RegimeSpecial::orderBy('libelle')->get();

        return view('admin.beneficiaires.edit', compact('beneficiaire', 'services', 'regimesSpeciaux'));
    }

    public function update(Request $request, Beneficiaire $beneficiaire): RedirectResponse
    {
        $beneficiaire->update($this->validated($request));

        return redirect()
            ->route('admin.beneficiaires.index')
            ->with('status', 'Bénéficiaire mis à jour avec succès.');
    }

    public function destroy(Beneficiaire $beneficiaire): RedirectResponse
    {
        $beneficiaire->delete();

        return redirect()
            ->route('admin.beneficiaires.index')
            ->with('status', 'Bénéficiaire supprimé.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'nom' => ['required', 'string', 'max:255'],
            'categorie' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:regulier,variable'],
            'numero_whatsapp' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'regime_special_id' => ['nullable', 'exists:regime_specials,id'],
        ]);
    }
}
